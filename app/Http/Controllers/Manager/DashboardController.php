<?php 
namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Customer;
use App\Models\Review;
use App\Models\Theater;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê HÔM NAY
        $todayRevenue = Booking::whereDate('created_at', Carbon::today())
            ->where('PaymentStatus', 'Paid')
            ->sum('TotalAmount');

        $todayTickets = BookingDetail::whereHas('booking', function ($query) {
            $query->whereDate('created_at', Carbon::today())
                  ->where('PaymentStatus', 'Paid');
        })->count();

        $todayBookings = Booking::whereDate('created_at', Carbon::today())
            ->where('PaymentStatus', 'Paid')
            ->count();

        // 2. Thống kê TUẦN NÀY
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $weekRevenue = Booking::whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('PaymentStatus', 'Paid')
            ->sum('TotalAmount');

        $weekTickets = BookingDetail::whereHas('booking', function ($query) use ($weekStart, $weekEnd) {
            $query->whereBetween('created_at', [$weekStart, $weekEnd])
                  ->where('PaymentStatus', 'Paid');
        })->count();

        // 3. Thống kê TỔNG QUAN
        $activeMovies = Movie::where('IsActive', true)->count();
        $totalCustomers = Customer::count();
        $totalTheaters = Theater::count();
        $totalRooms = Room::count();
        
        // 4. Top 3 phim có đánh giá cao nhất
        $topRatedMovies = Review::select('MovieID', DB::raw('AVG(Rating) as avg_rating'), DB::raw('COUNT(ReviewID) as review_count'))
            ->groupBy('MovieID')
            ->having('review_count', '>', 0)
            ->orderByDesc('avg_rating')
            ->limit(3)
            ->get();
        
        // Lấy thông tin phim cho top rated
        foreach ($topRatedMovies as $review) {
            $movie = Movie::find($review->MovieID);
            $review->movie_title = $movie ? $movie->Title : 'Unknown';
            $review->poster_url = $movie ? $movie->PosterURL : null;
        }

        // 5. Thống kê doanh thu theo giờ trong ngày (cho biểu đồ)
        $hourlyRevenue = [];
        for ($i = 0; $i < 24; $i++) {
            $hourStart = Carbon::today()->addHours($i);
            $hourEnd = Carbon::today()->addHours($i + 1);
            
            $revenue = Booking::whereBetween('created_at', [$hourStart, $hourEnd])
                ->where('PaymentStatus', 'Paid')
                ->sum('TotalAmount');
                
            $hourlyRevenue[] = [
                'hour' => sprintf('%02d:00', $i),
                'revenue' => $revenue
            ];
        }

        // 6. Thống kê loại ghế bán chạy
        $seatTypeStats = DB::table('bookingdetail')
            ->join('seat', 'bookingdetail.SeatID', '=', 'seat.SeatID')
            ->join('booking', 'bookingdetail.BookingID', '=', 'booking.BookingID')
            ->where('booking.PaymentStatus', 'Paid')
            ->whereDate('booking.created_at', Carbon::today())
            ->select('seat.SeatType', DB::raw('COUNT(*) as count'))
            ->groupBy('seat.SeatType')
            ->orderByDesc('count')
            ->get();

        // 7. Lịch chiếu sắp tới
        $upcomingShowtimes = Showtime::with(['movie', 'room.theater'])
            ->where('StartTime', '>=', Carbon::now())
            ->where('Status', 'Scheduled')
            ->orderBy('StartTime')
            ->take(10)
            ->get();

        // 8. Booking mới nhất
        $recentBookings = Booking::with(['customer', 'showtime.movie'])
            ->where('PaymentStatus', 'Paid')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // 9. Thống kê tỷ lệ hoàn thành booking
        $bookingStats = [
            'total' => Booking::count(),
            'paid' => Booking::where('PaymentStatus', 'Paid')->count(),
            'pending' => Booking::where('PaymentStatus', 'Pending')->count(),
            'failed' => Booking::where('PaymentStatus', 'Failed')->count(),
        ];

        // 10. Top rạp có doanh thu cao nhất tuần
        $topTheaters = DB::table('booking')
            ->join('showtime', 'booking.ShowtimeID', '=', 'showtime.ShowtimeID')
            ->join('room', 'showtime.RoomID', '=', 'room.RoomID')
            ->join('theater', 'room.TheaterID', '=', 'theater.TheaterID')
            ->whereBetween('booking.created_at', [$weekStart, $weekEnd])
            ->where('booking.PaymentStatus', 'Paid')
            ->select('theater.Name', DB::raw('SUM(booking.TotalAmount) as revenue'))
            ->groupBy('theater.TheaterID', 'theater.Name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('manager.dashboard.index', compact(
            'todayRevenue',
            'todayTickets',
            'todayBookings',
            'weekRevenue',
            'weekTickets',
            'activeMovies',
            'totalCustomers',
            'totalTheaters',
            'totalRooms',
            'topRatedMovies',
            'hourlyRevenue',
            'seatTypeStats',
            'upcomingShowtimes',
            'recentBookings',
            'bookingStats',
            'topTheaters'
        ));
    }
}