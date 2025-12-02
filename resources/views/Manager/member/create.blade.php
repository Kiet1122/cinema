@extends('manager.layouts.app') 

@section('title', 'Thêm Thành viên Mới')

@section('content')
{{-- Khối chính, sử dụng container và padding của Bootstrap --}}
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark">Thêm Thành viên Mới</h1>
        <a href="{{ route('manager.member.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-body p-lg-5">
            {{-- Hiển thị lỗi validation (Alert Bootstrap) --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading h6">Lỗi nhập liệu!</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('manager.member.store') }}" method="POST">
                @csrf

                {{-- THÔNG TIN ĐĂNG NHẬP --}}
                <h2 class="h5 text-primary mb-3 pb-2 border-bottom">Thông tin Đăng nhập (User)</h2>
                <div class="row g-3 mb-4">
                    
                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="Email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="Email" id="Email" required 
                            class="form-control @error('Email') is-invalid @enderror" 
                            value="{{ old('Email') }}">
                        @error('Email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Password --}}
                    <div class="col-md-6">
                        <label for="Password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="Password" id="Password" required minlength="6"
                            class="form-control @error('Password') is-invalid @enderror">
                        @error('Password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div> {{-- End row --}}

                {{-- THÔNG TIN CÁ NHÂN --}}
                <h2 class="h5 text-primary mb-3 pb-2 border-bottom">Thông tin Cá nhân (Customer)</h2>
                <div class="row g-3 mb-4">
                    
                    {{-- FullName --}}
                    <div class="col-md-4">
                        <label for="FullName" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="FullName" id="FullName" required 
                            class="form-control @error('FullName') is-invalid @enderror"
                            value="{{ old('FullName') }}">
                        @error('FullName')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Phone --}}
                    <div class="col-md-4">
                        <label for="Phone" class="form-label">Số điện thoại</label>
                        <input type="tel" name="Phone" id="Phone" 
                            class="form-control @error('Phone') is-invalid @enderror"
                            value="{{ old('Phone') }}">
                        @error('Phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    {{-- DateOfBirth --}}
                    <div class="col-md-4">
                        <label for="DateOfBirth" class="form-label">Ngày sinh</label>
                        <input type="date" name="DateOfBirth" id="DateOfBirth" 
                            class="form-control @error('DateOfBirth') is-invalid @enderror"
                            value="{{ old('DateOfBirth') }}">
                        @error('DateOfBirth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div> {{-- End row --}}

                <div class="row g-3 mb-4">
                    {{-- Gender --}}
                    <div class="col-md-4">
                        <label for="Gender" class="form-label">Giới tính</label>
                        <select name="Gender" id="Gender"
                            class="form-select @error('Gender') is-invalid @enderror">
                            <option value="">-- Chọn --</option>
                            <option value="Male" {{ old('Gender') == 'Male' ? 'selected' : '' }}>Nam</option>
                            <option value="Female" {{ old('Gender') == 'Female' ? 'selected' : '' }}>Nữ</option>
                            <option value="Other" {{ old('Gender') == 'Other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('Gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Placeholder col-md-8 để cân đối layout --}}
                    <div class="col-md-8"></div>
                </div>

                {{-- Address --}}
                <div class="mb-5">
                    <label for="Address" class="form-label">Địa chỉ</label>
                    <textarea name="Address" id="Address" rows="3"
                        class="form-control @error('Address') is-invalid @enderror">{{ old('Address') }}</textarea>
                    @error('Address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-user-plus me-2"></i> Tạo Thành viên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
