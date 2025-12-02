<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class BookingsController extends Controller
{
    /**
     * Hiển thị danh sách tất cả các giao dịch đặt vé (Bookings).
     * Cho phép lọc theo trạng thái (Created, Confirmed, Cancelled) và tìm kiếm.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Lấy các tham số lọc từ request
        $filterStatus = $request->get('status', 'all');
        $searchQuery = $request->get('search');
        
        // Bắt đầu truy vấn, tải thông tin Customer và Showtime.Movie liên quan
        $bookings = Booking::with([
            'customer.user', 
            'showtime.movie',
            'bookingDetails.seat',
            'payment'
        ])->latest(); // Sắp xếp theo booking mới nhất trước

        // 1. Lọc theo trạng thái
        $validStatuses = ['Created', 'Confirmed', 'Cancelled'];
        
        if (in_array($filterStatus, $validStatuses)) {
            $bookings->where('Status', $filterStatus);
        }

        // 2. Lọc theo tìm kiếm
        if ($searchQuery) {
            $bookings->where(function ($query) use ($searchQuery) {
                // Tìm kiếm theo Mã booking (BookingID)
                $query->where('BookingID', 'like', '%' . $searchQuery . '%')
                    
                    // Hoặc tìm kiếm theo tên phim (qua relationship 'showtime.movie')
                    ->orWhereHas('showtime.movie', function ($q) use ($searchQuery) {
                        $q->where('Title', 'like', '%' . $searchQuery . '%');
                    })
                    
                    // Hoặc tìm kiếm theo tên khách hàng (qua relationship 'customer')
                    ->orWhereHas('customer', function ($q) use ($searchQuery) {
                        $q->where('FullName', 'like', '%' . $searchQuery . '%');
                    })
                    
                    // Hoặc tìm kiếm theo email khách hàng (qua relationship 'customer.user')
                    ->orWhereHas('customer.user', function ($q) use ($searchQuery) {
                        $q->where('Email', 'like', '%' . $searchQuery . '%');
                    })
                    
                    // Hoặc tìm kiếm theo số điện thoại khách hàng
                    ->orWhereHas('customer', function ($q) use ($searchQuery) {
                        $q->where('Phone', 'like', '%' . $searchQuery . '%');
                    });
            });
        }

        $bookings = $bookings->paginate(20); // Phân trang 20 kết quả

        // Trả về view quản lý đặt vé
        return view('manager.bookings.index', [
            'bookings' => $bookings,
            'filterStatus' => $filterStatus,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Hiển thị chi tiết một booking
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $booking = Booking::with([
            'customer.user',
            'showtime.movie',
            'showtime.room.theater',
            'bookingDetails.seat',
            'voucher',
            'payment'
        ])->findOrFail($id);

        return view('manager.bookings.show', compact('booking'));
    }

    /**
     * Cập nhật trạng thái của một giao dịch đặt vé.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id ID của Booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $newStatus = $request->input('status');

        // Kiểm tra trạng thái hợp lệ
        $validStatuses = ['Created', 'Confirmed', 'Cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            return back()->with('error', 'Trạng thái cập nhật không hợp lệ.');
        }

        try {
            // Lưu lại trạng thái cũ cho log hoặc logic nghiệp vụ phức tạp hơn
            $oldStatus = $booking->Status; 
            
            $booking->Status = $newStatus;
            
            // Nếu xác nhận booking, cập nhật cả trạng thái thanh toán nếu cần
            if ($newStatus === 'Confirmed' && $booking->PaymentStatus === 'Pending') {
                $booking->PaymentStatus = 'Paid';
            }
            
            // Nếu hủy booking, cập nhật trạng thái thanh toán
            if ($newStatus === 'Cancelled' && $booking->PaymentStatus === 'Paid') {
                $booking->PaymentStatus = 'Refunded';
            }
            
            $booking->save();
            
            Log::info("Booking ID: {$id} đã được cập nhật trạng thái từ {$oldStatus} sang {$newStatus}.");

            return back()->with('success', 'Đã cập nhật trạng thái đặt vé thành công.');

        } catch (\Exception $e) {
            Log::error("Lỗi cập nhật trạng thái Booking ID: {$id}. Lỗi: " . $e->getMessage());
            return back()->with('error', 'Không thể cập nhật trạng thái đặt vé. Vui lòng thử lại.');
        }
    }

    /**
     * Cập nhật trạng thái thanh toán
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $newPaymentStatus = $request->input('payment_status');

        $validPaymentStatuses = ['Pending', 'Paid', 'Failed', 'Refunded'];
        if (!in_array($newPaymentStatus, $validPaymentStatuses)) {
            return back()->with('error', 'Trạng thái thanh toán không hợp lệ.');
        }

        try {
            $oldPaymentStatus = $booking->PaymentStatus;
            
            $booking->PaymentStatus = $newPaymentStatus;
            
            // Nếu thanh toán thành công, cập nhật trạng thái booking
            if ($newPaymentStatus === 'Paid' && $booking->Status === 'Created') {
                $booking->Status = 'Confirmed';
            }
            
            $booking->save();
            
            Log::info("Booking ID: {$id} đã được cập nhật trạng thái thanh toán từ {$oldPaymentStatus} sang {$newPaymentStatus}.");

            return back()->with('success', 'Đã cập nhật trạng thái thanh toán thành công.');

        } catch (\Exception $e) {
            Log::error("Lỗi cập nhật trạng thái thanh toán Booking ID: {$id}. Lỗi: " . $e->getMessage());
            return back()->with('error', 'Không thể cập nhật trạng thái thanh toán. Vui lòng thử lại.');
        }
    }

    /**
     * Xóa vĩnh viễn một giao dịch đặt vé khỏi hệ thống.
     *
     * @param  int  $id ID của Booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $booking = Booking::with(['bookingDetails', 'payment'])->findOrFail($id);
            $bookingId = $booking->BookingID;

            // Xóa các bản ghi liên quan trước
            if ($booking->bookingDetails) {
                $booking->bookingDetails()->delete();
            }
            
            if ($booking->payment) {
                $booking->payment()->delete();
            }

            // Xóa booking
            $booking->delete();

            Log::warning("Đã xóa vĩnh viễn Booking ID: {$bookingId} khỏi hệ thống.");

            return redirect()->route('manager.bookings.index')->with('success', "Đã xóa giao dịch đặt vé (#{$bookingId}) thành công.");

        } catch (\Exception $e) {
            Log::error("Lỗi xóa Booking ID: {$id}. Lỗi: " . $e->getMessage());
            return back()->with('error', 'Không thể xóa giao dịch đặt vé. Vui lòng thử lại.');
        }
    }

    /**
     * Hiển thị form chỉnh sửa booking
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $booking = Booking::with([
            'customer.user',
            'showtime.movie',
            'showtime.room.theater',
            'bookingDetails.seat',
            'voucher',
            'payment'
        ])->findOrFail($id);

        return view('manager.bookings.edit', compact('booking'));
    }

    /**
     * Cập nhật thông tin booking
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'TotalAmount' => 'required|numeric|min:0',
            'Status' => 'required|in:Created,Confirmed,Cancelled',
            'PaymentStatus' => 'required|in:Pending,Paid,Failed,Refunded'
        ]);

        try {
            $booking = Booking::findOrFail($id);
            
            $booking->update([
                'TotalAmount' => $request->TotalAmount,
                'Status' => $request->Status,
                'PaymentStatus' => $request->PaymentStatus
            ]);

            Log::info("Booking ID: {$id} đã được cập nhật thông tin.");

            return redirect()->route('manager.bookings.show', $id)->with('success', 'Cập nhật thông tin booking thành công!');

        } catch (\Exception $e) {
            Log::error("Lỗi cập nhật Booking ID: {$id}. Lỗi: " . $e->getMessage());
            return back()->with('error', 'Không thể cập nhật thông tin booking. Vui lòng thử lại.');
        }
    }
}