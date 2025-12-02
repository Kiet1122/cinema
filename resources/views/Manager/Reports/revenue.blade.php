@extends('manager.layouts.app')

@section('title', 'Báo Cáo Doanh Thu Theo Tháng')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">Báo Cáo Doanh Thu Theo Tháng</h1>
                <p class="text-muted">Phân tích doanh thu hệ thống theo từng tháng</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Nút Xuất Excel -->
                @if(Route::has('manager.reports.export'))
                    <a href="{{ route('manager.reports.export', ['year' => $selectedYear]) }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @elseif(Route::has('reports.export'))
                    <a href="{{ route('reports.export', ['year' => $selectedYear]) }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @else
                    <a href="{{ url('/manager/reports/export/' . $selectedYear) }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @endif
            </div>
        </div>

        <!-- Bộ Lọc Năm -->
        <div class="card shadow mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('manager.reports.revenue') }}" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label fw-bold mb-0">Năm:</label>
                    </div>
                    <div class="col-auto">
                        <select name="year" class="form-select" onchange="this.form.submit()">
                            @foreach($availableYears as $yearItem)
                                <option value="{{ $yearItem }}" {{ $selectedYear == $yearItem ? 'selected' : '' }}>
                                    {{ $yearItem }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <span class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Hiển thị doanh thu 12 tháng
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thống Kê Tổng Quan -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng Doanh Thu Năm
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalRevenueYear) }} VND
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
                                    Tổng Số Vé Năm
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($totalTicketsYear) }}
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
                                    Trung Bình Mỗi Vé
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($avgRevenuePerTicket) }} VND
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
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
                                    Tháng Cao Nhất
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @php
                                        $maxMonth = collect($revenueData)->sortByDesc('revenue')->first();
                                        $maxMonthName = $maxMonth ? \Carbon\Carbon::createFromFormat('Y-m', $maxMonth['month'])->format('m/Y') : 'N/A';
                                    @endphp
                                    {{ $maxMonthName }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu Đồ Cột -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>Doanh Thu Theo Tháng
                </h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 400px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bảng Dữ Liệu -->
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>Dữ Liệu Chi Tiết
                </h6>
                @if(Route::has('manager.reports.export'))
                    <a href="{{ route('manager.reports.export', ['year' => $selectedYear]) }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @elseif(Route::has('reports.export'))
                    <a href="{{ route('reports.export', ['year' => $selectedYear]) }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @else
                    <a href="{{ url('/manager/reports/export/' . $selectedYear) }}" 
                       class="btn btn-success d-flex align-items-center px-4 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-file-excel me-2"></i>
                        Xuất Excel
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th width="10%">STT</th>
                                <th>Tháng</th>
                                <th>Doanh Thu</th>
                                <th>Số Vé</th>
                                <th>Số Booking</th>
                                <th>Trung Bình/Vé</th>
                                <th>Tỷ Lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = $totalRevenueYear;
                            @endphp
                            @foreach($revenueData as $index => $item)
                                @php
                                    $percentage = $total > 0 ? ($item['revenue'] / $total) * 100 : 0;
                                    $avgPerTicket = $item['ticket_count'] > 0 ? $item['revenue'] / $item['ticket_count'] : 0;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $item['month_label'] }}</td>
                                    <td class="text-end">{{ number_format($item['revenue']) }} VND</td>
                                    <td class="text-center">{{ number_format($item['ticket_count']) }}</td>
                                    <td class="text-center">{{ number_format($item['booking_count']) }}</td>
                                    <td class="text-end">{{ number_format($avgPerTicket) }} VND</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-muted small">{{ number_format($percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="2" class="fw-bold">TỔNG CỘNG</td>
                                <td class="fw-bold text-end text-success">{{ number_format($totalRevenueYear) }} VND</td>
                                <td class="fw-bold text-center">{{ number_format($totalTicketsYear) }}</td>
                                <td class="fw-bold text-center">{{ number_format(array_sum(array_column($revenueData, 'booking_count'))) }}</td>
                                <td class="fw-bold text-end">{{ number_format($avgRevenuePerTicket) }} VND</td>
                                <td>100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Dữ liệu từ Controller
        const revenueData = @json($revenueData);
        const labels = revenueData.map(item => item.month_label);
        const revenues = revenueData.map(item => item.revenue);
        const tickets = revenueData.map(item => item.ticket_count);

        let revenueChart;

        function initializeChart() {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            if (revenueChart) {
                revenueChart.destroy();
            }

            revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh Thu (VND)',
                        data: revenues,
                        backgroundColor: 'rgba(79, 70, 229, 0.8)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        yAxisID: 'y',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
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
                    },
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
                                callback: function(value) {
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
                                text: 'Tháng',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Khởi tạo biểu đồ
        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();
        });
    </script>

    <style>
        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(79, 70, 229, 0.05);
        }

        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .text-end {
            text-align: right;
        }
    </style>
@endsection