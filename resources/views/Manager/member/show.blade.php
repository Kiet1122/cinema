@extends('manager.layouts.app') {{-- Giả định bạn có một layout cơ bản cho Manager và đã nhúng Bootstrap 5 --}}

@section('title', 'Chi tiết Thành viên: ' . $customer->FullName)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h1 class="h3 text-dark fw-bold">Chi tiết Thành viên</h1>
        <div class="d-flex gap-2">
            {{-- Nút Chỉnh sửa --}}
            <a href="{{ route('manager.member.edit', $customer->CustomerID) }}" class="btn btn-primary shadow-sm">
                Chỉnh sửa Thông tin
            </a>
            {{-- Nút Quay lại --}}
            <a href="{{ route('manager.member.index') }}" class="btn btn-outline-secondary shadow-sm">
                &larr; Quay lại danh sách
            </a>
        </div>
    </div>

{{-- Thông báo cho thao tác Tặng/Thu hồi Voucher --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <h4 class="alert-heading fs-6">Lỗi!</h4>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-lg border-0">
    <div class="card-body p-4 p-lg-5">
        <div class="row g-4">
            
            {{-- Cột 1 & 2: THÔNG TIN CHUNG & CÁ NHÂN (col-lg-8) --}}
            <div class="col-lg-8 d-flex flex-column gap-5">
                
                {{-- KHỐI 1: THÔNG TIN CÁ NHÂN (CUSTOMER) --}}
                <div class="pb-3 border-bottom">
                    <h2 class="h4 text-primary mb-4">Thông tin Cá nhân</h2>
                    <dl class="row g-3">
                        <div class="col-sm-6">
                            <dt class="text-muted fw-semibold">Họ và Tên</dt>
                            <dd class="text-dark">{{ $customer->FullName }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-muted fw-semibold">Mã khách hàng (ID)</dt>
                            <dd class="text-dark">{{ $customer->CustomerID }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-muted fw-semibold">Số điện thoại</dt>
                            <dd class="text-dark">{{ $customer->Phone ?? 'Chưa cung cấp' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-muted fw-semibold">Ngày sinh</dt>
                            <dd class="text-dark">{{ $customer->DateOfBirth ? \Carbon\Carbon::parse($customer->DateOfBirth)->format('d/m/Y') : 'Chưa cung cấp' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-muted fw-semibold">Giới tính</dt>
                            <dd class="text-dark">
                                @if($customer->Gender == 'Male') Nam 
                                @elseif($customer->Gender == 'Female') Nữ
                                @else Khác / Không rõ
                                @endif
                            </dd>
                        </div>
                    </dl>
                    
                    {{-- Địa chỉ --}}
                    <div class="mt-3">
                        <dt class="text-muted fw-semibold">Địa chỉ</dt>
                        <dd class="text-dark">{{ $customer->Address ?? 'Chưa cung cấp' }}</dd>
                    </div>
                </div>

                {{-- KHỐI 2: THÔNG TIN TÀI KHOẢN (USER) --}}
                <div>
                    <h2 class="h4 text-info mb-4">Thông tin Tài khoản User</h2>
                    @if($customer->user)
                    <dl class="row g-3">
                        <div class="col-sm-6">
                            <dt class="text-muted fw-semibold">Email</dt>
                            <dd class="text-dark">{{ $customer->user->Email }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-muted fw-semibold">Trạng thái</dt>
                            <dd class="text-dark">
                                <span class="badge 
                                    @if($customer->user->Status === 'Active') text-bg-success
                                    @elseif($customer->user->Status === 'Banned') text-bg-danger
                                    @else text-bg-warning @endif">
                                    {{ $customer->user->Status }}
                                </span>
                            </dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-muted fw-semibold">Ngày tạo</dt>
                            <dd class="text-dark">{{ $customer->user->created_at ? $customer->user->created_at->format('H:i d/m/Y') : 'N/A' }}</dd>
                        </div>
                    </dl>
                    @else
                        <p class="text-danger fst-italic small">Thành viên này chưa có tài khoản User liên kết.</p>
                    @endif
                </div>
            </div>

            {{-- Cột 3: QUẢN LÝ VOUCHER TẠI CHỖ (col-lg-4) --}}
            <div class="col-lg-4 bg-light p-4 rounded-3 shadow-sm d-flex flex-column h-100">
                <h2 class="h4 text-purple mb-3 pb-2 border-bottom border-purple-200">Quản lý Voucher</h2>

                {{-- 1. FORM TÍCH/GỠ VOUCHER (Multi-select) --}}
                <div class="mb-4 p-3 border border-purple-300 rounded-3 bg-white shadow-sm">
                    <h3 class="fs-6 fw-semibold text-secondary mb-3">Voucher Khả dụng (Tích để Tặng/Bỏ tích để Gỡ)</h3>
                    
                    <form action="{{ route('manager.member.assignVoucher', $customer->CustomerID) }}" method="POST">
                        @csrf
                        
                        {{-- Danh sách Voucher khả dụng với Checkbox --}}
                        {{-- Sử dụng style cho chiều cao cố định và overflow-y --}}
                        <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto;"> 
                            @if($availableVouchers->isEmpty())
                                <p class="text-center text-muted small">Không có Voucher đang hoạt động để tặng.</p>
                            @else
                                @foreach($availableVouchers as $voucher)
                                    @php
                                        // Kiểm tra xem khách hàng đã sở hữu voucher này (và chưa sử dụng) hay chưa.
                                        $isChecked = $customer->customerVouchers
                                                            ->where('VoucherID', $voucher->VoucherID)
                                                            ->where('IsUsed', 0)
                                                            ->isNotEmpty();
                                        
                                        // Kiểm tra nếu voucher đã được dùng, thì không cho phép tích/gỡ
                                        $isUsed = $customer->customerVouchers
                                                            ->where('VoucherID', $voucher->VoucherID)
                                                            ->where('IsUsed', 1)
                                                            ->isNotEmpty();
                                    @endphp
                                    <div class="form-check p-2 border rounded-2 {{ $isUsed ? 'bg-secondary-subtle opacity-75' : 'bg-white hover:bg-purple-100' }}">
                                        <input id="voucher_{{ $voucher->VoucherID }}" 
                                                name="voucher_ids[]" 
                                                type="checkbox" 
                                                value="{{ $voucher->VoucherID }}" 
                                                {{ $isChecked ? 'checked' : '' }}
                                                {{ $isUsed ? 'disabled' : '' }}
                                                class="form-check-input mx-2 text-purple border-purple-300 shadow-none">
                                        
                                        <label for="voucher_{{ $voucher->VoucherID }}" class="form-check-label small text-dark flex-grow-1 cursor-pointer">
                                            {{ $voucher->Code }} 
                                            <span class="text-muted fw-normal">
                                                ({{ $voucher->DiscountType === 'Percent' ? $voucher->Value . '%' : number_format($voucher->Value) . ' VNĐ' }})
                                            </span>
                                            @if($isUsed)
                                                <span class="text-danger fst-italic ms-2">(Đã dùng - Không thể gỡ)</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" 
                                class="btn btn-purple w-100 fw-semibold shadow-sm">
                                Cập nhật Voucher (Tặng/Gỡ)
                            </button>
                        </div>
                    </form>
                </div>
                
                {{-- 2. DANH SÁCH VOUCHER ĐÃ SỞ HỮU --}}
                <h3 class="fs-6 fw-semibold text-secondary mt-3 mb-3 pt-3 border-top">Trạng thái Voucher hiện tại</h3>

                @if($customer->customerVouchers->isEmpty())
                    <p class="text-center text-muted py-3 small">Thành viên này chưa sở hữu voucher nào.</p>
                @else
                    {{-- Danh sách chi tiết các voucher đã sở hữu --}}
                    <ul class="list-unstyled d-flex flex-column gap-2" style="max-height: 250px; overflow-y: auto;"> 
                        @foreach($customer->customerVouchers as $cv)
                        <li class="p-3 border rounded-3 d-flex justify-content-between align-items-center small 
                            @if($cv->IsUsed) bg-white opacity-75 border-secondary-subtle
                            @else bg-white shadow-sm border-success @endif">
                            <div class="flex-grow-1">
                                <p class="fw-semibold text-dark mb-0">{{ $cv->voucher->Code }}</p>
                                <p class="text-muted mb-0 small">
                                    Giá trị: <span class="fw-medium text-purple">
                                        {{ $cv->voucher->DiscountType === 'Percent' ? $cv->voucher->Value . '%' : number_format($cv->voucher->Value) . ' VNĐ' }}
                                    </span>
                                </p>
                            </div>
                            
                            <span class="badge 
                                @if($cv->IsUsed) text-bg-danger
                                @else text-bg-success @endif mx-3">
                                {{ $cv->IsUsed ? 'Đã dùng' : 'Chưa dùng' }}
                            </span>
                            
                            {{-- Nút THU HỒI (CHỈ CHO VOUCHER CHƯA DÙNG) --}}
                            @if(!$cv->IsUsed)
                                <button type="button" 
                                        data-voucher-id="{{ $cv->CustomerVoucherID }}" 
                                        data-voucher-code="{{ $cv->voucher->Code }}" 
                                        data-customer-id="{{ $customer->CustomerID }}"
                                        class="revoke-btn btn btn-sm btn-outline-danger shadow-none"
                                        title="Thu hồi Voucher">
                                    Thu hồi
                                </button>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</div>
</div>

{{-- Thêm custom CSS để định nghĩa màu tím (purple) cho Bootstrap và đảm bảo scrollbar đẹp hơn --}}
<style>
    /* Custom Scrollbar (optional, to maintain the original intent) */
    .overflow-y-auto::-webkit-scrollbar {
        width: 8px;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background-color: #A78BFA; /* purple-400 */
        border-radius: 4px;
    }
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #F3F4F6; /* gray-100 */
    }

    /* Custom color for Bootstrap (indigo & purple) */
    .text-primary { color: #4F46E5 !important; } /* Tailwind indigo-600 */
    .btn-primary { 
        background-color: #4F46E5; 
        border-color: #4F46E5; 
    }
    .btn-primary:hover { 
        background-color: #4338CA; /* indigo-700 */
        border-color: #4338CA;
    }
    .text-purple { color: #9333EA !important; } /* Tailwind purple-600 */
    .btn-purple { 
        background-color: #9333EA; 
        border-color: #9333EA; 
    }
    .btn-purple:hover { 
        background-color: #7E22CE; /* purple-700 */
        border-color: #7E22CE;
    }
    .text-purple.form-check-input:checked {
        background-color: #9333EA;
        border-color: #9333EA;
    }
</style>

{{-- Script để xử lý Thu hồi Voucher bằng AJAX hoặc Form Post --}}
{{-- Cần phải tạo form động vì nút Thu hồi nằm trong vòng lặp --}}
<form id="revoke-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE') {{-- Giả sử route Thu hồi dùng phương thức DELETE --}}
    <input type="hidden" name="customer_voucher_id" id="customer_voucher_id_input">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const revokeButtons = document.querySelectorAll('.revoke-btn');
        const revokeForm = document.getElementById('revoke-form');
        const customerVoucherIdInput = document.getElementById('customer_voucher_id_input');
        const customerId = '{{ $customer->CustomerID }}';

        revokeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const voucherId = this.dataset.voucherId;
                const voucherCode = this.dataset.voucherCode;
                
                // Thay thế window.confirm bằng modal Bootstrap nếu có thể, nhưng dùng JS confirm tạm thời
                if (confirm(`Bạn có chắc chắn muốn thu hồi Voucher "${voucherCode}" khỏi khách hàng này không?`)) {
                    // Cập nhật action của form và giá trị input
                    const url = `{{ url('manager/member') }}/${customerId}/revoke-voucher/${voucherId}`;
                    revokeForm.action = url;
                    customerVoucherIdInput.value = voucherId;
                    
                    // Gửi form
                    revokeForm.submit();
                }
            });
        });
    });
</script>
@endsection
