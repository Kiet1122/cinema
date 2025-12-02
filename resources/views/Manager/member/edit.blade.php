@extends('manager.layouts.app') {{-- Giả định bạn có một layout cơ bản cho Manager --}}

@section('title', 'Chỉnh sửa Thành viên: ' . $customer->FullName)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h1 class="h2 text-dark fw-bold">Chỉnh sửa Thành viên: <span class="text-primary">{{ $customer->FullName }}</span></h1>
        
        {{-- Nút Quay lại --}}
        <a href="{{ route('manager.member.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
        </a>
    </div>

    {{-- Thông báo Thành công --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    {{-- Thông báo Lỗi nhập liệu --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <h4 class="alert-heading fs-5">Lỗi nhập liệu!</h4>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-lg mb-4">
        {{-- Tiêu đề Card --}}
        <div class="card-header bg-white border-bottom border-primary-subtle">
            <h2 class="fs-4 text-primary fw-bold mb-0">Cập nhật Thông tin Thành viên</h2>
        </div>
        
        <div class="card-body p-4 p-md-5">
            <section>
                {{-- Giả định route cập nhật là manager.members.update --}}
                <form action="{{ route('manager.member.update', $customer->CustomerID) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- THÔNG TIN USER (Tài khoản) --}}
                    <h3 class="h4 text-secondary mt-3 mb-3 border-bottom pb-2">Thông tin Đăng nhập (User)</h3>
                    <div class="row g-4 mb-4">
                        
                        {{-- Email (Không cho sửa) --}}
                        <div class="col-md-4">
                            <label for="Email" class="form-label fw-medium">Email (Không thể sửa)</label>
                            <input type="email" name="Email_disabled" id="Email"
                                class="form-control bg-light text-muted" 
                                value="{{ old('Email', $customer->user->Email ?? '') }}"
                                disabled>
                            {{-- Trường ẩn để gửi Email hiện tại --}}
                            <input type="hidden" name="Email" value="{{ $customer->user->Email ?? '' }}"> 
                            @if(!$customer->user) 
                                <div class="form-text text-danger">Thành viên chưa có tài khoản User liên kết.</div>
                            @endif
                        </div>
                        
                        {{-- Password --}}
                        <div class="col-md-4">
                            <label for="Password" class="form-label fw-medium">Mật khẩu mới (Để trống nếu không đổi)</label>
                            <input type="password" name="Password" id="Password" 
                                class="form-control"
                                {{-- Chỉ cho phép đổi mật khẩu nếu user đã tồn tại --}}
                                @if(!$customer->user) disabled @endif>
                            @if ($customer->user)
                                <div class="form-text">Mật khẩu phải từ 8 ký tự trở lên.</div>
                            @endif
                        </div>
                        
                        {{-- Status --}}
                        <div class="col-md-4">
                            <label for="Status" class="form-label fw-medium">Trạng thái Tài khoản</label>
                            <select name="Status" id="Status" required
                                class="form-select"
                                @if(!$customer->user) disabled @endif>
                                <option value="Active" {{ old('Status', $customer->user->Status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('Status', $customer->user->Status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="Banned" {{ old('Status', $customer->user->Status ?? '') == 'Banned' ? 'selected' : '' }}>Banned</option>
                            </select>
                        </div>
                    </div>

                    {{-- THÔNG TIN CUSTOMER (Cá nhân) --}}
                    <h3 class="h4 text-secondary mt-5 mb-3 border-bottom pb-2">Thông tin Cá nhân (Customer)</h3>
                    <div class="row g-4 mb-5">
                        
                        {{-- FullName --}}
                        <div class="col-md-4">
                            <label for="FullName" class="form-label fw-medium">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" name="FullName" id="FullName" required 
                                class="form-control"
                                value="{{ old('FullName', $customer->FullName) }}">
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-4">
                            <label for="Phone" class="form-label fw-medium">Số điện thoại</label>
                            <input type="tel" name="Phone" id="Phone" 
                                class="form-control"
                                value="{{ old('Phone', $customer->Phone) }}">
                        </div>
                        
                        {{-- DateOfBirth --}}
                        <div class="col-md-4">
                            <label for="DateOfBirth" class="form-label fw-medium">Ngày sinh</label>
                            <input type="date" name="DateOfBirth" id="DateOfBirth" 
                                class="form-control"
                                value="{{ old('DateOfBirth', $customer->DateOfBirth) }}">
                        </div>

                        {{-- Gender --}}
                        <div class="col-md-4">
                            <label for="Gender" class="form-label fw-medium">Giới tính</label>
                            <select name="Gender" id="Gender" class="form-select">
                                <option value="">-- Chọn --</option>
                                <option value="Male" {{ old('Gender', $customer->Gender) == 'Male' ? 'selected' : '' }}>Nam</option>
                                <option value="Female" {{ old('Gender', $customer->Gender) == 'Female' ? 'selected' : '' }}>Nữ</option>
                                <option value="Other" {{ old('Gender', $customer->Gender) == 'Other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="mb-5">
                        <label for="Address" class="form-label fw-medium">Địa chỉ</label>
                        <textarea name="Address" id="Address" rows="3"
                            class="form-control">{{ old('Address', $customer->Address) }}</textarea>
                    </div>

                    {{-- Nút Submit --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" 
                            class="btn btn-primary btn-lg shadow-sm">
                            <i class="fas fa-save me-2"></i> Lưu Thay đổi Thông tin
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
