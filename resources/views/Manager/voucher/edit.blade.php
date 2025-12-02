@extends('manager.layouts.app')

@section('title', 'Chỉnh sửa Voucher')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-edit text-primary me-2"></i>Chỉnh sửa Voucher
                    </h2>
                    <p class="text-muted mb-0">Cập nhật thông tin mã giảm giá</p>
                </div>
                <a href="{{ route('manager.vouchers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>

            <!-- Thông báo lỗi -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex">
                        <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                        <div>
                            <h6 class="alert-heading mb-2">Có lỗi xảy ra!</h6>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-gray-800">
                        <i class="fas fa-ticket-alt me-2 text-warning"></i>Thông tin Voucher
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('manager.vouchers.update', $voucher->VoucherID) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Code -->
                            <div class="col-md-6 mb-3">
                                <label for="Code" class="form-label fw-bold text-gray-700">
                                    Mã Voucher <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('Code') is-invalid @enderror" 
                                       id="Code" name="Code" value="{{ old('Code', $voucher->Code) }}" 
                                       placeholder="Nhập mã voucher" required>
                                @error('Code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Mã phải là duy nhất trong hệ thống.</small>
                            </div>

                            <!-- Discount Type -->
                            <div class="col-md-6 mb-3">
                                <label for="DiscountType" class="form-label fw-bold text-gray-700">
                                    Loại giảm giá <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('DiscountType') is-invalid @enderror" 
                                        id="DiscountType" name="DiscountType" required>
                                    <option value="">Chọn loại giảm giá</option>
                                    <option value="Percent" {{ (old('DiscountType', $voucher->DiscountType) == 'Percent') ? 'selected' : '' }}>
                                        Phần trăm (%)
                                    </option>
                                    <option value="FixedAmount" {{ (old('DiscountType', $voucher->DiscountType) == 'FixedAmount') ? 'selected' : '' }}>
                                        Số tiền cố định
                                    </option>
                                </select>
                                @error('DiscountType')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Value -->
                            <div class="col-md-6 mb-3">
                                <label for="Value" class="form-label fw-bold text-gray-700">
                                    Giá trị <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('Value') is-invalid @enderror" 
                                           id="Value" name="Value" value="{{ old('Value', $voucher->Value) }}" 
                                           placeholder="0.00" required>
                                    <span class="input-group-text bg-light" id="valueSuffix">
                                        @if($voucher->DiscountType === 'Percent')
                                            %
                                        @else
                                            ₫
                                        @endif
                                    </span>
                                </div>
                                @error('Value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="Status" class="form-label fw-bold text-gray-700">
                                    Trạng thái <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('Status') is-invalid @enderror" 
                                        id="Status" name="Status" required>
                                    <option value="">Chọn trạng thái</option>
                                    <option value="Active" {{ (old('Status', $voucher->Status) == 'Active') ? 'selected' : '' }}>
                                        <i class="fas fa-check-circle text-success me-2"></i>Hoạt động
                                    </option>
                                    <option value="Inactive" {{ (old('Status', $voucher->Status) == 'Inactive') ? 'selected' : '' }}>
                                        <i class="fas fa-times-circle text-secondary me-2"></i>Ngừng hoạt động
                                    </option>
                                </select>
                                @error('Status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Start Date -->
                            <div class="col-md-6 mb-3">
                                <label for="StartDate" class="form-label fw-bold text-gray-700">
                                    Ngày bắt đầu <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" class="form-control @error('StartDate') is-invalid @enderror" 
                                       id="StartDate" name="StartDate" 
                                       value="{{ old('StartDate', date('Y-m-d\TH:i', strtotime($voucher->StartDate))) }}" required>
                                @error('StartDate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6 mb-3">
                                <label for="EndDate" class="form-label fw-bold text-gray-700">
                                    Ngày kết thúc <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" class="form-control @error('EndDate') is-invalid @enderror" 
                                       id="EndDate" name="EndDate" 
                                       value="{{ old('EndDate', date('Y-m-d\TH:i', strtotime($voucher->EndDate))) }}" required>
                                @error('EndDate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Usage Limit -->
                            <div class="col-md-6 mb-3">
                                <label for="UsageLimit" class="form-label fw-bold text-gray-700">
                                    Giới hạn sử dụng
                                </label>
                                <input type="number" class="form-control @error('UsageLimit') is-invalid @enderror" 
                                       id="UsageLimit" name="UsageLimit" 
                                       value="{{ old('UsageLimit', $voucher->UsageLimit) }}" 
                                       placeholder="Để trống nếu không giới hạn">
                                @error('UsageLimit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Số lần sử dụng tối đa của voucher.</small>
                            </div>

                            <!-- Per User Limit -->
                            <div class="col-md-6 mb-3">
                                <label for="PerUserLimit" class="form-label fw-bold text-gray-700">
                                    Giới hạn mỗi người dùng
                                </label>
                                <input type="number" class="form-control @error('PerUserLimit') is-invalid @enderror" 
                                       id="PerUserLimit" name="PerUserLimit" 
                                       value="{{ old('PerUserLimit', $voucher->PerUserLimit) }}" 
                                       placeholder="Để trống nếu không giới hạn">
                                @error('PerUserLimit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Số lần mỗi người có thể sử dụng voucher.</small>
                            </div>
                        </div>

                        <!-- Thông tin bổ sung -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-plus me-1"></i>
                                                Ngày tạo: {{ \Carbon\Carbon::parse($voucher->created_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-sync-alt me-1"></i>
                                                Cập nhật lần cuối: {{ \Carbon\Carbon::parse($voucher->updated_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('manager.vouchers.index') }}" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-times me-2"></i>Hủy
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Cập nhật Voucher
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountTypeSelect = document.getElementById('DiscountType');
    const valueSuffix = document.getElementById('valueSuffix');
    const valueInput = document.getElementById('Value');

    // Update value suffix based on discount type
    function updateValueSuffix() {
        if (discountTypeSelect.value === 'Percent') {
            valueSuffix.textContent = '%';
            valueInput.placeholder = '0.00';
            valueInput.step = '0.01';
        } else {
            valueSuffix.textContent = '₫';
            valueInput.placeholder = '0';
            valueInput.step = '1';
        }
    }

    // Set minimum dates for datetime inputs
    const startDateInput = document.getElementById('StartDate');
    const endDateInput = document.getElementById('EndDate');

    // Set end date to be after start date
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });

    // Event listeners
    discountTypeSelect.addEventListener('change', updateValueSuffix);
    
    // Initialize on page load
    updateValueSuffix();
});
</script>

<style>
.card {
    border-radius: 0.5rem;
}

.form-label {
    font-size: 0.875rem;
}

.form-control, .form-select {
    border-radius: 0.375rem;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.alert {
    border-radius: 0.5rem;
    border: none;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #d1d3e2;
    min-width: 60px;
}

.alert-light {
    background-color: #f8f9fa;
    border-color: #e3e6f0;
}
</style>
@endsection