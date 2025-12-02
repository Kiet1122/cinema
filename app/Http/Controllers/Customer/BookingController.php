<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Seat;
use App\Models\Voucher;
use App\Models\CustomerVoucher;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Hiển thị trang đặt vé
     */
    public function create($showtime_id)
    {
        $showtime = Showtime::with([
            'movie',
            'room.theater',
            'room.seats' => function ($query) {
                $query->orderBy('SeatNumber');
            }
        ])->findOrFail($showtime_id);

        // Lấy các ghế đã được đặt
        $bookedSeats = BookingDetail::whereHas('booking', function ($query) use ($showtime_id) {
            $query->where('ShowtimeID', $showtime_id)
                ->whereIn('Status', ['Created', 'Confirmed']);
        })->pluck('SeatID')->toArray();

        // Lấy voucher khả dụng của khách hàng
        $availableVouchers = [];
        if (Auth::check()) {
            $customer = Auth::user()->customer;
            if ($customer) {
                $availableVouchers = CustomerVoucher::with('voucher')
                    ->where('CustomerID', $customer->CustomerID)
                    ->where('IsUsed', false)
                    ->whereHas('voucher', function ($query) {
                        $query->where('Status', 'Active')
                            ->where('StartDate', '<=', now())
                            ->where('EndDate', '>=', now())
                            ->where(function ($query) {
                                $query->whereNull('UsageLimit')
                                    ->orWhereRaw('UsedCount < UsageLimit');
                            });
                    })
                    ->get();
            }
        }

        return view('customer.booking.create', compact(
            'showtime',
            'bookedSeats',
            'availableVouchers'
        ));
    }

    /**
     * Xử lý đặt vé
     */
    public function store(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtime,ShowtimeID',
            'seats' => 'required|json',
            'voucher_id' => 'nullable|exists:voucher,VoucherID',
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,momo,zalopay'
        ]);

        try {
            DB::beginTransaction();

            $showtime = Showtime::findOrFail($request->showtime_id);
            $customer = Auth::user()->customer;

            // Parse JSON seats
            $requestedSeats = json_decode($request->seats, true);
            if (empty($requestedSeats)) {
                return back()->withErrors([
                    'seats' => 'Vui lòng chọn ít nhất một ghế.'
                ])->withInput();
            }

            // Kiểm tra ghế có còn trống không
            $bookedSeats = BookingDetail::whereHas('booking', function ($query) use ($request) {
                $query->where('ShowtimeID', $request->showtime_id)
                    ->whereIn('Status', ['Created', 'Confirmed']);
            })->pluck('SeatID')->toArray();

            $conflictedSeats = array_intersect($bookedSeats, $requestedSeats);
            if (!empty($conflictedSeats)) {
                return back()->withErrors([
                    'seats' => 'Một số ghế đã được đặt. Vui lòng chọn ghế khác.'
                ])->withInput();
            }

            // FIXED: Tính tổng tiền CHÍNH XÁC (giá vé phim + phí ghế)
            $seats = Seat::whereIn('SeatID', $requestedSeats)->get();
            $numberOfTickets = count($requestedSeats);

            // Giá vé phim cơ bản
            $baseTicketPrice = $showtime->Price * $numberOfTickets;

            // Tổng phí ghế đặc biệt
            $seatFees = $seats->sum('Price');

            // Tổng trước giảm giá
            $subtotal = $baseTicketPrice + $seatFees;

            // Áp dụng voucher nếu có
            $voucher = null;
            $discountAmount = 0;
            $voucherId = null;

            if ($request->voucher_id) {
                $voucher = Voucher::where('VoucherID', $request->voucher_id)
                    ->where('Status', 'Active')
                    ->where('StartDate', '<=', now())
                    ->where('EndDate', '>=', now())
                    ->where(function ($query) {
                        $query->whereNull('UsageLimit')
                            ->orWhereRaw('UsedCount < UsageLimit');
                    })
                    ->first();

                if ($voucher) {
                    $customerVoucher = CustomerVoucher::where([
                        'CustomerID' => $customer->CustomerID,
                        'VoucherID' => $voucher->VoucherID,
                        'IsUsed' => false
                    ])->first();

                    if ($customerVoucher) {
                        // Tính discount dựa trên subtotal (đã bao gồm cả giá vé và phí ghế)
                        if ($voucher->DiscountType === 'Percent') {
                            $discountAmount = ($subtotal * $voucher->Value) / 100;
                        } else {
                            $discountAmount = $voucher->Value;
                        }

                        $discountAmount = min($discountAmount, $subtotal);
                        $voucherId = $voucher->VoucherID;
                    }
                }
            }

            $finalAmount = $subtotal - $discountAmount;

            // Log để debug
            \Log::info('Booking Calculation:', [
                'showtime_price' => $showtime->Price,
                'number_of_tickets' => $numberOfTickets,
                'base_ticket_price' => $baseTicketPrice,
                'seat_fees' => $seatFees,
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'final_amount' => $finalAmount
            ]);

            // Tạo booking
            $booking = Booking::create([
                'CustomerID' => $customer->CustomerID,
                'ShowtimeID' => $request->showtime_id,
                'TotalAmount' => $finalAmount, // Lưu tổng tiền đã tính đúng
                'Status' => 'Created',
                'PaymentStatus' => 'Pending',
                'VoucherID' => $voucherId,
            ]);

            // Tạo booking details
            foreach ($requestedSeats as $seatId) {
                $seat = $seats->firstWhere('SeatID', $seatId);
                BookingDetail::create([
                    'BookingID' => $booking->BookingID,
                    'SeatID' => $seatId,
                    'Price' => $seat->Price,
                ]);
            }

            // Tạo payment
            Payment::create([
                'BookingID' => $booking->BookingID,
                'PaymentMethod' => $request->payment_method,
                'Amount' => $finalAmount,
                'Status' => 'Pending',
            ]);

            // Đánh dấu voucher đã sử dụng nếu có
            if ($voucher && isset($customerVoucher)) {
                $customerVoucher->update([
                    'IsUsed' => true,
                    'UsedAt' => now(),
                ]);

                $voucher->increment('UsedCount');
            }

            DB::commit();

            // Chuyển hướng đến trang thanh toán
            return redirect()->route('customer.booking.payment', ['id' => $booking->BookingID])
                ->with('success', 'Đặt vé thành công! Vui lòng thanh toán.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Hiển thị trang thanh toán
     */
    public function payment($booking_id)
    {
        $booking = Booking::with([
            'showtime.movie',
            'showtime.room.theater',
            'bookingDetails.seat',
            'voucher',
            'payment'
        ])->where('CustomerID', Auth::user()->customer->CustomerID)
            ->findOrFail($booking_id);

        if ($booking->PaymentStatus === 'Paid') {
            return redirect()->route('customer.booking.success', ['id' => $booking_id]);
        }

        return view('customer.booking.payment', compact('booking'));
    }

    /**
     * Xử lý thanh toán
     */
    public function processPayment(Request $request, $booking_id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,credit_card,bank_transfer,momo,zalopay'
        ]);

        try {
            DB::beginTransaction();

            $booking = Booking::where('CustomerID', Auth::user()->customer->CustomerID)
                ->findOrFail($booking_id);

            // Kiểm tra nếu đã thanh toán
            if ($booking->PaymentStatus === 'Paid') {
                return redirect()->route('customer.booking.success', ['id' => $booking_id]);
            }

            // Xử lý thanh toán
            $isPaymentSuccessful = $this->processPaymentGateway($request->payment_method);

            if ($isPaymentSuccessful) {
                // Cập nhật booking
                $booking->update([
                    'Status' => 'Confirmed',
                    'PaymentStatus' => 'Paid',
                ]);

                // Cập nhật payment
                $booking->payment->update([
                    'Status' => 'Paid',
                    'PaymentDate' => now(),
                    'PaymentMethod' => $request->payment_method,
                ]);

                DB::commit();

                return redirect()->route('customer.booking.success', ['id' => $booking_id])
                    ->with('success', 'Thanh toán thành công!');
            } else {
                DB::rollBack();
                return back()->withErrors([
                    'payment' => 'Thanh toán thất bại. Vui lòng thử lại.'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Trang thành công
     */
    public function success($booking_id)
    {
        $booking = Booking::with([
            'showtime.movie',
            'showtime.room.theater',
            'bookingDetails.seat',
            'voucher',
            'payment'
        ])->where('CustomerID', Auth::user()->customer->CustomerID)
            ->findOrFail($booking_id);

        return view('customer.booking.success', compact('booking'));
    }

    /**
     * Lịch sử đặt vé
     */
    public function history()
    {
        $customer = Auth::user()->customer;
        $bookings = Booking::with([
            'showtime.movie',
            'showtime.room.theater',
            'bookingDetails.seat',
            'payment'
        ])->where('CustomerID', $customer->CustomerID)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.booking.history', compact('bookings'));
    }

    /**
     * Chi tiết đặt vé
     */
    public function show($booking_id)
    {
        $booking = Booking::with([
            'showtime.movie',
            'showtime.room.theater',
            'bookingDetails.seat',
            'voucher',
            'payment'
        ])->where('CustomerID', Auth::user()->customer->CustomerID)
            ->findOrFail($booking_id);

        return view('customer.booking.show', compact('booking'));
    }

    /**
     * Hủy đặt vé
     */
    public function cancel($booking_id)
    {
        try {
            DB::beginTransaction();

            $booking = Booking::where('CustomerID', Auth::user()->customer->CustomerID)
                ->findOrFail($booking_id);

            // Chỉ cho phép hủy booking chưa được xác nhận hoặc chưa thanh toán
            if (!in_array($booking->Status, ['Created']) || $booking->PaymentStatus !== 'Pending') {
                return back()->withErrors([
                    'booking' => 'Không thể hủy đặt vé này.'
                ]);
            }

            // Hoàn lại voucher nếu có
            if ($booking->VoucherID) {
                $customerVoucher = CustomerVoucher::where([
                    'CustomerID' => $booking->CustomerID,
                    'VoucherID' => $booking->VoucherID,
                ])->first();

                if ($customerVoucher) {
                    $customerVoucher->update([
                        'IsUsed' => false,
                        'UsedAt' => null,
                    ]);

                    // Giảm số lần sử dụng của voucher
                    $voucher = Voucher::find($booking->VoucherID);
                    $voucher->decrement('UsedCount');
                }
            }

            // Cập nhật booking
            $booking->update([
                'Status' => 'Cancelled',
            ]);

            // Cập nhật payment nếu có
            if ($booking->payment) {
                $booking->payment->update([
                    'Status' => 'Refunded',
                ]);
            }

            DB::commit();

            return redirect()->route('customer.booking.history')
                ->with('success', 'Hủy đặt vé thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simulate payment gateway
     */
    private function processPaymentGateway($method)
    {
        // Simulate payment processing
        if ($method === 'cash') {
            return true;
        }

        // Simulate 90% success rate for other methods
        return rand(1, 100) <= 90;
    }

    /**
     * API: Lấy thông tin ghế cho showtime
     */
    public function getSeats($showtime_id)
    {
        try {
            $showtime = Showtime::with(['room.seats'])->findOrFail($showtime_id);
            $bookedSeats = BookingDetail::whereHas('booking', function ($query) use ($showtime_id) {
                $query->where('ShowtimeID', $showtime_id)
                    ->whereIn('Status', ['Created', 'Confirmed']);
            })->pluck('SeatID')->toArray();

            return response()->json([
                'success' => true,
                'seats' => $showtime->room->seats,
                'booked_seats' => $bookedSeats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải thông tin ghế.'
            ], 500);
        }
    }

    /**
     * API: Validate voucher
     */
    /**
     * API: Validate voucher
     */
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'base_ticket_price' => 'required|numeric|min:0', // Thêm base ticket price
            'seat_fees' => 'required|numeric|min:0' // Thêm seat fees
        ]);

        try {
            $customer = Auth::user()->customer;
            $voucher = Voucher::where('Code', $request->voucher_code)
                ->where('Status', 'Active')
                ->where('StartDate', '<=', now())
                ->where('EndDate', '>=', now())
                ->where(function ($query) {
                    $query->whereNull('UsageLimit')
                        ->orWhereRaw('UsedCount < UsageLimit');
                })
                ->first();

            if (!$voucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã voucher không hợp lệ hoặc đã hết hạn.'
                ]);
            }

            // Kiểm tra customer có sở hữu voucher này không
            $customerVoucher = CustomerVoucher::where([
                'CustomerID' => $customer->CustomerID,
                'VoucherID' => $voucher->VoucherID,
                'IsUsed' => false
            ])->first();

            if (!$customerVoucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không sở hữu voucher này hoặc đã sử dụng.'
                ]);
            }

            // Tính discount amount dựa trên subtotal (base + fees)
            $subtotal = $request->base_ticket_price + $request->seat_fees;

            if ($voucher->DiscountType === 'Percent') {
                $discountAmount = ($subtotal * $voucher->Value) / 100;
            } else {
                $discountAmount = $voucher->Value;
            }

            $discountAmount = min($discountAmount, $subtotal);
            $finalAmount = $subtotal - $discountAmount;

            return response()->json([
                'success' => true,
                'voucher' => [
                    'VoucherID' => $voucher->VoucherID,
                    'Code' => $voucher->Code,
                    'DiscountType' => $voucher->DiscountType,
                    'Value' => $voucher->Value
                ],
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'message' => 'Áp dụng voucher thành công!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác thực voucher.'
            ], 500);
        }
    }

    /**
     * API: Tính toán giá
     */
    /**
     * API: Tính toán giá
     */
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seat,SeatID',
            'voucher_id' => 'nullable|exists:voucher,VoucherID',
            'showtime_id' => 'required|exists:showtime,ShowtimeID' // Thêm showtime_id
        ]);

        try {
            $showtime = Showtime::findOrFail($request->showtime_id);
            $seats = Seat::whereIn('SeatID', $request->seat_ids)->get();

            // FIXED: Tính tổng tiền chính xác
            $numberOfTickets = count($request->seat_ids);
            $baseTicketPrice = $showtime->Price * $numberOfTickets;
            $seatFees = $seats->sum('Price');
            $subtotal = $baseTicketPrice + $seatFees;

            $discountAmount = 0;
            $voucher = null;

            if ($request->voucher_id) {
                $voucher = Voucher::find($request->voucher_id);
                if ($voucher) {
                    if ($voucher->DiscountType === 'Percent') {
                        $discountAmount = ($subtotal * $voucher->Value) / 100;
                    } else {
                        $discountAmount = $voucher->Value;
                    }
                    $discountAmount = min($discountAmount, $subtotal);
                }
            }

            $finalAmount = $subtotal - $discountAmount;

            return response()->json([
                'success' => true,
                'base_ticket_price' => $baseTicketPrice,
                'seat_fees' => $seatFees,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'seats' => $seats->map(function ($seat) {
                    return [
                        'SeatID' => $seat->SeatID,
                        'SeatNumber' => $seat->SeatNumber,
                        'SeatType' => $seat->SeatType,
                        'Price' => $seat->Price
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tính toán giá.'
            ], 500);
        }
    }
}