@extends('customer.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 text-dark mb-0">
                    <i class="fas fa-user-circle me-2"></i>Thông Tin Cá Nhân
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('customer.booking.history') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-history me-1"></i>Lịch sử đặt vé
                    </a>
                    <a href="{{ route('customer.voucher.list') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-ticket-alt me-1"></i>Voucher của tôi
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Có lỗi xảy ra:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Sidebar -->
        <div class="col-xl-3 col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        @if($customer->Avatar)
                            <img src="{{ $customer->Avatar }}" 
                                 alt="Avatar" 
                                 class="rounded-circle border border-4 border-primary shadow"
                                 style="width: 120px; height: 120px; object-fit: cover;"
                                 onerror="this.src='https://via.placeholder.com/120?text=No+Image'">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border border-4 border-primary shadow"
                                 style="width: 120px; height: 120px;">
                                <i class="fas fa-user text-muted fs-1"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="card-title text-dark mb-2 fw-bold">{{ $customer->FullName }}</h4>
                    <p class="text-muted mb-3">
                        <i class="fas fa-envelope me-2"></i>{{ Auth::user()->Email }}
                    </p>
                    
                    <div class="bg-light rounded-3 p-3 text-start">
                        <div class="row g-2">
                            @if($customer->Phone)
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-phone me-2"></i>{{ $customer->Phone }}
                                </small>
                            </div>
                            @endif
                            @if($customer->DateOfBirth)
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-birthday-cake me-2"></i>
                                    {{ \Carbon\Carbon::parse($customer->DateOfBirth)->format('d/m/Y') }}
                                </small>
                            </div>
                            @endif
                            @if($customer->Gender)
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-venus-mars me-2"></i>
                                    {{ $customer->Gender == 'Male' ? 'Nam' : ($customer->Gender == 'Female' ? 'Nữ' : 'Khác') }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-8">
            <div class="row g-4">
                <!-- Thông tin cá nhân -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-transparent border-bottom-0 py-3">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="fas fa-user-edit me-2"></i>
                                Chỉnh Sửa Thông Tin
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Họ và tên</label>
                                        <input type="text" name="FullName" 
                                               value="{{ old('FullName', $customer->FullName) }}" 
                                               class="form-control form-control-lg">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Số điện thoại</label>
                                        <input type="text" name="Phone" 
                                               value="{{ old('Phone', $customer->Phone) }}" 
                                               class="form-control form-control-lg">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Giới tính</label>
                                        <select name="Gender" class="form-select form-select-lg">
                                            <option value="">Chọn giới tính</option>
                                            <option value="Male" {{ old('Gender', $customer->Gender) == 'Male' ? 'selected' : '' }}>Nam</option>
                                            <option value="Female" {{ old('Gender', $customer->Gender) == 'Female' ? 'selected' : '' }}>Nữ</option>
                                            <option value="Other" {{ old('Gender', $customer->Gender) == 'Other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Ngày sinh</label>
                                        <input type="date" name="DateOfBirth" 
                                               value="{{ old('DateOfBirth', $customer->DateOfBirth ? \Carbon\Carbon::parse($customer->DateOfBirth)->format('Y-m-d') : '') }}" 
                                               class="form-control form-control-lg">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Trạng thái</label>
                                        <div class="form-control form-control-lg bg-light border-0">
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Hoạt động
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Địa chỉ</label>
                                        <textarea name="Address" rows="2" class="form-control form-control-lg">{{ old('Address', $customer->Address) }}</textarea>
                                    </div>
                                    
                                    <!-- Ảnh đại diện -->
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Ảnh đại diện</label>
                                        
                                        <div class="row g-4">
                                            <!-- URL Input -->
                                            <div class="col-md-6">
                                                <div class="border rounded-3 p-3 h-100">
                                                    <h6 class="fw-semibold mb-3">
                                                        <i class="fas fa-link me-2 text-primary"></i>URL Ảnh
                                                    </h6>
                                                    <input type="url" name="AvatarURL" 
                                                           value="{{ old('AvatarURL', $customer->Avatar) }}" 
                                                           class="form-control mb-3" 
                                                           placeholder="https://example.com/avatar.jpg">
                                                    <div id="url-preview" class="text-center {{ $customer->Avatar ? '' : 'd-none' }}">
                                                        <img id="url-preview-img" 
                                                             src="{{ $customer->Avatar ? $customer->Avatar : '#' }}" 
                                                             class="rounded border shadow-sm"
                                                             style="width: 100px; height: 100px; object-fit: cover;"
                                                             onerror="this.style.display='none'">
                                                        <p class="text-muted small mt-2">Xem trước</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- File Upload -->
                                            <div class="col-md-6">
                                                <div class="border rounded-3 p-3 h-100">
                                                    <h6 class="fw-semibold mb-3">
                                                        <i class="fas fa-upload me-2 text-success"></i>Tải Ảnh Lên
                                                    </h6>
                                                    <input type="file" name="AvatarFile" id="avatar-file-input" 
                                                           class="d-none" accept="image/*">
                                                    <label for="avatar-file-input" 
                                                           class="btn btn-outline-success w-100 mb-3 cursor-pointer">
                                                        <i class="fas fa-cloud-upload-alt me-2"></i>
                                                        Chọn ảnh từ máy
                                                    </label>
                                                    <span id="file-name" class="text-muted small d-block text-center">Chưa chọn ảnh</span>
                                                    
                                                    <div id="upload-preview" class="text-center d-none mt-3">
                                                        <img id="upload-preview-img" class="rounded border shadow-sm"
                                                             style="width: 100px; height: 100px; object-fit: cover;">
                                                        <p class="text-muted small mt-2">Xem trước</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($customer->Avatar)
                                            <div class="mt-3 p-3 bg-light rounded-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <p class="text-muted small mb-1">Ảnh đại diện hiện tại:</p>
                                                        <a href="{{ $customer->Avatar }}" target="_blank" class="text-primary small">
                                                            <i class="fas fa-external-link-alt me-1"></i>
                                                            {{ Str::limit($customer->Avatar, 40) }}
                                                        </a>
                                                    </div>
                                                    <button type="button" onclick="document.getElementById('remove-avatar-form').submit();" 
                                                            class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i>
                                                        Xóa ảnh
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary btn-lg px-4">
                                        <i class="fas fa-save me-2"></i>
                                        Cập nhật thông tin
                                    </button>
                                    <a href="{{ route('customer.home') }}" class="btn btn-outline-secondary btn-lg px-4 ms-2">
                                        <i class="fas fa-home me-2"></i>
                                        Về trang chủ
                                    </a>
                                </div>
                            </form>

                            <form id="remove-avatar-form" action="{{ route('customer.profile.remove.avatar') }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Đổi mật khẩu -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-transparent border-bottom-0 py-3">
                            <h5 class="card-title mb-0 text-dark">
                                <i class="fas fa-shield-alt me-2"></i>
                                Bảo Mật Tài Khoản
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('customer.profile.update.password') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                                        <input type="password" name="current_password" class="form-control form-control-lg" placeholder="••••••••">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Mật khẩu mới</label>
                                        <input type="password" name="new_password" class="form-control form-control-lg" placeholder="••••••••">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                                        <input type="password" name="new_password_confirmation" class="form-control form-control-lg" placeholder="••••••••">
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top">
                                    <button type="submit" class="btn btn-warning btn-lg px-4">
                                        <i class="fas fa-key me-2"></i>
                                        Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý upload file
    const avatarFileInput = document.getElementById('avatar-file-input');
    const fileName = document.getElementById('file-name');
    const uploadPreview = document.getElementById('upload-preview');
    const uploadPreviewImg = document.getElementById('upload-preview-img');

    avatarFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            fileName.textContent = file.name;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadPreviewImg.src = e.target.result;
                uploadPreview.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            fileName.textContent = 'Chưa chọn ảnh';
            uploadPreview.classList.add('d-none');
        }
    });

    // Xử lý preview URL
    const avatarURLInput = document.querySelector('input[name="AvatarURL"]');
    const urlPreview = document.getElementById('url-preview');
    const urlPreviewImg = document.getElementById('url-preview-img');

    avatarURLInput.addEventListener('input', function(e) {
        const url = e.target.value;
        
        if (url) {
            urlPreviewImg.src = url;
            urlPreview.classList.remove('d-none');
        } else {
            urlPreview.classList.add('d-none');
        }
    });

    // Xử lý khi URL ảnh bị lỗi
    urlPreviewImg.addEventListener('error', function() {
        this.style.display = 'none';
    });

    // Xử lý khi URL ảnh tải thành công
    urlPreviewImg.addEventListener('load', function() {
        this.style.display = 'block';
    });
});
</script>

<style>
.cursor-pointer {
    cursor: pointer;
}

.card {
    border-radius: 16px;
}

.form-control:focus, .form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border: none;
    border-radius: 12px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5b5ee0 0%, #7c3aed 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.border-primary {
    border-color: #6366f1 !important;
}

.shadow-sm {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
}

.form-control-lg {
    border-radius: 12px;
    padding: 0.75rem 1rem;
}

.rounded-3 {
    border-radius: 16px !important;
}

.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
    border-radius: 8px;
}
</style>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection