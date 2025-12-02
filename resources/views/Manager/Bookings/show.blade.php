@extends('Manager.layouts.app')

@section('title', 'Chi Tiết Đặt Vé #' . str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Chi Tiết Đặt Vé</h1>
            <p class="text-muted">Thông tin chi tiết về giao dịch đặt vé</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('manager.bookings.index') }}" class="btn btn-outline-primary d-flex align-items-center px-4 py-2">
                <i class="fas fa-arrow-left me-2"></i>
                Quay Lại Danh Sách
            </a>
            <button class="btn btn-primary d-flex align-items-center px-4 py-2" onclick="window.print()">
                <i class="fas fa-print me-2"></i>
                In Vé
            </button>
        </div>
    </div>

    <!-- Thông báo Flash -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Thông tin chính -->
        <div class="col-lg-8">
            <!-- Thông tin Booking -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-ticket-alt me-2"></i>Thông Tin Đặt Vé
                    </h6>
                    <div class="d-flex gap-2">
                        @php
                            $statusConfig = [
                                'Created' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Chờ Xử Lý'],
                                'Confirmed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Đã Xác Nhận'],
                                'Cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Đã Hủy'],
                            ];
                            $status = $statusConfig[$booking->Status] ?? ['class' => 'secondary', 'icon' => 'question-circle', 'text' => $booking->Status];
                            
                            $paymentStatusConfig = [
                                'Pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Chờ Thanh Toán'],
                                'Paid' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Đã Thanh Toán'],
                                'Failed' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Thanh Toán Thất Bại'],
                                'Refunded' => ['class' => 'info', 'icon' => 'undo', 'text' => 'Đã Hoàn Tiền'],
                            ];
                            $paymentStatus = $paymentStatusConfig[$booking->PaymentStatus] ?? ['class' => 'secondary', 'icon' => 'question-circle', 'text' => $booking->PaymentStatus];
                        @endphp
                        <span class="badge bg-{{ $paymentStatus['class'] }} d-flex align-items-center">
                            <i class="fas fa-{{ $paymentStatus['icon'] }} me-1"></i>
                            {{ $paymentStatus['text'] }}
                        </span>
                        <span class="badge bg-{{ $status['class'] }} d-flex align-items-center">
                            <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                            {{ $status['text'] }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">Mã Booking:</td>
                                    <td class="fw-bold text-primary">#{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Ngày đặt:</td>
                                    <td>
                                        <i class="fas fa-calendar me-1 text-muted"></i>
                                        {{ $booking->created_at->format('d/m/Y') }}
                                        <i class="fas fa-clock ms-2 me-1 text-muted"></i>
                                        {{ $booking->created_at->format('H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tổng tiền:</td>
                                    <td class="fw-bold text-success fs-5">{{ number_format($booking->TotalAmount, 0, ',', '.') }} VNĐ</td>
                                </tr>
                                @if($booking->voucher)
                                <tr>
                                    <td class="text-muted">Mã giảm giá:</td>
                                    <td>
                                        <span class="badge bg-success">{{ $booking->voucher->Code }}</span>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-muted" width="40%">Trạng thái:</td>
                                    <td>
                                        <form action="{{ route('manager.bookings.updateStatus', $booking->BookingID) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block w-auto">
                                                <option value="Created" {{ $booking->Status == 'Created' ? 'selected' : '' }}>Chờ Xử Lý</option>
                                                <option value="Confirmed" {{ $booking->Status == 'Confirmed' ? 'selected' : '' }}>Đã Xác Nhận</option>
                                                <option value="Cancelled" {{ $booking->Status == 'Cancelled' ? 'selected' : '' }}>Đã Hủy</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Thanh toán:</td>
                                    <td>
                                        @if($booking->payment)
                                        <form action="{{ route('manager.bookings.updatePaymentStatus', $booking->BookingID) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="payment_status" onchange="this.form.submit()" class="form-select form-select-sm d-inline-block w-auto">
                                                <option value="Pending" {{ $booking->PaymentStatus == 'Pending' ? 'selected' : '' }}>Chờ Thanh Toán</option>
                                                <option value="Paid" {{ $booking->PaymentStatus == 'Paid' ? 'selected' : '' }}>Đã Thanh Toán</option>
                                                <option value="Failed" {{ $booking->PaymentStatus == 'Failed' ? 'selected' : '' }}>Thất Bại</option>
                                                <option value="Refunded" {{ $booking->PaymentStatus == 'Refunded' ? 'selected' : '' }}>Đã Hoàn Tiền</option>
                                            </select>
                                        </form>
                                        @else
                                        <span class="text-muted">Chưa có thông tin thanh toán</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin Phim & Suất Chiếu -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-film me-2"></i>Thông Tin Phim & Suất Chiếu
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @if($booking->showtime && $booking->showtime->movie)
                                <img src="{{ asset('storage/movies/' . $booking->showtime->movie->PosterURL) }}" 
                                     alt="{{ $booking->showtime->movie->Title }}" 
                                     class="img-fluid rounded shadow-sm">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-film fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            @if($booking->showtime && $booking->showtime->movie)
                            <h4 class="text-gray-800 mb-3">{{ $booking->showtime->movie->Title }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" width="40%">Thời lượng:</td>
                                            <td>{{ $booking->showtime->movie->Duration }} phút</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Ngôn ngữ:</td>
                                            <td>{{ $booking->showtime->movie->Language }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Độ tuổi:</td>
                                            <td>{{ $booking->showtime->movie->AgeRestriction ? $booking->showtime->movie->AgeRestriction . '+' : 'Mọi lứa tuổi' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" width="40%">Rạp:</td>
                                            <td class="fw-bold">{{ $booking->showtime->room->theater->Name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Phòng:</td>
                                            <td>{{ $booking->showtime->room->RoomName ?? 'N/A' }} ({{ $booking->showtime->room->RoomType ?? 'N/A' }})</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Suất chiếu:</td>
                                            <td class="fw-bold text-primary">
                                                {{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('H:i d/m/Y') }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                                <p class="text-muted">Thông tin phim không khả dụng</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin Ghế -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chair me-2"></i>Thông Tin Ghế Đã Đặt
                    </h6>
                </div>
                <div class="card-body">
                    @if($booking->bookingDetails && $booking->bookingDetails->count() > 0)
                    <div class="row">
                        @foreach($booking->bookingDetails as $detail)
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-chair fa-2x 
                                            @if($detail->seat->SeatType == 'VIP') text-warning
                                            @elseif($detail->seat->SeatType == 'Couple') text-danger
                                            @else text-primary @endif">
                                        </i>
                                    </div>
                                    <h5 class="mb-1">{{ $detail->seat->SeatNumber }}</h5>
                                    <span class="badge 
                                        @if($detail->seat->SeatType == 'VIP') bg-warning text-dark
                                        @elseif($detail->seat->SeatType == 'Couple') bg-danger
                                        @else bg-primary @endif">
                                        {{ $detail->seat->SeatType }}
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted">{{ number_format($detail->Price, 0, ',', '.') }} VNĐ</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-chair fa-2x text-muted mb-3"></i>
                        <p class="text-muted">Không có thông tin ghế</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Thông tin Khách Hàng -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Thông Tin Khách Hàng
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-4x text-gray-400"></i>
                    </div>
                    <h5 class="text-gray-800">{{ $booking->customer->FullName ?? 'Khách hàng đã xóa' }}</h5>
                    <p class="text-muted mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        {{ $booking->customer->user->Email ?? 'N/A' }}
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fas fa-phone me-2"></i>
                        {{ $booking->customer->Phone ?? 'Chưa cập nhật' }}
                    </p>
                    @if($booking->customer->DateOfBirth)
                    <p class="text-muted">
                        <i class="fas fa-birthday-cake me-2"></i>
                        {{ \Carbon\Carbon::parse($booking->customer->DateOfBirth)->format('d/m/Y') }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Thông tin Thanh Toán -->
            @if($booking->payment)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-credit-card me-2"></i>Thông Tin Thanh Toán
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Phương thức:</td>
                            <td class="fw-bold">
                                @switch($booking->payment->PaymentMethod)
                                    @case('credit_card')
                                        <i class="fas fa-credit-card me-2"></i>Thẻ Tín Dụng
                                        @break
                                    @case('bank_transfer')
                                        <i class="fas fa-university me-2"></i>Chuyển Khoản
                                        @break
                                    @case('momo')
                                        <i class="fas fa-mobile-alt me-2"></i>MoMo
                                        @break
                                    @case('zalopay')
                                        <i class="fas fa-wallet me-2"></i>ZaloPay
                                        @break
                                    @default
                                        <i class="fas fa-money-bill-wave me-2"></i>Tiền Mặt
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Số tiền:</td>
                            <td class="fw-bold text-success">{{ number_format($booking->payment->Amount, 0, ',', '.') }} VNĐ</td>
                        </tr>
                        @if($booking->payment->PaymentDate)
                        <tr>
                            <td class="text-muted">Ngày thanh toán:</td>
                            <td>
                                <i class="fas fa-calendar me-1 text-muted"></i>
                                {{ \Carbon\Carbon::parse($booking->payment->PaymentDate)->format('d/m/Y') }}
                                <i class="fas fa-clock ms-2 me-1 text-muted"></i>
                                {{ \Carbon\Carbon::parse($booking->payment->PaymentDate)->format('H:i') }}
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            <!-- Hành động -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Hành Động
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('manager.bookings.edit', $booking->BookingID) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Chỉnh Sửa Thông Tin
                        </a>
                        <form action="{{ route('manager.bookings.destroy', $booking->BookingID) }}" method="POST" 
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đơn hàng này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Xóa Vĩnh Viễn
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
@media print {
    .btn, .dropdown, .form-select, .card-header .badge {
        display: none !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
.card {
    border: none;
    border-radius: 0.5rem;
}
.table-borderless td {
    padding: 0.5rem 0;
    border: none;
}
</style>
@endsection