@extends('manager.layouts.app')

@section('title', 'Thêm Rạp Mới')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-plus-circle fs-1 text-primary"></i>
                </div>
                <div>
                    <h2 class="h4 mb-0 fw-bold">Thêm Rạp Mới</h2>
                    <p class="text-muted mb-0">Thêm thông tin rạp chiếu phim mới vào hệ thống</p>
                </div>
            </div>
            <a href="{{ route('manager.theaters.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Quay lại
            </a>
        </div>

        <!-- Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-3 border-0">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2 text-primary"></i>
                            Thông tin rạp chiếu phim
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('manager.theaters.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="Name" class="form-label fw-semibold">Tên Rạp <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-building text-muted"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control border-start-0 @error('Name') is-invalid @enderror"
                                            name="Name" id="Name" value="{{ old('Name') }}"
                                            placeholder="Nhập tên rạp chiếu phim" required>
                                    </div>
                                    @error('Name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="Phone" class="form-label fw-semibold">Số điện thoại</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-telephone text-muted"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control border-start-0 @error('Phone') is-invalid @enderror"
                                            name="Phone" id="Phone" value="{{ old('Phone') }}"
                                            placeholder="Nhập số điện thoại">
                                    </div>
                                    @error('Phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="City" class="form-label fw-semibold">Thành phố <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-geo-alt text-muted"></i>
                                        </span>
                                        <select class="form-select border-start-0 @error('City') is-invalid @enderror"
                                            name="City" id="City" required>
                                            <option value="">Chọn thành phố/tỉnh</option>

                                            {{-- Cities --}}
                                            <option value="Thành phố Hà Nội" {{ old('City') == 'Thành phố Hà Nội' ? 'selected' : '' }}>Thành phố Hà Nội</option>
                                            <option value="Thành phố Hồ Chí Minh" {{ old('City') == 'Thành phố Hồ Chí Minh' ? 'selected' : '' }}>Thành phố Hồ Chí Minh</option>
                                            <option value="Thành phố Đà Nẵng" {{ old('City') == 'Thành phố Đà Nẵng' ? 'selected' : '' }}>Thành phố Đà Nẵng</option>
                                            <option value="Thành phố Hải Phòng" {{ old('City') == 'Thành phố Hải Phòng' ? 'selected' : '' }}>Thành phố Hải Phòng</option>
                                            <option value="Thành phố Cần Thơ" {{ old('City') == 'Thành phố Cần Thơ' ? 'selected' : '' }}>Thành phố Cần Thơ</option>
                                            <option value="Thành phố Huế" {{ old('City') == 'Thành phố Huế' ? 'selected' : '' }}>Thành phố Huế</option>

                                            {{-- Provinces --}}
                                            <option value="Tỉnh An Giang" {{ old('City') == 'Tỉnh An Giang' ? 'selected' : '' }}>Tỉnh An Giang</option>
                                            <option value="Tỉnh Bắc Ninh" {{ old('City') == 'Tỉnh Bắc Ninh' ? 'selected' : '' }}>Tỉnh Bắc Ninh</option>
                                            <option value="Tỉnh Cao Bằng" {{ old('City') == 'Tỉnh Cao Bằng' ? 'selected' : '' }}>Tỉnh Cao Bằng</option>
                                            <option value="Tỉnh Cà Mau" {{ old('City') == 'Tỉnh Cà Mau' ? 'selected' : '' }}>
                                                Tỉnh Cà Mau</option>
                                            <option value="Tỉnh Điện Biên" {{ old('City') == 'Tỉnh Điện Biên' ? 'selected' : '' }}>Tỉnh Điện Biên</option>
                                            <option value="Tỉnh Đắk Lắk" {{ old('City') == 'Tỉnh Đắk Lắk' ? 'selected' : '' }}>Tỉnh Đắk Lắk</option>
                                            <option value="Tỉnh Đồng Nai" {{ old('City') == 'Tỉnh Đồng Nai' ? 'selected' : '' }}>Tỉnh Đồng Nai</option>
                                            <option value="Tỉnh Đồng Tháp" {{ old('City') == 'Tỉnh Đồng Tháp' ? 'selected' : '' }}>Tỉnh Đồng Tháp</option>
                                            <option value="Tỉnh Gia Lai" {{ old('City') == 'Tỉnh Gia Lai' ? 'selected' : '' }}>Tỉnh Gia Lai</option>
                                            <option value="Tỉnh Hà Tĩnh" {{ old('City') == 'Tỉnh Hà Tĩnh' ? 'selected' : '' }}>Tỉnh Hà Tĩnh</option>
                                            <option value="Tỉnh Hưng Yên" {{ old('City') == 'Tỉnh Hưng Yên' ? 'selected' : '' }}>Tỉnh Hưng Yên</option>
                                            <option value="Tỉnh Khánh Hòa" {{ old('City') == 'Tỉnh Khánh Hòa' ? 'selected' : '' }}>Tỉnh Khánh Hòa</option>
                                            <option value="Tỉnh Lai Châu" {{ old('City') == 'Tỉnh Lai Châu' ? 'selected' : '' }}>Tỉnh Lai Châu</option>
                                            <option value="Tỉnh Lào Cai" {{ old('City') == 'Tỉnh Lào Cai' ? 'selected' : '' }}>Tỉnh Lào Cai</option>
                                            <option value="Tỉnh Lâm Đồng" {{ old('City') == 'Tỉnh Lâm Đồng' ? 'selected' : '' }}>Tỉnh Lâm Đồng</option>
                                            <option value="Tỉnh Lạng Sơn" {{ old('City') == 'Tỉnh Lạng Sơn' ? 'selected' : '' }}>Tỉnh Lạng Sơn</option>
                                            <option value="Tỉnh Nghệ An" {{ old('City') == 'Tỉnh Nghệ An' ? 'selected' : '' }}>Tỉnh Nghệ An</option>
                                            <option value="Tỉnh Ninh Bình" {{ old('City') == 'Tỉnh Ninh Bình' ? 'selected' : '' }}>Tỉnh Ninh Bình</option>
                                            <option value="Tỉnh Phú Thọ" {{ old('City') == 'Tỉnh Phú Thọ' ? 'selected' : '' }}>Tỉnh Phú Thọ</option>
                                            <option value="Tỉnh Quảng Ngãi" {{ old('City') == 'Tỉnh Quảng Ngãi' ? 'selected' : '' }}>Tỉnh Quảng Ngãi</option>
                                            <option value="Tỉnh Quảng Ninh" {{ old('City') == 'Tỉnh Quảng Ninh' ? 'selected' : '' }}>Tỉnh Quảng Ninh</option>
                                            <option value="Tỉnh Quảng Trị" {{ old('City') == 'Tỉnh Quảng Trị' ? 'selected' : '' }}>Tỉnh Quảng Trị</option>
                                            <option value="Tỉnh Sơn La" {{ old('City') == 'Tỉnh Sơn La' ? 'selected' : '' }}>
                                                Tỉnh Sơn La</option>
                                            <option value="Tỉnh Tây Ninh" {{ old('City') == 'Tỉnh Tây Ninh' ? 'selected' : '' }}>Tỉnh Tây Ninh</option>
                                            <option value="Tỉnh Thanh Hóa" {{ old('City') == 'Tỉnh Thanh Hóa' ? 'selected' : '' }}>Tỉnh Thanh Hóa</option>
                                            <option value="Tỉnh Thái Nguyên" {{ old('City') == 'Tỉnh Thái Nguyên' ? 'selected' : '' }}>Tỉnh Thái Nguyên</option>
                                            <option value="Tỉnh Tuyên Quang" {{ old('City') == 'Tỉnh Tuyên Quang' ? 'selected' : '' }}>Tỉnh Tuyên Quang</option>
                                            <option value="Tỉnh Vĩnh Long" {{ old('City') == 'Tỉnh Vĩnh Long' ? 'selected' : '' }}>Tỉnh Vĩnh Long</option>

                                        </select>
                                    </div>
                                    @error('City')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="Address" class="form-label fw-semibold">Địa chỉ <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 align-items-start pt-2">
                                            <i class="bi bi-geo-fill text-muted"></i>
                                        </span>
                                        <textarea class="form-control border-start-0 @error('Address') is-invalid @enderror"
                                            name="Address" id="Address" rows="3" placeholder="Nhập địa chỉ đầy đủ"
                                            required>{{ old('Address') }}</textarea>
                                    </div>
                                    @error('Address')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Nút hành động -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('manager.theaters.index') }}"
                                            class="btn btn-outline-secondary px-4">
                                            <i class="bi bi-x-circle me-2"></i> Hủy
                                        </a>
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-check-circle me-2"></i> Lưu Thông Tin
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Thông tin hữu ích -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-light py-3 border-0">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-lightbulb me-2 text-warning"></i>
                            Mẹo nhập thông tin
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-center mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <small class="text-muted">Tên rạp nên rõ ràng và dễ nhận biết</small>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <small class="text-muted">Địa chỉ cần chính xác để khách hàng dễ tìm</small>
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <small class="text-muted">Số điện thoại đúng định dạng để liên hệ</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 12px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
        }

        .form-control,
        .form-select {
            border-radius: 0 8px 8px 0;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tự động focus vào ô đầu tiên
            document.getElementById('Name').focus();

            // Validate form
            const form = document.querySelector('form');
            form.addEventListener('submit', function (e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    </script>
@endpush