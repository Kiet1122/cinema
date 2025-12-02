@extends('manager.layouts.app')

@section('title', 'Phân Tích Hiệu Suất Phim')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">Phân Tích Hiệu Suất Phim</h1>
                <p class="text-muted">Đánh giá chi tiết hiệu suất các phim đang chiếu</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Nút Quay Lại -->
                <a href="{{ route('manager.reports.index') }}" class="btn btn-outline-primary d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i>
                    Quay Lại
                </a>

                <!-- Nút Xuất Excel - SỬA ROUTE -->
                @if(Route::has('manager.reports.export.performance'))
                    <a href="{{ route('manager.reports.export.performance') }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @elseif(Route::has('reports.export.performance'))
                    <a href="{{ route('reports.export.performance') }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @else
                    <a href="{{ url('/manager/reports/performance/export') }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @endif
            </div>
        </div>

        <!-- Thống kê tổng quan -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng Doanh Thu
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        $totalRevenue = $performanceData->sum('total_revenue');
                                    @endphp
                                    {{ number_format($totalRevenue) }} VND
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Tổng Vé Bán Ra
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        // SỬA: Sử dụng total_tickets_sold thay vì sum trực tiếp
                                        $totalTickets = 0;
                                        foreach ($performanceData as $movie) {
                                            $totalTickets += $movie->total_tickets_sold ?? 0;
                                        }
                                    @endphp
                                    {{ number_format($totalTickets) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Điểm Đánh Giá TB
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        // SỬA: Tính trung bình đúng cách
                                        $totalRating = 0;
                                        $count = 0;
                                        foreach ($performanceData as $movie) {
                                            if ($movie->average_rating > 0) {
                                                $totalRating += $movie->average_rating;
                                                $count++;
                                            }
                                        }
                                        $avgRating = $count > 0 ? $totalRating / $count : 0;
                                    @endphp
                                    {{ number_format($avgRating, 1) }}/5
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-star fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Tổng Số Reviews
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        $totalReviews = $performanceData->sum('total_reviews');
                                    @endphp
                                    {{ number_format($totalReviews) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-comment fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ... phần còn lại của code giữ nguyên ... -->

        <!-- Bảng dữ liệu chi tiết - SỬA: Đảm bảo kiểm tra dữ liệu -->
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>Danh Sách Hiệu Suất Phim
                </h6>
                <span class="badge bg-primary">Top {{ count($performanceData) }} phim</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="performanceTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="30%">Tên Phim</th>
                                <th width="15%" class="text-end">Doanh Thu</th>
                                <th width="12%" class="text-center">Số Vé</th>
                                <th width="15%" class="text-center">Đánh Giá</th>
                                <th width="12%" class="text-center">Reviews</th>
                                <th width="11%" class="text-center">Hiệu Suất</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($performanceData as $index => $movie)
                                @php
                                    // Đảm bảo biến tồn tại
                                    $total_revenue = $movie->total_revenue ?? 0;
                                    $total_tickets_sold = $movie->total_tickets_sold ?? 0;
                                    $total_reviews = $movie->total_reviews ?? 0;
                                    $average_rating = $movie->average_rating ?? 0;
                                    
                                    $maxRevenue = $performanceData->max('total_revenue') ?? 1;
                                    $revenuePercent = $maxRevenue > 0 ? ($total_revenue / $maxRevenue) * 100 : 0;
                                    
                                    $avgTicketPrice = ($total_tickets_sold > 0 && $total_revenue > 0) 
                                        ? $total_revenue / $total_tickets_sold 
                                        : 0;
                                        
                                    $rating = $average_rating;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = $rating - $fullStars >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                    
                                    // Tính điểm hiệu suất
                                    $totalRevenueAll = $performanceData->sum('total_revenue') ?: 1;
                                    $totalTicketsAll = 0;
                                    foreach ($performanceData as $m) {
                                        $totalTicketsAll += $m->total_tickets_sold ?? 0;
                                    }
                                    $totalReviewsAll = $performanceData->sum('total_reviews') ?: 1;
                                    
                                    $revenueScore = ($total_revenue / $totalRevenueAll) * 40;
                                    $ticketScore = ($totalTicketsAll > 0 ? ($total_tickets_sold / $totalTicketsAll) * 30 : 0);
                                    $ratingScore = ($rating / 5) * 20;
                                    $reviewScore = ($totalReviewsAll > 0 ? ($total_reviews / $totalReviewsAll) * 10 : 0);
                                    
                                    $performanceScore = $revenueScore + $ticketScore + $ratingScore + $reviewScore;
                                @endphp
                                <tr class="movie-row" 
                                    data-rating="{{ $rating }}"
                                    data-revenue="{{ $total_revenue }}"
                                    data-tickets="{{ $total_tickets_sold }}"
                                    data-reviews="{{ $total_reviews }}"
                                    data-title="{{ strtolower($movie->Title ?? '') }}">
                                    <td class="text-center fw-bold">
                                        <span class="badge 
                                                    @if($index < 3) bg-warning text-dark
                                                    @elseif($index < 5) bg-secondary
                                                    @else bg-light text-dark
                                                    @endif">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                @if(isset($movie->PosterURL) && $movie->PosterURL)
                                                    <img src="{{ $movie->PosterURL }}" 
                                                         alt="{{ $movie->Title ?? 'Phim' }}" 
                                                         class="img-fluid rounded" 
                                                         style="width: 50px; height: 70px; object-fit: cover;">
                                                @else
                                                    <i class="fas fa-film text-primary fa-lg"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 text-primary fw-bold movie-title">{{ $movie->Title ?? 'Không có tên' }}</h6>
                                                <small class="text-muted">ID: {{ $movie->MovieID ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success">
                                            {{ number_format($total_revenue) }} VND
                                        </span>
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $revenuePercent }}%" 
                                                aria-valuenow="{{ $revenuePercent }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-gray-700">
                                            {{ number_format($total_tickets_sold) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ number_format($avgTicketPrice) }} VND/vé
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            @for($i = 0; $i < $fullStars; $i++)
                                                <i class="fas fa-star text-warning me-1"></i>
                                            @endfor

                                            @if($hasHalfStar)
                                                <i class="fas fa-star-half-alt text-warning me-1"></i>
                                            @endif

                                            @for($i = 0; $i < $emptyStars; $i++)
                                                <i class="far fa-star text-warning me-1"></i>
                                            @endfor

                                            <span class="fw-bold ms-2 {{ $rating >= 4 ? 'text-warning' : 'text-gray-600' }}">
                                                {{ number_format($rating, 1) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-info">
                                            {{ number_format($total_reviews) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar 
                                                        @if($performanceScore >= 80) bg-success
                                                        @elseif($performanceScore >= 60) bg-info
                                                        @elseif($performanceScore >= 40) bg-warning
                                                        @else bg-danger
                                                        @endif" 
                                                 role="progressbar" 
                                                 style="width: {{ $performanceScore }}%"
                                                 aria-valuenow="{{ $performanceScore }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <span class="fw-bold text-white">{{ number_format($performanceScore, 0) }}%</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-film fa-3x mb-3"></i>
                                            <p>Không tìm thấy dữ liệu hiệu suất phim.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Dữ liệu cho biểu đồ so sánh - SỬA: Kiểm tra dữ liệu
        const topMovies = @json($performanceData->take(5)->values());
        const revenueData = topMovies.map(movie => movie.total_revenue ?? 0);
        const ticketData = topMovies.map(movie => movie.total_tickets_sold ?? 0);
        const movieTitles = topMovies.map(movie => {
            const title = movie.Title || 'Unknown';
            return title.substring(0, 15) + (title.length > 15 ? '...' : '');
        });

        // Khởi tạo biểu đồ
        document.addEventListener('DOMContentLoaded', function () {
            // Chỉ tạo biểu đồ nếu có dữ liệu
            if (revenueData.length > 0 && revenueData.some(value => value > 0)) {
                const ctx = document.getElementById('revenueComparisonChart').getContext('2d');
                const revenueChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: movieTitles,
                        datasets: [{
                            label: 'Doanh Thu (VND)',
                            data: revenueData,
                            backgroundColor: 'rgba(79, 70, 229, 0.7)',
                            borderColor: 'rgba(79, 70, 229, 1)',
                            borderWidth: 1,
                            yAxisID: 'y',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Doanh Thu (VND)',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    callback: function (value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000).toFixed(1) + 'M';
                                        } else if (value >= 1000) {
                                            return (value / 1000).toFixed(0) + 'K';
                                        }
                                        return value;
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tên Phim',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += new Intl.NumberFormat('vi-VN', {
                                            style: 'currency',
                                            currency: 'VND'
                                        }).format(context.parsed.y);
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                // Ẩn biểu đồ nếu không có dữ liệu
                document.getElementById('revenueComparisonChart').closest('.card').style.display = 'none';
            }

            // ... phần xử lý bộ lọc và tìm kiếm giữ nguyên ...
        });
    </script>

    <style>
        .progress {
            border-radius: 0.375rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(79, 70, 229, 0.05);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .chart-bar {
            position: relative;
        }
        
        .badge {
            min-width: 30px;
        }
        
        img {
            max-width: 100%;
        }
    </style>
@endsection