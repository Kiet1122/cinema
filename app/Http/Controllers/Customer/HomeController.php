<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home()
    {
        // 1. Khởi tạo biến mặc định
        $customer = null;
        $upcomingBookings = collect();

        // 2. Lấy danh sách phim đang chiếu (IsActive = 1)
        $activeMovies = Movie::where('IsActive', 1)
                        ->orderBy('ReleaseDate', 'desc')
                        ->take(8)
                        ->get();

        // 3. Lấy danh sách phim ngừng chiếu (IsActive = 0)
        $endedMovies = Movie::where('IsActive', 0)
                        ->orderBy('ReleaseDate', 'desc')
                        ->take(8)
                        ->get();

        // 4. Nếu đã đăng nhập thì lấy upcoming bookings
        if (Auth::check()) {
            $user = Auth::user();
            $customer = $user->customer;

            if ($customer) {
                $upcomingBookings = Booking::where('CustomerID', $customer->CustomerID)
                    ->join('showtime', 'booking.ShowtimeID', '=', 'showtime.ShowtimeID')
                    ->where('showtime.StartTime', '>', Carbon::now())
                    ->whereIn('booking.Status', ['Created', 'Confirmed'])
                    ->orderBy('showtime.StartTime', 'asc')
                    ->select('booking.*')
                    ->with(['showtime.movie', 'showtime.room', 'bookingDetails'])
                    ->get();
            }
        }

        // 5. Trả về view với đầy đủ biến
        return view('customer.home.home', [
            'customer' => $customer,
            'activeMovies' => $activeMovies, // Phim đang chiếu
            'endedMovies' => $endedMovies,   // Phim ngừng chiếu
            'upcomingBookings' => $upcomingBookings
        ]);
    }
}