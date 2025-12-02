@extends('manager.layouts.app')

@section('title', 'Báo Cáo & Thống Kê')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Báo Cáo Tổng Quan Hoạt Động</h1>
        </div>

        <!-- 1. Thẻ Chỉ Số Tổng Hợp -->
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
                                    {{ number_format($totalRevenue ?? 0) }} VND
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-muted">
                            Doanh thu từ tất cả booking đã xác nhận
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
                                    Doanh Thu Tháng Hiện Tại
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($monthlyRevenue ?? 0) }} VND
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-muted">
                            Doanh thu từ đầu tháng {{ date('m/Y') }}
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
                                    Phim Đang Hoạt Động
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($activeMovies ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-film fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-muted">
                            Tổng số phim đang active trong hệ thống
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
                                    Tổng Số Vé Đã Bán
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalTickets ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-muted">
                            Tổng số vé (ghế) đã được đặt thành công
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Báo Cáo Chi Tiết và Đường Dẫn Nhanh -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Truy Cập Báo Cáo Chi Tiết</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <a href="{{ route('manager.reports.revenue') }}"
                                    class="btn btn-primary btn-block p-3 d-flex align-items-center justify-content-start">
                                    <i class="fas fa-chart-line fa-2x me-3"></i>
                                    <div class="text-start">
                                        <h5 class="mb-1">Báo Cáo Doanh Thu</h5>
                                        <p class="mb-0 text-light">Phân tích doanh thu theo thời gian</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <a href="{{ route('manager.reports.performance') }}"
                                    class="btn btn-success btn-block p-3 d-flex align-items-center justify-content-start">
                                    <i class="fas fa-film fa-2x me-3"></i>
                                    <div class="text-start">
                                        <h5 class="mb-1">Phân Tích Hiệu Suất Phim</h5>
                                        <p class="mb-0 text-light">Đánh giá hiệu quả các bộ phim</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Top 5 Phim Doanh Thu Cao Nhất -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Top 5 Phim Doanh Thu Cao Nhất</h6>
                        <span class="badge bg-primary">Cập nhật mới nhất</span>
                    </div>
                    <div class="card-body">
                        @forelse ($topMovies as $index => $movie)
                            <div
                                class="row align-items-center mb-3 p-3 rounded {{ $index === 0 ? 'bg-warning bg-opacity-10' : 'bg-light' }}">
                                <div class="col-md-1 text-center">
                                    <span
                                        class="badge {{ $index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : ($index === 2 ? 'bg-danger' : 'bg-dark')) }} rounded-circle p-2">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="col-md-2">
                                    @if(isset($movie->PosterURL) && $movie->PosterURL)
                                        <img src="{{ $movie->PosterURL }}" alt="{{ $movie->Title ?? 'Phim' }}"
                                            class="img-fluid rounded shadow-sm" style="max-height: 120px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                            style="width: 100px; height: 120px;">
                                            <i class="fas fa-film text-white fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <h5 class="mb-1">{{ $movie->Title ?? 'Không có tên' }}</h5>
                                    <p class="text-muted mb-0">ID: {{ $movie->MovieID ?? 'N/A' }}</p>
                                    @if(isset($movie->Duration) && $movie->Duration)
                                        <small class="text-muted">{{ $movie->Duration }} phút</small>
                                    @endif
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-ticket-alt me-1"></i> Số vé:
                                            {{ number_format($movie->ticket_count ?? 0) }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <small class="text-muted">Tổng doanh thu</small>
                                    <h4 class="text-success font-weight-bold">{{ number_format($movie->revenue_sum ?? 0) }} VND
                                    </h4>
                                    
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-film fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Chưa có dữ liệu booking nào để phân tích.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê bổ sung -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thống Kê Theo Tháng</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> Doanh thu
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> Số vé
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-info"></i> Phim mới
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Xu Hướng Doanh Thu 12 Tháng Gần Nhất</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biểu đồ tròn với dữ liệu thực
        var ctxPie = document.getElementById("monthlyChart").getContext('2d');
        var myPieChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ["Doanh thu", "Số vé", "Phim mới"],
                datasets: [{
                    data: [
                        {{ $monthlyChartData['monthRevenue'] ?? 0 }},
                        {{ $monthlyChartData['monthTickets'] ?? 0 }},
                        {{ $monthlyChartData['newMovies'] ?? 0 }}
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var label = data.labels[tooltipItem.index];
                            var value = data.datasets[0].data[tooltipItem.index];

                            if (label === 'Doanh thu') {
                                return label + ': ' + value.toLocaleString('vi-VN') + ' VND';
                            } else if (label === 'Số vé') {
                                return label + ': ' + value.toLocaleString('vi-VN') + ' vé';
                            } else {
                                return label + ': ' + value.toLocaleString('vi-VN') + ' phim';
                            }
                        }
                    }
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });

        // Biểu đồ đường với dữ liệu thực
        var ctxLine = document.getElementById("revenueTrendChart").getContext('2d');
        var myLineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: @json($revenueTrendData['monthLabels'] ?? []),
                datasets: [{
                    label: "Doanh thu (VND)",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($revenueTrendData['revenueData'] ?? []),
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function (value, index, values) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'K';
                                }
                                return value.toLocaleString('vi-VN') + ' VND';
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFont: {
                            size: 14
                        },
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: {
                            x: 15,
                            y: 15
                        },
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>

    <style>
        .card {
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .badge.rounded-circle {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-warning.bg-opacity-10 {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
    </style>
@endsection