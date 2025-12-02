<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Movie;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Hiển thị trang tổng quan Báo cáo (Dashboard Reports).
     */
    public function index()
    {
        // Tổng doanh thu từ các booking đã xác nhận
        $totalRevenue = Booking::where('Status', 'Confirmed')->sum('TotalAmount');

        // Doanh thu tháng hiện tại
        $startOfMonth = Carbon::now()->startOfMonth();
        $monthlyRevenue = Booking::where('Status', 'Confirmed')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('TotalAmount');

        // Phân tích Phim có Doanh thu cao nhất (Top 5)
        // SỬA: Tính doanh thu chính xác hơn, tránh trùng lặp
        $topMovies = DB::table('booking')
            ->join('showtime', 'booking.ShowtimeID', '=', 'showtime.ShowtimeID')
            ->select(
                'showtime.MovieID',
                DB::raw('SUM(booking.TotalAmount) as revenue_sum'),
                DB::raw('COUNT(DISTINCT booking.BookingID) as booking_count')
            )
            ->where('booking.Status', 'Confirmed')
            ->groupBy('showtime.MovieID')
            ->orderByDesc('revenue_sum')
            ->limit(5)
            ->get();

        // Tính tổng số vé cho mỗi phim (đếm bookingdetail)
        foreach ($topMovies as $movie) {
            $movie->ticket_count = DB::table('booking')
                ->join('bookingdetail', 'booking.BookingID', '=', 'bookingdetail.BookingID')
                ->join('showtime', 'booking.ShowtimeID', '=', 'showtime.ShowtimeID')
                ->where('booking.Status', 'Confirmed')
                ->where('showtime.MovieID', $movie->MovieID)
                ->count('bookingdetail.BookingDetailID');
        }

        // Tải thông tin phim
        $topMovieIds = $topMovies->pluck('MovieID')->toArray();
        $moviesData = Movie::whereIn('MovieID', $topMovieIds)->get()->keyBy('MovieID');

        $topMovies = $topMovies->map(function ($item) use ($moviesData) {
            $movie = $moviesData->get($item->MovieID);
            $item->Title = $movie->Title ?? 'Phim không tồn tại';
            $item->PosterURL = $movie->PosterURL ?? '';
            $item->Duration = $movie->Duration ?? null;
            $item->MovieID = $movie->MovieID ?? $item->MovieID;
            return $item;
        });

        // Lấy số lượng phim đang hoạt động
        $activeMovies = Movie::where('IsActive', 1)->count();

        // Tổng số vé đã bán
        $totalTickets = DB::table('booking')
            ->join('bookingdetail', 'booking.BookingID', '=', 'bookingdetail.BookingID')
            ->where('booking.Status', 'Confirmed')
            ->count('bookingdetail.BookingDetailID');

        // Thêm dữ liệu cho biểu đồ
        $monthlyChartData = $this->getMonthlyChartData();
        $revenueTrendData = $this->getRevenueTrendData();

        // DEBUG: Kiểm tra dữ liệu
        logger('Revenue Debug:', [
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'top_movies_total_revenue' => $topMovies->sum('revenue_sum'),
            'top_movie_1_revenue' => $topMovies->first()->revenue_sum ?? 0,
            'total_tickets' => $totalTickets,
        ]);

        return view('manager.reports.index', [
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'topMovies' => $topMovies,
            'activeMovies' => $activeMovies,
            'totalTickets' => $totalTickets,
            'monthlyChartData' => $monthlyChartData,
            'revenueTrendData' => $revenueTrendData,
        ]);
    }

    // Phương thức mới: Lấy dữ liệu cho biểu đồ tròn
    private function getMonthlyChartData()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Doanh thu tháng này
        $monthRevenue = DB::table('booking')
            ->where('Status', 'Confirmed')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('TotalAmount');

        // Số VÉ bán trong tháng (đếm bookingdetail)
        $monthTickets = DB::table('booking')
            ->join('bookingdetail', 'booking.BookingID', '=', 'bookingdetail.BookingID')
            ->where('booking.Status', 'Confirmed')
            ->whereYear('booking.created_at', $currentYear)
            ->whereMonth('booking.created_at', $currentMonth)
            ->count('bookingdetail.BookingDetailID');

        // Số phim mới tháng này
        $newMovies = DB::table('movie')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        return [
            'monthRevenue' => $monthRevenue,
            'monthTickets' => $monthTickets,
            'newMovies' => $newMovies,
        ];
    }

    // Phương thức mới: Lấy dữ liệu xu hướng doanh thu 12 tháng
    private function getRevenueTrendData()
    {
        $revenueData = [];
        $monthLabels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('m/Y');
            $monthLabel = "T" . $date->format('m');

            $revenue = DB::table('booking')
                ->where('Status', 'Confirmed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('TotalAmount');

            $revenueData[] = $revenue;
            $monthLabels[] = $monthLabel;
        }

        return [
            'revenueData' => $revenueData,
            'monthLabels' => $monthLabels,
        ];
    }

    /**
     * Tạo báo cáo Doanh thu theo tháng
     */
    public function generateRevenueReport(Request $request)
    {
        // Lấy năm hiện tại
        $year = $request->get('year', Carbon::now()->year);

        // Tạo mảng đầy đủ 12 tháng
        $allMonths = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthKey = $year . '-' . $monthStr;
            $monthLabel = $monthStr . '/' . $year;

            // Tính doanh thu cho từng tháng
            $startDate = Carbon::create($year, $i, 1)->startOfMonth();
            $endDate = Carbon::create($year, $i, 1)->endOfMonth();

            $revenue = DB::table('booking')
                ->where('Status', 'Confirmed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('TotalAmount');

            // Tính số vé cho từng tháng
            $ticketCount = DB::table('booking')
                ->join('bookingdetail', 'booking.BookingID', '=', 'bookingdetail.BookingID')
                ->where('booking.Status', 'Confirmed')
                ->whereBetween('booking.created_at', [$startDate, $endDate])
                ->count('bookingdetail.BookingDetailID');

            // Tính số booking cho từng tháng
            $bookingCount = DB::table('booking')
                ->where('Status', 'Confirmed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count('BookingID');

            $allMonths[] = [
                'month' => $monthKey,
                'month_label' => $monthLabel,
                'revenue' => $revenue,
                'ticket_count' => $ticketCount,
                'booking_count' => $bookingCount,
            ];
        }

        // Tổng doanh thu năm
        $totalRevenueYear = array_sum(array_column($allMonths, 'revenue'));
        $totalTicketsYear = array_sum(array_column($allMonths, 'ticket_count'));
        $avgRevenuePerTicket = $totalTicketsYear > 0 ? $totalRevenueYear / $totalTicketsYear : 0;

        // Lấy danh sách năm có dữ liệu để hiển thị trong dropdown
        $availableYears = DB::table('booking')
            ->select(DB::raw('YEAR(created_at) as year'))
            ->where('Status', 'Confirmed')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Nếu năm hiện tại không có trong danh sách, thêm vào
        if (!in_array(Carbon::now()->year, $availableYears)) {
            $availableYears[] = Carbon::now()->year;
            rsort($availableYears);
        }

        return view('manager.reports.revenue', [
            'revenueData' => $allMonths,
            'totalRevenueYear' => $totalRevenueYear,
            'totalTicketsYear' => $totalTicketsYear,
            'avgRevenuePerTicket' => $avgRevenuePerTicket,
            'selectedYear' => $year,
            'availableYears' => $availableYears,
        ]);
    }

    /**
     * Tạo báo cáo Hiệu suất Phim
     */
    public function generateMoviePerformanceReport()
    {
        // CÁCH 1: Sử dụng subqueries để tránh trùng lặp
        $performanceData = DB::table('movie')
            ->select(
                'movie.MovieID',
                'movie.Title',
                'movie.PosterURL',
                DB::raw('COALESCE((SELECT SUM(b.TotalAmount) 
                        FROM booking b 
                        JOIN showtime s ON b.ShowtimeID = s.ShowtimeID 
                        WHERE s.MovieID = movie.MovieID 
                        AND b.Status = "Confirmed"), 0) as total_revenue'),
                DB::raw('COALESCE((SELECT COUNT(DISTINCT b.BookingID) 
                        FROM booking b 
                        JOIN showtime s ON b.ShowtimeID = s.ShowtimeID 
                        WHERE s.MovieID = movie.MovieID 
                        AND b.Status = "Confirmed"), 0) as booking_count'),
                DB::raw('COALESCE((SELECT COUNT(DISTINCT r.ReviewID) 
                        FROM review r 
                        WHERE r.MovieID = movie.MovieID), 0) as total_reviews'),
                DB::raw('COALESCE((SELECT AVG(r.Rating) 
                        FROM review r 
                        WHERE r.MovieID = movie.MovieID), 0) as average_rating'),
                DB::raw('COALESCE((SELECT COUNT(bd.BookingDetailID) 
                        FROM booking b 
                        JOIN showtime s ON b.ShowtimeID = s.ShowtimeID 
                        JOIN bookingdetail bd ON b.BookingID = bd.BookingID 
                        WHERE s.MovieID = movie.MovieID 
                        AND b.Status = "Confirmed"), 0) as total_tickets_sold')
            )
            ->orderByDesc('total_revenue')
            ->limit(20)
            ->get();

        return view('manager.reports.performance', compact('performanceData'));
    }

    /**
     * Xuất báo cáo Doanh thu Excel
     */
    public function exportRevenueExcel($year = null)
    {
        $year = $year ?? Carbon::now()->year;

        // Lấy dữ liệu theo cách tương tự như generateRevenueReport
        $revenueData = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthKey = $year . '-' . $monthStr;
            $monthLabel = $monthStr . '/' . $year;

            // Tính doanh thu cho từng tháng
            $startDate = Carbon::create($year, $i, 1)->startOfMonth();
            $endDate = Carbon::create($year, $i, 1)->endOfMonth();

            $revenue = DB::table('booking')
                ->where('Status', 'Confirmed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('TotalAmount');

            // Tính số vé cho từng tháng
            $ticketCount = DB::table('booking')
                ->join('bookingdetail', 'booking.BookingID', '=', 'bookingdetail.BookingID')
                ->where('booking.Status', 'Confirmed')
                ->whereBetween('booking.created_at', [$startDate, $endDate])
                ->count('bookingdetail.BookingDetailID');

            // Tính số booking cho từng tháng
            $bookingCount = DB::table('booking')
                ->where('Status', 'Confirmed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count('BookingID');

            // Tính trung bình mỗi vé
            $avgPerTicket = $ticketCount > 0 ? $revenue / $ticketCount : 0;

            $revenueData[] = [
                'month_key' => $monthKey,
                'Tháng' => $monthLabel,
                'Doanh_thu' => $revenue,
                'Số_lượng_vé' => $ticketCount,
                'Số_booking' => $bookingCount,
                'Trung_bình_mỗi_vé' => $avgPerTicket,
            ];
        }

        $filename = "bao_cao_doanh_thu_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($revenueData, $year) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM
            fwrite($output, "\xEF\xBB\xBF");

            // Tiêu đề
            fputcsv($output, ['BÁO CÁO DOANH THU NĂM ' . $year]);
            fputcsv($output, []); // Dòng trống
            fputcsv($output, ['Tháng', 'Doanh thu (VND)', 'Số lượng vé', 'Số booking', 'Trung bình mỗi vé (VND)']);

            // Dữ liệu
            foreach ($revenueData as $row) {
                fputcsv($output, [
                    $row['Tháng'],
                    number_format($row['Doanh_thu']),
                    $row['Số_lượng_vé'],
                    $row['Số_booking'],
                    number_format($row['Trung_bình_mỗi_vé'])
                ]);
            }

            // Tổng cộng
            fputcsv($output, []); // Dòng trống

            $totalRevenue = array_sum(array_column($revenueData, 'Doanh_thu'));
            $totalTickets = array_sum(array_column($revenueData, 'Số_lượng_vé'));
            $totalBookings = array_sum(array_column($revenueData, 'Số_booking'));
            $avgPerTicket = $totalTickets > 0 ? $totalRevenue / $totalTickets : 0;

            fputcsv($output, [
                'TỔNG CỘNG',
                number_format($totalRevenue),
                $totalTickets,
                $totalBookings,
                number_format($avgPerTicket)
            ]);

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Xuất báo cáo Hiệu suất Phim Excel - Phiên bản sửa lỗi doanh thu
     */
    public function exportPerformanceExcel()
    {
        // CÁCH 1: Sử dụng subquery để tránh trùng lặp
        $performanceData = DB::table('movie')
            ->select(
                'movie.MovieID',
                'movie.Title',
                DB::raw('COALESCE((SELECT SUM(b.TotalAmount) 
                        FROM booking b 
                        JOIN showtime s ON b.ShowtimeID = s.ShowtimeID 
                        WHERE s.MovieID = movie.MovieID 
                        AND b.Status = "Confirmed"), 0) as total_revenue'),
                DB::raw('COALESCE((SELECT COUNT(DISTINCT b.BookingID) 
                        FROM booking b 
                        JOIN showtime s ON b.ShowtimeID = s.ShowtimeID 
                        WHERE s.MovieID = movie.MovieID 
                        AND b.Status = "Confirmed"), 0) as booking_count'),
                DB::raw('COALESCE((SELECT COUNT(DISTINCT r.ReviewID) 
                        FROM review r 
                        WHERE r.MovieID = movie.MovieID), 0) as total_reviews'),
                DB::raw('COALESCE((SELECT AVG(r.Rating) 
                        FROM review r 
                        WHERE r.MovieID = movie.MovieID), 0) as average_rating'),
                DB::raw('COALESCE((SELECT COUNT(bd.BookingDetailID) 
                        FROM booking b 
                        JOIN showtime s ON b.ShowtimeID = s.ShowtimeID 
                        JOIN bookingdetail bd ON b.BookingID = bd.BookingID 
                        WHERE s.MovieID = movie.MovieID 
                        AND b.Status = "Confirmed"), 0) as total_tickets_sold')
            )
            ->orderByDesc('total_revenue')
            ->limit(50)
            ->get();

        $filename = "bao_cao_hieu_suat_phim_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($performanceData) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM
            fwrite($output, "\xEF\xBB\xBF");

            // Tiêu đề
            fputcsv($output, ['BÁO CÁO HIỆU SUẤT PHIM - ' . date('d/m/Y')]);
            fputcsv($output, []); // Dòng trống
            fputcsv($output, ['STT', 'Mã phim', 'Tên phim', 'Doanh thu (VND)', 'Số vé bán', 'Số booking', 'Số review', 'Điểm đánh giá TB', 'Trung bình/vé']);

            // Dữ liệu
            foreach ($performanceData as $index => $movie) {
                $avgPerTicket = ($movie->total_tickets_sold > 0)
                    ? $movie->total_revenue / $movie->total_tickets_sold
                    : 0;

                fputcsv($output, [
                    $index + 1,
                    $movie->MovieID,
                    $movie->Title,
                    number_format($movie->total_revenue),
                    $movie->total_tickets_sold,
                    $movie->booking_count,
                    $movie->total_reviews,
                    number_format($movie->average_rating, 1),
                    number_format($avgPerTicket)
                ]);
            }

            // Tổng cộng
            fputcsv($output, []); // Dòng trống
            $totalRevenue = $performanceData->sum('total_revenue');
            $totalTickets = $performanceData->sum('total_tickets_sold');
            $totalBookings = $performanceData->sum('booking_count');
            $totalReviews = $performanceData->sum('total_reviews');
            $avgRating = $performanceData->avg('average_rating');
            $avgPerTicketAll = $totalTickets > 0 ? $totalRevenue / $totalTickets : 0;

            fputcsv($output, [
                'TỔNG CỘNG',
                '',
                '',
                number_format($totalRevenue),
                $totalTickets,
                $totalBookings,
                $totalReviews,
                number_format($avgRating, 1),
                number_format($avgPerTicketAll)
            ]);

            fclose($output);
        }, 200, $headers);
    }
}