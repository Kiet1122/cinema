@extends('Manager.layouts.app')

@section('title', 'Chỉnh Sửa Booking - #' . str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Chỉnh Sửa Thông Tin Booking</h1>
                    <p class="text-muted">Cập nhật thông tin chi tiết về đặt vé</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.bookings.show', $booking->BookingID) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay Lại
                    </a>
                    <a href="{{ route('manager.bookings.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>Danh Sách
                    </a>
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

            <!-- Form chỉnh sửa -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Thông Tin Booking #{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('manager.bookings.update', $booking->BookingID) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Thông tin cơ bản -->
                            <div class="col-md-6">
                                <h6 class="fw-bold text-gray-800 mb-3 border-bottom pb-2">
                                    <i class="fas fa-info-circle me-2"></i>Thông Tin Cơ Bản
                                </h6>

                                <!-- Mã Booking -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-gray-700">Mã Booking</label>
                                    <input type="text" class="form-control bg-light" value="#{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}" readonly>
                                    <small class="form-text text-muted">Mã booking không thể thay đổi</small>
                                </div>

                                <!-- Tổng tiền -->
                                <div class="mb-3">
                                    <label for="TotalAmount" class="form-label fw-bold text-gray-700">Tổng Tiền <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="TotalAmount" 
                                               id="TotalAmount"
                                               class="form-control @error('TotalAmount') is-invalid @enderror"
                                               value="{{ old('TotalAmount', $booking->TotalAmount) }}"
                                               min="0"
                                               step="1000"
                                               required>
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                    @error('TotalAmount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Trạng thái -->
                                <div class="mb-3">
                                    <label for="Status" class="form-label fw-bold text-gray-700">Trạng Thái <span class="text-danger">*</span></label>
                                    <select name="Status" id="Status" class="form-select @error('Status') is-invalid @enderror" required>
                                        <option value="Created" {{ old('Status', $booking->Status) == 'Created' ? 'selected' : '' }}>⏳ Chờ Xử Lý</option>
                                        <option value="Confirmed" {{ old('Status', $booking->Status) == 'Confirmed' ? 'selected' : '' }}>✅ Đã Xác Nhận</option>
                                        <option value="Cancelled" {{ old('Status', $booking->Status) == 'Cancelled' ? 'selected' : '' }}>❌ Đã Hủy</option>
                                    </select>
                                    @error('Status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Trạng thái thanh toán -->
                                <div class="mb-3">
                                    <label for="PaymentStatus" class="form-label fw-bold text-gray-700">Trạng Thái Thanh Toán <span class="text-danger">*</span></label>
                                    <select name="PaymentStatus" id="PaymentStatus" class="form-select @error('PaymentStatus') is-invalid @enderror" required>
                                        <option value="Pending" {{ old('PaymentStatus', $booking->PaymentStatus) == 'Pending' ? 'selected' : '' }}>⏳ Chờ Thanh Toán</option>
                                        <option value="Paid" {{ old('PaymentStatus', $booking->PaymentStatus) == 'Paid' ? 'selected' : '' }}>✅ Đã Thanh Toán</option>
                                        <option value="Failed" {{ old('PaymentStatus', $booking->PaymentStatus) == 'Failed' ? 'selected' : '' }}>❌ Thanh Toán Thất Bại</option>
                                        <option value="Refunded" {{ old('PaymentStatus', $booking->PaymentStatus) == 'Refunded' ? 'selected' : '' }}>↩️ Đã Hoàn Tiền</option>
                                    </select>
                                    @error('PaymentStatus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Thông tin liên quan -->
                            <div class="col-md-6">
                                <h6 class="fw-bold text-gray-800 mb-3 border-bottom pb-2">
                                    <i class="fas fa-link me-2"></i>Thông Tin Liên Quan
                                </h6>

                                <!-- Thông tin khách hàng -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-gray-700">Khách Hàng</label>
                                    <div class="bg-light p-3 rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle text-gray-400 me-3 fa-lg"></i>
                                            <div>
                                                <strong class="text-gray-800">{{ $booking->customer->FullName ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $booking->customer->user->Email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Thông tin phim -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-gray-700">Phim & Suất Chiếu</label>
                                    <div class="bg-light p-3 rounded">
                                        @if($booking->showtime && $booking->showtime->movie)
                                            <strong class="text-gray-800">{{ $booking->showtime->movie->Title }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('H:i d/m/Y') }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-theater-masks me-1"></i>
                                                {{ $booking->showtime->room->theater->Name ?? 'N/A' }}
                                            </small>
                                        @else
                                            <span class="text-muted">Thông tin phim không khả dụng</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Thông tin ghế -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-gray-700">Ghế Đã Đặt</label>
                                    <div class="bg-light p-3 rounded">
                                        @if($booking->bookingDetails && $booking->bookingDetails->count() > 0)
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($booking->bookingDetails as $detail)
                                                    <span class="badge 
                                                        @if($detail->seat->SeatType == 'VIP') bg-warning text-dark
                                                        @elseif($detail->seat->SeatType == 'Couple') bg-danger
                                                        @else bg-primary @endif">
                                                        {{ $detail->seat->SeatNumber }} ({{ $detail->seat->SeatType }})
                                                    </span>
                                                @endforeach
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                Tổng: {{ number_format($booking->bookingDetails->sum('Price'), 0, ',', '.') }} VNĐ
                                            </small>
                                        @else
                                            <span class="text-muted">Không có thông tin ghế</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Thông tin voucher -->
                                @if($booking->voucher)
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-gray-700">Mã Giảm Giá</label>
                                    <div class="bg-light p-3 rounded">
                                        <span class="badge bg-success">{{ $booking->voucher->Code }}</span>
                                        <small class="text-muted d-block mt-1">
                                            Đã áp dụng cho booking này
                                        </small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Thông tin thanh toán -->
                        @if($booking->payment)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-gray-800 mb-3 border-bottom pb-2">
                                    <i class="fas fa-credit-card me-2"></i>Thông Tin Thanh Toán
                                </h6>
                                <div class="bg-light p-4 rounded">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong class="text-gray-700">Phương thức:</strong><br>
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
                                        </div>
                                        <div class="col-md-4">
                                            <strong class="text-gray-700">Số tiền:</strong><br>
                                            <span class="text-success">{{ number_format($booking->payment->Amount, 0, ',', '.') }} VNĐ</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong class="text-gray-700">Ngày thanh toán:</strong><br>
                                            @if($booking->payment->PaymentDate)
                                                {{ \Carbon\Carbon::parse($booking->payment->PaymentDate)->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">Chưa thanh toán</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Thông tin hệ thống -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="fw-bold text-gray-800 mb-3 border-bottom pb-2">
                                    <i class="fas fa-database me-2"></i>Thông Tin Hệ Thống
                                </h6>
                                <div class="bg-light p-3 rounded">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong class="text-gray-700">Ngày tạo:</strong><br>
                                            <small class="text-muted">
                                                {{ $booking->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="col-md-4">
                                            <strong class="text-gray-700">Cập nhật cuối:</strong><br>
                                            <small class="text-muted">
                                                {{ $booking->updated_at}}
                                            </small>
                                        </div>
                                        <div class="col-md-4">
                                            <strong class="text-gray-700">Booking ID:</strong><br>
                                            <small class="text-muted">{{ $booking->BookingID }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-2"></i>Cập Nhật Thông Tin
                                        </button>
                                        <a href="{{ route('manager.bookings.show', $booking->BookingID) }}" class="btn btn-outline-secondary ms-2">
                                            <i class="fas fa-times me-2"></i>Hủy
                                        </a>
                                    </div>
                                    <div>
                                        <a href="{{ route('manager.bookings.updateStatus', $booking->BookingID) }}" class="btn btn-outline-warning me-2">
                                            <i class="fas fa-sync me-2"></i>Chỉ Cập Nhật Trạng Thái
                                        </a>
                                        <form action="{{ route('manager.bookings.destroy', $booking->BookingID) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn booking này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash me-2"></i>Xóa Booking
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.card {
    border: none;
    border-radius: 0.5rem;
}
.bg-light {
    background-color: #f8f9fa !important;
}
.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
}
.form-label {
    font-weight: 600;
    color: #374151;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tự động định dạng số tiền khi nhập
    const totalAmountInput = document.getElementById('TotalAmount');
    totalAmountInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseInt(this.value).toLocaleString('vi-VN');
        }
    });

    totalAmountInput.addEventListener('focus', function() {
        if (this.value) {
            this.value = this.value.replace(/\./g, '');
        }
    });

    // Hiển thị cảnh báo khi chọn trạng thái hủy
    const statusSelect = document.getElementById('Status');
    statusSelect.addEventListener('change', function() {
        if (this.value === 'Cancelled') {
            if (!confirm('Bạn có chắc chắn muốn hủy booking này? Hành động này có thể ảnh hưởng đến thống kê và không thể hoàn tác.')) {
                this.value = '{{ $booking->Status }}';
            }
        }
    });

    // Hiển thị cảnh báo khi chọn trạng thái thanh toán thất bại
    const paymentStatusSelect = document.getElementById('PaymentStatus');
    paymentStatusSelect.addEventListener('change', function() {
        if (this.value === 'Failed') {
            if (!confirm('Xác nhận đánh dấu thanh toán thất bại? Khách hàng sẽ được thông báo về việc này.')) {
                this.value = '{{ $booking->PaymentStatus }}';
            }
        }
    });
});
</script>
@endsection