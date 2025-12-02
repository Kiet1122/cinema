<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Showtime;
use Carbon\Carbon;

class DetailsMovieController extends Controller
{
    /**
     * Hiển thị trang chi tiết phim và các suất chiếu.
     */
    public function details($id)
    {
        // 1. Lấy chi tiết phim
        $movie = Movie::with('genres', 'reviews.customer.user')
                      ->where('MovieID', $id)
                      ->where('IsActive', 1)
                      ->firstOrFail();

        // 2. Định nghĩa phạm vi ngày (7 ngày tới)
        $today = Carbon::today();
        $end_date = Carbon::today()->addDays(6);
        $date_range = [];
        
        // Tạo mảng 7 ngày
        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            $date_range[] = $date;
        }

        // 3. Lấy tất cả suất chiếu còn hiệu lực cho bộ phim này
        $rawShowtimes = Showtime::where('MovieID', $id)
            ->where('StartTime', '>=', Carbon::now())
            ->whereBetween('StartTime', [$today->startOfDay(), $end_date->endOfDay()])
            ->where('Status', 'Scheduled')
            ->with(['room', 'theater'])
            ->orderBy('StartTime', 'asc')
            ->get();
            
        // 4. Nhóm suất chiếu theo Ngày và Rạp (Theater)
        $showtimesByDate = [];
        foreach ($date_range as $date) {
            $dateString = $date->toDateString();
            $showtimesForDate = $rawShowtimes->filter(function ($showtime) use ($dateString) {
                return Carbon::parse($showtime->StartTime)->toDateString() === $dateString;
            });

            // Nhóm sâu hơn theo Rạp
            $showtimesByTheater = $showtimesForDate->groupBy('theater.Name');

            $showtimesByDate[$dateString] = [
                'date' => $date,
                'showtimes' => $showtimesByTheater,
            ];
        }

        // Tính rating trung bình
        $averageRating = $movie->reviews->avg('Rating');

        return view('customer.movie.details', compact('movie', 'showtimesByDate', 'averageRating'));
    }
}