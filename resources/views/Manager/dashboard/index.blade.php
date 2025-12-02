@extends('manager.layouts.app')

@section('title', 'Tổng Quan Quản Lý')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <i class="fas fa-tv fs-5"></i>
                                </div>
                                <div>
                                    <h1 class="h3 mb-0 fw-bold">Dashboard</h1>
                                    <p class="text-muted mb-0">Xin chào, <span class="text-primary fw-semibold">{{ Auth::user()->manager->FullName ?? 'Quản lý' }}</span></p>
                                </div>
                            </div>
                            <p class="text-muted mb-0" id="current-time">
                                Tổng quan hiệu suất hệ thống • {{ now()->format('l, d/m/Y • H:i') }}
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <button onclick="window.location.reload()" 
                                    class="btn btn-outline-primary d-flex align-items-center">
                                <i class="fas fa-sync-alt me-2"></i>
                                Làm mới
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-4 mb-4">
        <!-- Today's Revenue Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-uppercase text-muted small fw-semibold mb-1">Doanh thu hôm nay</p>
                            <h2 class="fw-bold mb-0">{{ number_format($todayRevenue) }} <small class="fs-6 text-muted">VND</small></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-coins text-success fs-4"></i>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-ticket-alt text-success small"></i>
                            </div>
                            <h5 class="fw-bold mb-1">{{ $todayTickets }}</h5>
                            <p class="text-muted small mb-0">Vé</p>
                        </div>
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 32px; height: 32px;">
                                <i class="fas fa-shopping-cart text-primary small"></i>
                            </div>
                            <h5 class="fw-bold mb-1">{{ $todayBookings }}</h5>
                            <p class="text-muted small mb-0">Booking</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-top">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-day text-muted me-2"></i>
                        <small class="text-muted">Cập nhật mới nhất</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Revenue Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-uppercase text-muted small fw-semibold mb-1">Doanh thu tuần này</p>
                            <h2 class="fw-bold mb-0">{{ number_format($weekRevenue) }} <small class="fs-6 text-muted">VND</small></h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-chart-bar text-primary fs-4"></i>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-ticket-alt text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $weekTickets }} vé bán ra</h5>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-arrow-up text-success me-1 small"></i>
                                <small class="text-muted">Tăng so với tuần trước</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-top">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Tuần {{ now()->weekOfYear }}</small>
                        <small class="text-primary fw-semibold">{{ now()->startOfWeek()->format('d/m') }} - {{ now()->endOfWeek()->format('d/m') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Movies Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-uppercase text-muted small fw-semibold mb-1">Phim đang chiếu</p>
                            <h2 class="fw-bold mb-0">{{ $activeMovies }}</h2>
                        </div>
                        <div class="bg-purple bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-film text-purple fs-4"></i>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-purple bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-users text-purple"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ $totalCustomers }}</h5>
                            <p class="text-muted small mb-0">Khách hàng</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-top">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-video text-muted me-2"></i>
                        <small class="text-muted">Trong hệ thống</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Overview Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-uppercase text-muted small fw-semibold mb-1">Hệ thống rạp</p>
                            <h2 class="fw-bold mb-0">{{ $totalTheaters }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fas fa-building text-warning fs-4"></i>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light rounded p-3 text-center">
                                <h4 class="fw-bold mb-1">{{ $totalRooms }}</h4>
                                <p class="text-muted small mb-0">Phòng chiếu</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-3 text-center">
                                <h4 class="fw-bold mb-1">{{ $totalCustomers }}</h4>
                                <p class="text-muted small mb-0">Khách hàng</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-top">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        <small class="text-muted">Toàn hệ thống</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-chart-line text-primary"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Doanh Thu Theo Giờ</h5>
                                <p class="text-muted small mb-0">Hôm nay • {{ now()->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2" style="width: 12px; height: 12px;"></span>
                            <small class="text-muted">Doanh thu (VND)</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="hourlyRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Rated Movies -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-crown text-warning"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Top Đánh Giá</h5>
                            <p class="text-muted small mb-0">Phim được yêu thích</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        @forelse($topRatedMovies as $index => $movie)
                        <div class="list-group-item border-0 px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="position-relative me-3">
                                    @if($movie->poster_url)
                                    <img src="{{ $movie->poster_url }}" 
                                         alt="{{ $movie->movie_title }}"
                                         class="rounded" style="width: 56px; height: 80px; object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 56px; height: 80px;">
                                        <i class="fas fa-film text-muted"></i>
                                    </div>
                                    @endif
                                    <div class="position-absolute top-0 start-0 translate-middle bg-gradient-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1 text-truncate">{{ $movie->movie_title }}</h6>
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="d-flex me-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($movie->avg_rating))
                                                    <i class="fas fa-star text-warning small me-1"></i>
                                                @elseif($i - 0.5 <= $movie->avg_rating)
                                                    <i class="fas fa-star-half-alt text-warning small me-1"></i>
                                                @else
                                                    <i class="far fa-star text-muted small me-1"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="fw-bold text-dark">{{ number_format($movie->avg_rating, 1) }}</span>
                                    </div>
                                    <small class="text-muted">{{ $movie->review_count }} đánh giá</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                <i class="fas fa-star text-muted fs-4"></i>
                            </div>
                            <p class="text-muted mb-0">Chưa có đánh giá nào</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="row g-4">
        <!-- Recent Bookings -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-receipt text-success"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Booking Gần Đây</h5>
                                <p class="text-muted small mb-0">Giao dịch mới nhất</p>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark">{{ count($recentBookings) }} giao dịch</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentBookings as $booking)
                        <div class="list-group-item border-0 px-4 py-3 hover-bg">
                            <div class="d-flex align-items-center">
                                <div class="bg-gradient-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ substr($booking->customer->FullName ?? 'K', 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 text-truncate">{{ $booking->customer->FullName ?? 'Khách' }}</h6>
                                        <span class="text-success fw-bold">{{ number_format($booking->TotalAmount) }} VND</span>
                                    </div>
                                    <p class="text-muted small mb-1 text-truncate">{{ $booking->showtime->movie->Title ?? 'N/A' }}</p>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted me-3">
                                            <i class="far fa-clock me-1"></i>{{ $booking->created_at->format('H:i') }}
                                        </small>
                                        <span class="badge bg-light text-dark small">
                                            Đã thanh toán
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                <i class="fas fa-shopping-cart text-muted fs-4"></i>
                            </div>
                            <p class="text-muted mb-0">Chưa có booking nào</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Seat Type Stats -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-chair text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Loại Ghế Bán Chạy</h5>
                            <p class="text-muted small mb-0">Hôm nay</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($seatTypeStats as $stat)
                    @php
                        $totalSeats = $seatTypeStats->sum('count');
                        $percentage = $totalSeats > 0 ? ($stat->count / $totalSeats) * 100 : 0;
                        $colors = [
                            'Standard' => ['bg' => 'bg-primary', 'text' => 'text-primary'],
                            'VIP' => ['bg' => 'bg-purple', 'text' => 'text-purple'],
                            'Couple' => ['bg' => 'bg-pink', 'text' => 'text-pink']
                        ];
                        $color = $colors[$stat->SeatType] ?? ['bg' => 'bg-secondary', 'text' => 'text-secondary'];
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="{{ str_replace('text-', 'bg-', $color['text']) }} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                    <i class="fas fa-chair {{ $color['text'] }} small"></i>
                                </div>
                                <span class="fw-semibold">
                                    @if($stat->SeatType == 'Standard') Ghế Thường
                                    @elseif($stat->SeatType == 'VIP') Ghế VIP
                                    @elseif($stat->SeatType == 'Couple') Ghế Đôi
                                    @else {{ $stat->SeatType }}
                                    @endif
                                </span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ $stat->count }} vé</div>
                                <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $color['bg'] }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                            <i class="fas fa-chair text-muted fs-4"></i>
                        </div>
                        <p class="text-muted mb-0">Chưa có vé nào bán hôm nay</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Upcoming Showtimes -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-calendar-alt text-info"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Lịch Chiếu Sắp Tới</h5>
                                <p class="text-muted small mb-0">{{ count($upcomingShowtimes) }} lịch chiếu</p>
                            </div>
                        </div>
                        <a href="{{ route('manager.showtimes.index') }}" class="text-decoration-none">
                            <small class="text-primary fw-semibold">Xem tất cả →</small>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingShowtimes as $showtime)
                        <div class="list-group-item border-0 px-4 py-3 hover-bg">
                            <div class="d-flex">
                                @if($showtime->movie->PosterURL)
                                <img src="{{ $showtime->movie->PosterURL }}" 
                                     alt="{{ $showtime->movie->Title }}"
                                     class="rounded me-3" style="width: 48px; height: 64px; object-fit: cover;">
                                @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 64px;">
                                    <i class="fas fa-film text-muted"></i>
                                </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $showtime->movie->Title }}</h6>
                                    <div class="d-flex align-items-center mb-2">
                                        <small class="text-muted me-2">{{ $showtime->room->theater->Name }}</small>
                                        <span class="badge bg-light text-dark small">
                                            {{ $showtime->room->RoomName }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-clock text-muted me-2 small"></i>
                                            <span class="fw-semibold">{{ $showtime->StartTime->format('H:i') }}</span>
                                            <small class="text-muted ms-2">{{ $showtime->StartTime->format('d/m') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-success fw-bold">{{ number_format($showtime->Price) }}</div>
                                            <small class="text-muted">VND</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                <i class="fas fa-film text-muted fs-4"></i>
                            </div>
                            <p class="text-muted mb-0">Không có lịch chiếu sắp tới</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-white bg-opacity-80 d-flex align-items-center justify-content-center d-none" style="z-index: 9999;">
    <div class="text-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted fw-semibold mt-3">Đang tải dữ liệu...</p>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="position-fixed bottom-0 end-0 p-3 d-none" style="z-index: 9999;">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2"></i>
            <strong class="me-auto">Thành công</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-message">
            Thành công!
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Custom Colors */
.bg-purple {
    background-color: #6f42c1 !important;
}

.text-purple {
    color: #6f42c1 !important;
}

.bg-pink {
    background-color: #d63384 !important;
}

.text-pink {
    color: #d63384 !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%) !important;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%) !important;
}

/* Hover Effects */
.hover-shadow {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-shadow:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.hover-bg:hover {
    background-color: rgba(0, 0, 0, 0.02) !important;
}

/* Card Styling */
.card {
    border-radius: 12px !important;
}

/* Progress Bar */
.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* List Group Items */
.list-group-item {
    transition: background-color 0.2s ease;
}

/* Animation for fade in */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade {
    animation: fadeIn 0.5s ease-out;
}

/* Print Styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    
    .btn {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo biểu đồ
    initHourlyRevenueChart();
    
    // Cập nhật thời gian thực
    updateRealTime();
    
    // Xử lý sự kiện
    initEventHandlers();
    
    // Auto-refresh
    startAutoRefresh();
});

function initHourlyRevenueChart() {
    const ctx = document.getElementById('hourlyRevenueChart');
    if (!ctx) return;
    
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: @json(array_column($hourlyRevenue, 'hour')),
            datasets: [{
                label: 'Doanh thu',
                data: @json(array_column($hourlyRevenue, 'revenue')),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '💰 ' + context.parsed.y.toLocaleString('vi-VN') + ' VND';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                            if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

function updateRealTime() {
    const timeElement = document.getElementById('current-time');
    if (!timeElement) return;
    
    function updateTime() {
        const now = new Date();
        const dateStr = now.toLocaleDateString('vi-VN', { 
            weekday: 'long', 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit' 
        }) + ' • ' + now.toLocaleTimeString('vi-VN', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        timeElement.textContent = `Tổng quan hiệu suất hệ thống • ${dateStr}`;
    }
    
    updateTime();
    setInterval(updateTime, 60000);
}

function initEventHandlers() {
    // Print dashboard
    document.getElementById('print-dashboard')?.addEventListener('click', function() {
        window.print();
        showToast('Đang chuẩn bị báo cáo để in...');
    });
    
    // Refresh button
    document.querySelector('button[onclick="window.location.reload()"]')?.addEventListener('click', function(e) {
        e.preventDefault();
        refreshDashboard();
    });
    
    // Click on items
    document.querySelectorAll('.list-group-item[data-booking-id]').forEach(item => {
        item.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            window.location.href = `/manager/bookings/${bookingId}`;
        });
    });
}

function refreshDashboard() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) loadingOverlay.classList.remove('d-none');
    
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Dữ liệu đã được cập nhật');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Refresh error:', error);
        showToast('Có lỗi xảy ra khi làm mới dữ liệu', 'error');
    })
    .finally(() => {
        if (loadingOverlay) loadingOverlay.classList.add('d-none');
    });
}

function startAutoRefresh() {
    // Auto-refresh mỗi 5 phút
    setInterval(() => {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Dữ liệu đã được cập nhật tự động');
            }
        });
    }, 5 * 60 * 1000);
}

function showToast(message, type = 'success') {
    const toastEl = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    if (!toastEl || !toastMessage) return;
    
    // Update message
    toastMessage.textContent = message;
    
    // Update style
    const toastHeader = toastEl.querySelector('.toast-header');
    if (type === 'error') {
        toastHeader.className = 'toast-header bg-danger text-white';
    } else {
        toastHeader.className = 'toast-header bg-success text-white';
    }
    
    // Show toast
    toastEl.classList.remove('d-none');
    
    // Hide after 3 seconds
    setTimeout(() => {
        toastEl.classList.add('d-none');
    }, 3000);
}
</script>
@endpush