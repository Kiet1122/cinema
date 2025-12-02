@extends('Manager.layouts.app')

@section('title', 'Hồ Sơ Quản Lý')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="h3 text-gray-800 mb-2">Hồ Sơ Quản Lý</h1>
                <p class="text-muted">Quản lý thông tin cá nhân của bạn</p>
            </div>

            <!-- Thông báo thành công -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Cột Avatar -->
                        <div class="col-md-4 text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <div class="rounded-circle overflow-hidden border-4 border-primary shadow-lg mb-3"
                                    style="width: 150px; height: 150px;">
                                    @php
                                        $avatarUrl = $manager->Avatar ?: "https://ui-avatars.com/api/?name=" . urlencode($manager->FullName ?? 'Manager') . "&background=4f46e5&color=fff&size=150";
                                    @endphp
                                    <img src="{{ $avatarUrl }}" alt="Avatar" class="w-100 h-100 object-cover"
                                        id="avatar-preview">
                                </div>
                                <div class="mt-3">
                                    <h5 class="fw-bold text-gray-800">{{ $manager->FullName ?? 'Chưa cập nhật' }}</h5>
                                    <p class="text-muted small">{{ $user->Username ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cột Form -->
                        <div class="col-md-8">
                            <h4 class="card-title mb-4 text-gray-700">
                                <i class="fas fa-user-edit me-2"></i>Cập nhật thông tin
                            </h4>

                            <form action="{{ route('manager.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Full Name -->
                                <div class="mb-3">
                                    <label for="FullName" class="form-label fw-semibold">
                                        <i class="fas fa-user me-2 text-primary"></i>Họ và Tên
                                    </label>
                                    <input type="text" name="FullName" id="FullName"
                                        value="{{ old('FullName', $manager->FullName) }}"
                                        class="form-control @error('FullName') is-invalid @enderror"
                                        placeholder="Nhập họ và tên đầy đủ">
                                    @error('FullName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="Phone" class="form-label fw-semibold">
                                        <i class="fas fa-phone me-2 text-primary"></i>Số Điện Thoại
                                    </label>
                                    <input type="text" name="Phone" id="Phone"
                                        value="{{ old('Phone', $manager->Phone) }}"
                                        class="form-control @error('Phone') is-invalid @enderror"
                                        placeholder="Nhập số điện thoại">
                                    @error('Phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Avatar Link -->
                                <div class="mb-4">
                                    <label for="Avatar_Link" class="form-label fw-semibold">
                                        <i class="fas fa-link me-2 text-primary"></i>Link Ảnh Đại Diện
                                    </label>
                                    <input type="url" name="Avatar_Link" id="Avatar_Link"
                                        value="{{ old('Avatar_Link', $manager->Avatar) }}"
                                        class="form-control @error('Avatar_Link') is-invalid @enderror"
                                        placeholder="https://example.com/avatar.jpg">
                                    @error('Avatar_Link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Nhập đường dẫn (URL) đến ảnh đại diện của bạn. 
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="testAvatarLink()">
                                            <i class="fas fa-eye me-1"></i>Xem trước
                                        </button>
                                    </div>
                                </div>

                                <!-- Thông tin không thể thay đổi -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">
                                            <i class="fas fa-envelope me-2"></i>Email Đăng Nhập
                                        </label>
                                        <div class="bg-light rounded p-3">
                                            <span class="text-gray-700">{{ $user->Email }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-muted">
                                            <i class="fas fa-id-card me-2"></i>Mã Người Dùng
                                        </label>
                                        <div class="bg-light rounded p-3">
                                            <span class="text-gray-700">{{ $user->UserID }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Lưu Thay Đổi
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
        // Xem trước ảnh khi nhập link
        const avatarLinkInput = document.getElementById('Avatar_Link');
        const avatarPreview = document.getElementById('avatar-preview');

        if (avatarLinkInput && avatarPreview) {
            avatarLinkInput.addEventListener('input', function() {
                const link = this.value.trim();
                if (link && isValidUrl(link)) {
                    // Thêm timeout để tránh request liên tục
                    clearTimeout(this._timer);
                    this._timer = setTimeout(() => {
                        avatarPreview.src = link;
                    }, 500);
                }
            });
        }
    });

    // Hàm kiểm tra URL hợp lệ
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Hàm xem trước ảnh
    function testAvatarLink() {
        const linkInput = document.getElementById('Avatar_Link');
        const avatarPreview = document.getElementById('avatar-preview');
        const link = linkInput.value.trim();

        if (!link) {
            alert('Vui lòng nhập link ảnh đại diện');
            linkInput.focus();
            return;
        }

        if (!isValidUrl(link)) {
            alert('Link ảnh không hợp lệ. Vui lòng nhập URL đầy đủ (ví dụ: https://example.com/avatar.jpg)');
            linkInput.focus();
            return;
        }

        // Hiển thị loading
        avatarPreview.style.opacity = '0.5';
        
        // Tạo ảnh tạm để test
        const testImage = new Image();
        testImage.onload = function() {
            avatarPreview.src = link;
            avatarPreview.style.opacity = '1';
            alert('✅ Link ảnh hợp lệ! Ảnh đã được xem trước.');
        };
        testImage.onerror = function() {
            avatarPreview.style.opacity = '1';
            alert('❌ Không thể tải ảnh từ link này. Vui lòng kiểm tra lại URL.');
        };
        testImage.src = link;
    }
</script>

<style>
    .card {
        border: none;
        border-radius: 0.5rem;
    }
    .form-control {
        border-radius: 0.375rem;
    }
    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border: none;
        border-radius: 0.375rem;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        transform: translateY(-1px);
    }
    .rounded-circle {
        border-radius: 50% !important;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endsection