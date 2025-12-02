@extends('manager.layouts.app')

@section('title', 'Chi Tiết Voucher: ' . $voucher->Code)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-ticket-alt text-primary me-2"></i>Chi Tiết Voucher
                    </h1>
                    <p class="text-muted mb-0">Thông tin chi tiết về mã giảm giá</p>
                </div>
                <a href="{{ route('manager.vouchers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                </a>
            </div>

            <!-- Thông báo -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {!! session('success') !!}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {!! session('error') !!}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Card chính -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-gray-800">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            Mã: <code class="fs-4">{{ $voucher->Code }}</code>
                        </h5>
                        @php
                            $status = $voucher->Status;
                            $isExpired = $voucher->EndDate && \Carbon\Carbon::parse($voucher->EndDate)->isPast();
                            
                            if ($status == 'Active' && !$isExpired) {
                                $statusClass = 'bg-success';
                                $statusIcon = 'fa-check-circle';
                                $statusText = 'Đang Hoạt Động';
                            } elseif ($isExpired) {
                                $statusClass = 'bg-secondary';
                                $statusIcon = 'fa-clock';
                                $statusText = 'Đã Hết Hạn';
                            } else {
                                $statusClass = 'bg-danger';
                                $statusIcon = 'fa-times-circle';
                                $statusText = 'Ngừng Hoạt Động';
                            }
                        @endphp
                        <span class="badge {{ $statusClass }} px-3 py-2 fs-6">
                            <i class="fas {{ $statusIcon }} me-1"></i>{{ $statusText }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Thông tin giảm giá -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <h6 class="mb-0 text-gray-700">
                                        <i class="fas fa-percentage me-2 text-warning"></i>Thông Tin Giảm Giá
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Loại giảm giá</small>
                                            <strong class="text-gray-800">
                                                @if($voucher->DiscountType == 'Percent')
                                                    Phần trăm
                                                @else
                                                    Số tiền cố định
                                                @endif
                                            </strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Giá trị</small>
                                            <strong class="text-success fs-5">
                                                @if($voucher->DiscountType == 'Percent')
                                                    {{ number_format($voucher->Value) }}%
                                                @else
                                                    {{ number_format($voucher->Value, 0, ',', '.') }}₫
                                                @endif
                                            </strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Giới hạn sử dụng</small>
                                            <strong class="text-gray-800">
                                                {{ $voucher->UsageLimit ? number_format($voucher->UsageLimit) . ' lần' : 'Không giới hạn' }}
                                            </strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Giới hạn mỗi người</small>
                                            <strong class="text-gray-800">
                                                {{ $voucher->PerUserLimit ? number_format($voucher->PerUserLimit) . ' lần' : 'Không giới hạn' }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin thời gian -->
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <h6 class="mb-0 text-gray-700">
                                        <i class="fas fa-calendar-alt me-2 text-info"></i>Thời Gian Hiệu Lực
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <small class="text-muted d-block">Ngày bắt đầu</small>
                                            <strong class="text-gray-800">
                                                <i class="far fa-calendar-plus me-1 text-success"></i>
                                                {{ \Carbon\Carbon::parse($voucher->StartDate)->format('d/m/Y H:i') }}
                                            </strong>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted d-block">Ngày kết thúc</small>
                                            <strong class="text-gray-800 {{ $isExpired ? 'text-danger' : '' }}">
                                                <i class="far fa-calendar-times me-1 {{ $isExpired ? 'text-danger' : 'text-success' }}"></i>
                                                {{ \Carbon\Carbon::parse($voucher->EndDate)->format('d/m/Y H:i') }}
                                                @if ($isExpired)
                                                    <span class="badge bg-danger ms-2">Đã hết hạn</span>
                                                @endif
                                            </strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Ngày tạo</small>
                                            <span class="text-gray-600">
                                                {{ \Carbon\Carbon::parse($voucher->created_at)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Cập nhật cuối</small>
                                            <span class="text-gray-600">
                                                {{ \Carbon\Carbon::parse($voucher->updated_at)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Phần tặng voucher -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <h6 class="text-gray-700 mb-2">
                                        <i class="fas fa-users me-2 text-primary"></i>Thống Kê Phân Phối
                                    </h6>
                                    <div class="d-flex align-items-center">
                                        <div class="me-4">
                                            <small class="text-muted d-block">Đã tặng cho</small>
                                            <strong class="text-primary fs-4">{{ number_format($assignedCount) }}</strong>
                                            <small class="text-muted"> thành viên</small>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Tổng thành viên</small>
                                            <strong class="text-gray-800 fs-6">{{ number_format(\App\Models\Customer::count()) }}</strong>
                                            <small class="text-muted"> thành viên</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <form action="{{ route('manager.vouchers.assignAll') }}" method="POST" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn tặng voucher {{ $voucher->Code }} này cho TẤT CẢ thành viên?');">
                                        @csrf
                                        <input type="hidden" name="voucher_id" value="{{ $voucher->VoucherID }}">
                                        <button type="submit" class="btn btn-primary w-100 w-md-auto">
                                            <i class="fas fa-gift me-2"></i>Tặng cho tất cả
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nút hành động -->
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('manager.vouchers.edit', $voucher) }}"
                           class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa
                        </a>
                        <form action="{{ route('manager.vouchers.destroy', $voucher) }}" method="POST" 
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa voucher này? Hành động này không thể hoàn tác.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-2"></i>Xóa voucher
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 0.5rem;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

code {
    background-color: #f1f3f4;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-family: 'Courier New', monospace;
    color: #e83e8c;
}

.alert {
    border-radius: 0.5rem;
    border: none;
}

.text-gray-700 {
    color: #4a5568;
}

.text-gray-800 {
    color: #2d3748;
}

.fs-4 {
    font-size: 1.5rem !important;
}

.fs-6 {
    font-size: 1rem !important;
}
</style>
@endsection