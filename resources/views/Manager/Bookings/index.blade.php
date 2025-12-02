@extends('Manager.layouts.app')

@section('title', 'Quản Lý Đặt Vé')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-ticket-alt text-primary me-2"></i>Quản Lý Đặt Vé
            </h1>
            <p class="text-muted mb-0">Quản lý và theo dõi tất cả giao dịch đặt vé của khách hàng</p>
        </div>
    </div>

    <!-- Thông báo Flash -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Bộ Lọc và Tìm Kiếm -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="m-0 font-weight-bold text-gray-800">
                <i class="fas fa-filter me-2 text-primary"></i>Bộ Lọc & Tìm Kiếm
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('manager.bookings.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <!-- Bộ Lọc Trạng Thái -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-gray-700">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                            <option value="Created" {{ request('status') == 'Created' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>

                    <!-- Thanh Tìm Kiếm -->
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-gray-700">Tìm kiếm</label>
                        <input type="text" name="search" id="search" 
                               placeholder="Mã booking, tên phim, tên khách hàng..." 
                               value="{{ request('search') }}"
                               class="form-control">
                    </div>

                    <!-- Nút hành động -->
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-search me-2"></i>Tìm kiếm
                        </button>
                        @if(request('status') != 'all' || request('search'))
                            <a href="{{ route('manager.bookings.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-2"></i>Xóa lọc
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        @php
            $totalCount = $bookings->total();
            $createdCount = $bookings->where('Status', 'Created')->count();
            $confirmedCount = $bookings->where('Status', 'Confirmed')->count();
            $cancelledCount = $bookings->where('Status', 'Cancelled')->count();
        @endphp
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng đơn hàng
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đã xác nhận
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $confirmedCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chờ xử lý
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $createdCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow-sm h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Đã hủy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $cancelledCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng Danh Sách Đặt Vé -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-gray-800">
                    <i class="fas fa-table me-2"></i>Danh Sách Đặt Vé
                </h6>
                <span class="badge bg-primary">Tổng: {{ $bookings->total() }} đơn</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($bookings->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy giao dịch đặt vé nào</h5>
                    <p class="text-muted">Hãy thử thay đổi điều kiện tìm kiếm hoặc bộ lọc</p>
                </div>
            @else
                <!-- Bảng không dùng scroll, hiển thị full width -->
                <table class="table table-hover mb-0 w-100">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Mã Booking</th>
                            <th>Khách hàng</th>
                            <th>Phim & Suất chiếu</th>
                            <th>Ghế</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                        <tr class="border-bottom">
                            <td class="ps-4">
                                <span class="fw-bold text-primary">#{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-medium text-gray-800">
                                        {{ $booking->customer->FullName ?? 'N/A' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $booking->customer->user->Email ?? 'N/A' }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-gray-800">
                                    {{ $booking->showtime->movie->Title ?? 'N/A' }}
                                </div>
                                <small class="text-muted">
                                    @if($booking->showtime && $booking->showtime->StartTime)
                                        {{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('H:i d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($booking->bookingDetails && $booking->bookingDetails->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($booking->bookingDetails as $detail)
                                            <span class="badge bg-light text-dark border">
                                                {{ $detail->seat->SeatNumber ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-success">
                                    {{ number_format($booking->TotalAmount, 0, ',', '.') }}₫
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'Created' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Chờ xử lý'],
                                        'Confirmed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Đã xác nhận'],
                                        'Cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Đã hủy'],
                                    ];
                                    $status = $statusConfig[$booking->Status] ?? ['class' => 'secondary', 'icon' => 'question-circle', 'text' => 'N/A'];
                                @endphp
                                <span class="badge bg-{{ $status['class'] }} bg-opacity-10 text-{{ $status['class'] }} border border-{{ $status['class'] }} border-opacity-25">
                                    <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                                    {{ $status['text'] }}
                                </span>
                            </td>
                            <td>
                                <div class="text-muted">
                                    <div>{{ $booking->created_at->format('d/m/Y') }}</div>
                                    <small>{{ $booking->created_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary border dropdown-toggle" 
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('manager.bookings.show', $booking->BookingID) }}">
                                                <i class="fas fa-eye me-2 text-info"></i>Xem chi tiết
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('manager.bookings.updateStatus', $booking->BookingID) }}" method="POST" class="dropdown-item p-0">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" onchange="this.form.submit()" 
                                                        class="form-select form-select-sm border-0 bg-transparent">
                                                    <option value="Created" {{ $booking->Status == 'Created' ? 'selected' : '' }}>Chờ xử lý</option>
                                                    <option value="Confirmed" {{ $booking->Status == 'Confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                                    <option value="Cancelled" {{ $booking->Status == 'Cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                                </select>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('manager.bookings.destroy', $booking->BookingID) }}" method="POST" 
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng #{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }} không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i>Xóa
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Phân Trang -->
        @if($bookings->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Hiển thị {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} của {{ $bookings->total() }} kết quả
                </div>
                <nav aria-label="Page navigation">
                    {{ $bookings->links() }}
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.card {
    border-radius: 0.5rem;
}

.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    padding: 1rem 0.75rem;
    background-color: #f8f9fa;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    white-space: nowrap;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.badge {
    font-weight: 500;
    font-size: 0.75rem;
}

.dropdown-menu {
    border-radius: 0.5rem;
    border: 1px solid #e3e6f0;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Đảm bảo bảng chiếm toàn bộ chiều rộng */
.container-fluid {
    max-width: 100%;
}

.card-body {
    overflow: visible;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto submit khi chọn trạng thái từ dropdown
    const statusSelects = document.querySelectorAll('select[name="status"]');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endsection