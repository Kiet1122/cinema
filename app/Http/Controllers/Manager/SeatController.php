<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seat;
use App\Models\Room;
use Carbon\Carbon;

class SeatController extends Controller
{
    /**
     * Hiển thị danh sách ghế theo phòng
     */
    public function index()
    {
        // Lấy tất cả phòng, ghế và quản lý
        $rooms = Room::with(['seats', 'manager', 'theater'])->get();

        // Gộp thông tin ghế cho từng phòng
        $rooms = $rooms->map(function ($room) {
            $seatSummary = $room->seats->groupBy('SeatType')->map(function ($seats, $type) {
                return count($seats) . ' ' . $type;
            })->implode(', ');

            $room->seatSummary = $seatSummary ?: 'N/A';
            $room->managerName = $room->manager->FullName ?? 'N/A';

            return $room;
        });
        $totalSeatsByType = [
            'Standard' => $rooms->flatMap(fn($r) => $r->seats->where('SeatType', 'Standard'))->count(),
            'VIP' => $rooms->flatMap(fn($r) => $r->seats->where('SeatType', 'VIP'))->count(),
            'Couple' => $rooms->flatMap(fn($r) => $r->seats->where('SeatType', 'Couple'))->count(),
            'Total' => $rooms->flatMap(fn($r) => $r->seats)->count()
        ];

        return view('manager.seat.index', compact('rooms', 'totalSeatsByType'));
    }


    /**
     * Hiển thị form tạo ghế mới
     */
    public function create()
    {
        $rooms = Room::all(); // Lấy danh sách phòng để chọn
        return view('manager.seat.create', compact('rooms'));
    }

    /**
     * Lưu ghế mới vào database
     */
    public function store(Request $request)
    {
        $request->validate([
            'RoomID' => 'required|exists:room,RoomID',
            'Standard' => 'nullable|integer|min:0',
            'VIP' => 'nullable|integer|min:0',
            'Couple' => 'nullable|integer|min:0',
            'StandardPrice' => 'required|numeric|min:0',
            'VIPPrice' => 'required|numeric|min:0',
            'CouplePrice' => 'required|numeric|min:0',
        ]);

        $room = Room::findOrFail($request->RoomID);

        // Lấy số lượng từng loại từ form
        $qtyStandard = (int) $request->Standard;
        $qtyVIP = (int) $request->VIP;
        $qtyCouple = (int) $request->Couple;

        $totalSeats = $qtyStandard + $qtyVIP + $qtyCouple;

        // Kiểm tra tổng bằng Capacity
        if ($totalSeats != $room->Capacity) {
            return back()->withErrors([
                'RoomID' => 'Tổng số ghế bạn nhập (' . $totalSeats . ') phải bằng Capacity của phòng (' . $room->Capacity . ').'
            ])->withInput();
        }

        $managerId = auth()->user()->manager->ManagerID ?? null;

        // Lấy giá từ form
        $prices = [
            'Standard' => (float) $request->StandardPrice,
            'VIP' => (float) $request->VIPPrice,
            'Couple' => (float) $request->CouplePrice,
        ];

        $types = [
            'Standard' => $qtyStandard,
            'VIP' => $qtyVIP,
            'Couple' => $qtyCouple,
        ];

        foreach ($types as $type => $qty) {
            // Lấy số ghế hiện có của loại này
            $currentTypeSeats = Seat::where('RoomID', $room->RoomID)
                ->where('SeatType', $type)
                ->count();

            $seatPrice = $prices[$type]; // lấy giá từ form

            for ($i = 1; $i <= $qty; $i++) {
                $seatNumber = match ($type) {
                    'Standard' => 'S' . ($currentTypeSeats + $i),
                    'VIP' => 'V' . ($currentTypeSeats + $i),
                    'Couple' => 'C' . ($currentTypeSeats + $i),
                };

                Seat::create([
                    'RoomID' => $room->RoomID,
                    'SeatNumber' => $seatNumber,
                    'SeatType' => $type,
                    'ManagerID' => $managerId,
                    'Price' => $seatPrice,
                ]);
            }
        }

        return redirect()->route('manager.seats.index')
            ->with('success', 'Thêm ghế thành công!');
    }







    /**
     * Hiển thị form chỉnh sửa ghế
     */
    public function edit($roomId)
    {
        // Lấy phòng theo RoomID cùng danh sách ghế
        $room = Room::with('seats')->findOrFail($roomId);

        $seatsByType = $room->seats->groupBy('SeatType');

        $standardCount = isset($seatsByType['Standard']) ? $seatsByType['Standard']->count() : 0;
        $vipCount = isset($seatsByType['VIP']) ? $seatsByType['VIP']->count() : 0;
        $coupleCount = isset($seatsByType['Couple']) ? $seatsByType['Couple']->count() : 0;

        $standardPrice = isset($seatsByType['Standard'][0]) ? $seatsByType['Standard'][0]->Price : 0;
        $vipPrice = isset($seatsByType['VIP'][0]) ? $seatsByType['VIP'][0]->Price : 0;
        $couplePrice = isset($seatsByType['Couple'][0]) ? $seatsByType['Couple'][0]->Price : 0;

        return view('Manager.seat.edit', compact(
            'room',
            'standardCount',
            'vipCount',
            'coupleCount',
            'standardPrice',
            'vipPrice',
            'couplePrice'
        ));
    }

    public function update(Request $request, $roomId)
    {
        $request->validate([
            'Standard' => 'required|integer|min:0',
            'VIP' => 'required|integer|min:0',
            'Couple' => 'required|integer|min:0',
            'StandardPrice' => 'required|numeric|min:0',
            'VIPPrice' => 'required|numeric|min:0',
            'CouplePrice' => 'required|numeric|min:0',
        ]);

        $room = Room::with('seats')->findOrFail($roomId);

        $qtyStandard = (int) $request->Standard;
        $qtyVIP = (int) $request->VIP;
        $qtyCouple = (int) $request->Couple;
        $totalSeats = $qtyStandard + $qtyVIP + $qtyCouple;

        if ($totalSeats != $room->Capacity) {
            return back()->withErrors([
                'Capacity' => 'Tổng số ghế (Standard + VIP + Couple = ' . $totalSeats . ') phải bằng Capacity của phòng (' . $room->Capacity . ').'
            ])->withInput();
        }

        $managerId = auth()->user()->manager->ManagerID ?? null;

        // Xóa tất cả ghế cũ của phòng
        $room->seats()->delete();

        // Lấy giá từ request
        $prices = [
            'Standard' => (float) $request->StandardPrice,
            'VIP' => (float) $request->VIPPrice,
            'Couple' => (float) $request->CouplePrice,
        ];

        // Tạo lại ghế
        $types = [
            'Standard' => $qtyStandard,
            'VIP' => $qtyVIP,
            'Couple' => $qtyCouple,
        ];

        foreach ($types as $type => $qty) {
            $seatPrice = $prices[$type]; // lấy giá từ form
            for ($i = 1; $i <= $qty; $i++) {
                $seatNumber = match ($type) {
                    'Standard' => 'S' . $i,
                    'VIP' => 'V' . $i,
                    'Couple' => 'C' . $i,
                };

                Seat::create([
                    'RoomID' => $room->RoomID,
                    'SeatNumber' => $seatNumber,
                    'SeatType' => $type,
                    'ManagerID' => $managerId,
                    'Price' => $seatPrice,
                ]);
            }
        }

        return redirect()->route('manager.seats.index')
            ->with('success', 'Cập nhật ghế thành công!');
    }




}
