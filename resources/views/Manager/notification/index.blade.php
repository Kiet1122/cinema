@extends('Manager.layouts.app')

@section('title', 'Quản Lý Thông Báo')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Gửi Thông Báo</h1>
            <p class="text-muted">Gửi thông báo đến khách hàng của bạn</p>
        </div>
        <a href="{{ route('manager.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Thông báo Flash -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form gửi thông báo -->
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-paper-plane me-2"></i>Soạn Thông Báo
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('manager.notification.send') }}" id="notification-form">
                        @csrf

                        <!-- Tiêu đề -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-gray-700">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" required 
                                   placeholder="Nhập tiêu đề thông báo"
                                   maxlength="255"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-end">
                                <small><span id="title-length">0</span>/255 ký tự</small>
                            </div>
                        </div>

                        <!-- Nội dung -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-gray-700">Nội dung <span class="text-danger">*</span></label>
                            <textarea name="content" rows="6" required
                                      placeholder="Nhập nội dung thông báo..."
                                      class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Người nhận -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-gray-700">Gửi đến <span class="text-danger">*</span></label>
                            <select name="customer_id" required
                                    class="form-select @error('customer_id') is-invalid @enderror"
                                    id="customer-select">
                                <option value="all">📢 Tất cả khách hàng</option>
                                @if($customers->count() > 0)
                                    <optgroup label="Khách hàng cụ thể">
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->CustomerID }}" {{ old('customer_id') == $customer->CustomerID ? 'selected' : '' }}>
                                            👤 {{ $customer->FullName }} 
                                            @if($customer->user)
                                                - {{ $customer->user->Email }}
                                            @endif
                                        </option>
                                    @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Thống kê -->
                        <div class="bg-light rounded p-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    Sẽ gửi đến: 
                                    <span id="recipient-count">{{ $totalCustomers }}</span> người
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Thời gian: {{ now()->format('H:i d/m/Y') }}
                                </small>
                            </div>
                        </div>

                        <!-- Nút gửi -->
                        <button type="submit" class="btn btn-primary w-100 py-3" id="submit-button">
                            <i class="fas fa-paper-plane me-2"></i>
                            <span id="submit-text">Gửi thông báo</span>
                            <div id="submit-spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="fw-semibold text-gray-700 mb-3">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>Mẹo gửi thông báo hiệu quả
                    </h6>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2">• Tiêu đề ngắn gọn, thu hút sự chú ý</li>
                        <li class="mb-2">• Nội dung rõ ràng, dễ hiểu</li>
                        <li class="mb-2">• Kiểm tra kỹ trước khi gửi</li>
                        <li>• Chọn đúng đối tượng nhận</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('notification-form');
    const submitButton = document.getElementById('submit-button');
    const submitText = document.getElementById('submit-text');
    const submitSpinner = document.getElementById('submit-spinner');
    const titleInput = document.querySelector('input[name="title"]');
    const titleLength = document.getElementById('title-length');
    const customerSelect = document.getElementById('customer-select');
    const recipientCount = document.getElementById('recipient-count');
    const totalCustomers = {{ $totalCustomers }};

    // Đếm ký tự tiêu đề
    titleInput.addEventListener('input', function() {
        titleLength.textContent = this.value.length;
    });

    // Cập nhật số người nhận
    customerSelect.addEventListener('change', function() {
        if (this.value === 'all') {
            recipientCount.textContent = totalCustomers;
        } else {
            recipientCount.textContent = '1';
        }
    });

    // Xử lý gửi form
    form.addEventListener('submit', function(event) {
        // Hiển thị loading
        submitButton.disabled = true;
        submitText.textContent = 'Đang gửi...';
        submitSpinner.classList.remove('d-none');
        
        // Form sẽ được gửi bình thường, không dùng AJAX
        // Nếu muốn dùng AJAX, có thể sử dụng route API
    });

    // Khởi tạo
    titleLength.textContent = titleInput.value.length;
});
</script>

<style>
.card {
    border-radius: 10px;
}
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 12px;
}
.form-control:focus, .form-select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
}
.btn-primary {
    border-radius: 8px;
    background-color: #4f46e5;
    border: none;
    padding: 12px 20px;
    font-weight: 600;
}
.btn-primary:hover:not(:disabled) {
    background-color: #4338ca;
    transform: translateY(-1px);
    transition: all 0.2s;
}
.btn-primary:disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
}
.bg-light {
    background-color: #f8f9fa !important;
}
.alert {
    border-radius: 8px;
    border: none;
}
</style>
@endsection