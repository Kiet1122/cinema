@extends('Manager.layouts.app')

@section('title', 'Danh sách Rạp Chiếu Phim')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-building fs-1 text-primary"></i>
                </div>
                <div>
                    <h2 class="h4 mb-0 fw-bold">Danh sách Rạp Chiếu Phim</h2>
                    <p class="text-muted mb-0">Quản lý thông tin các rạp chiếu phim trong hệ thống</p>
                </div>
            </div>
            <a href="{{ route('manager.theaters.create') }}" class="btn btn-primary px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Thêm Rạp Mới
            </a>
        </div>

        <!-- Thông báo -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        <!-- Card chính -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <!-- Toolbar với form lọc -->
                <form method="GET" action="{{ route('manager.theaters.index') }}"
                    class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <div class="d-flex gap-2">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" name="search" class="form-control ps-5" placeholder="Tìm kiếm rạp..."
                                style="width: 250px;" value="{{ request('search') }}">
                        </div>
                        <!-- SỬA: đổi name="City" thành name="city" để khớp với controller -->
                        <select class="form-select" name="city" id="city" style="width: 200px;">
                            <option value="">Tất cả thành phố/tỉnh</option>

                            {{-- Cities --}}
                            <option value="Thành phố Hà Nội" {{ request('city') == 'Thành phố Hà Nội' ? 'selected' : '' }}>Thành
                                phố Hà Nội</option>
                            <option value="Thành phố Hồ Chí Minh" {{ request('city') == 'Thành phố Hồ Chí Minh' ? 'selected' : '' }}>Thành phố Hồ Chí Minh</option>
                            <option value="Thành phố Đà Nẵng" {{ request('city') == 'Thành phố Đà Nẵng' ? 'selected' : '' }}>Thành
                                phố Đà Nẵng</option>
                            <option value="Thành phố Hải Phòng" {{ request('city') == 'Thành phố Hải Phòng' ? 'selected' : '' }}>
                                Thành phố Hải Phòng</option>
                            <option value="Thành phố Cần Thơ" {{ request('city') == 'Thành phố Cần Thơ' ? 'selected' : '' }}>Thành
                                phố Cần Thơ</option>
                            <option value="Thành phố Huế" {{ request('city') == 'Thành phố Huế' ? 'selected' : '' }}>Thành phố Huế
                            </option>

                            {{-- Provinces --}}
                            <option value="Tỉnh An Giang" {{ request('city') == 'Tỉnh An Giang' ? 'selected' : '' }}>Tỉnh An Giang
                            </option>
                            <option value="Tỉnh Bắc Ninh" {{ request('city') == 'Tỉnh Bắc Ninh' ? 'selected' : '' }}>Tỉnh Bắc Ninh
                            </option>
                            <option value="Tỉnh Cao Bằng" {{ request('city') == 'Tỉnh Cao Bằng' ? 'selected' : '' }}>Tỉnh Cao Bằng
                            </option>
                            <option value="Tỉnh Cà Mau" {{ request('city') == 'Tỉnh Cà Mau' ? 'selected' : '' }}>
                                Tỉnh Cà Mau</option>
                            <option value="Tỉnh Điện Biên" {{ request('city') == 'Tỉnh Điện Biên' ? 'selected' : '' }}>Tỉnh Điện
                                Biên</option>
                            <option value="Tỉnh Đắk Lắk" {{ request('city') == 'Tỉnh Đắk Lắk' ? 'selected' : '' }}>Tỉnh Đắk Lắk
                            </option>
                            <option value="Tỉnh Đồng Nai" {{ request('city') == 'Tỉnh Đồng Nai' ? 'selected' : '' }}>Tỉnh Đồng Nai
                            </option>
                            <option value="Tỉnh Đồng Tháp" {{ request('city') == 'Tỉnh Đồng Tháp' ? 'selected' : '' }}>Tỉnh Đồng
                                Tháp</option>
                            <option value="Tỉnh Gia Lai" {{ request('city') == 'Tỉnh Gia Lai' ? 'selected' : '' }}>Tỉnh Gia Lai
                            </option>
                            <option value="Tỉnh Hà Tĩnh" {{ request('city') == 'Tỉnh Hà Tĩnh' ? 'selected' : '' }}>Tỉnh Hà Tĩnh
                            </option>
                            <option value="Tỉnh Hưng Yên" {{ request('city') == 'Tỉnh Hưng Yên' ? 'selected' : '' }}>Tỉnh Hưng Yên
                            </option>
                            <option value="Tỉnh Khánh Hòa" {{ request('city') == 'Tỉnh Khánh Hòa' ? 'selected' : '' }}>Tỉnh Khánh
                                Hòa</option>
                            <option value="Tỉnh Lai Châu" {{ request('city') == 'Tỉnh Lai Châu' ? 'selected' : '' }}>Tỉnh Lai Châu
                            </option>
                            <option value="Tỉnh Lào Cai" {{ request('city') == 'Tỉnh Lào Cai' ? 'selected' : '' }}>Tỉnh Lào Cai
                            </option>
                            <option value="Tỉnh Lâm Đồng" {{ request('city') == 'Tỉnh Lâm Đồng' ? 'selected' : '' }}>Tỉnh Lâm Đồng
                            </option>
                            <option value="Tỉnh Lạng Sơn" {{ request('city') == 'Tỉnh Lạng Sơn' ? 'selected' : '' }}>Tỉnh Lạng Sơn
                            </option>
                            <option value="Tỉnh Nghệ An" {{ request('city') == 'Tỉnh Nghệ An' ? 'selected' : '' }}>Tỉnh Nghệ An
                            </option>
                            <option value="Tỉnh Ninh Bình" {{ request('city') == 'Tỉnh Ninh Bình' ? 'selected' : '' }}>Tỉnh Ninh
                                Bình</option>
                            <option value="Tỉnh Phú Thọ" {{ request('city') == 'Tỉnh Phú Thọ' ? 'selected' : '' }}>Tỉnh Phú Thọ
                            </option>
                            <option value="Tỉnh Quảng Ngãi" {{ request('city') == 'Tỉnh Quảng Ngãi' ? 'selected' : '' }}>Tỉnh
                                Quảng Ngãi</option>
                            <option value="Tỉnh Quảng Ninh" {{ request('city') == 'Tỉnh Quảng Ninh' ? 'selected' : '' }}>Tỉnh
                                Quảng Ninh</option>
                            <option value="Tỉnh Quảng Trị" {{ request('city') == 'Tỉnh Quảng Trị' ? 'selected' : '' }}>Tỉnh Quảng
                                Trị</option>
                            <option value="Tỉnh Sơn La" {{ request('city') == 'Tỉnh Sơn La' ? 'selected' : '' }}>
                                Tỉnh Sơn La</option>
                            <option value="Tỉnh Tây Ninh" {{ request('city') == 'Tỉnh Tây Ninh' ? 'selected' : '' }}>Tỉnh Tây Ninh
                            </option>
                            <option value="Tỉnh Thanh Hóa" {{ request('city') == 'Tỉnh Thanh Hóa' ? 'selected' : '' }}>Tỉnh Thanh
                                Hóa</option>
                            <option value="Tỉnh Thái Nguyên" {{ request('city') == 'Tỉnh Thái Nguyên' ? 'selected' : '' }}>Tỉnh
                                Thái Nguyên</option>
                            <option value="Tỉnh Tuyên Quang" {{ request('city') == 'Tỉnh Tuyên Quang' ? 'selected' : '' }}>Tỉnh
                                Tuyên Quang</option>
                            <option value="Tỉnh Vĩnh Long" {{ request('city') == 'Tỉnh Vĩnh Long' ? 'selected' : '' }}>Tỉnh Vĩnh
                                Long</option>

                        </select>
                        <select name="sort" class="form-select" style="width: 180px;">
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                            <option value="city_asc" {{ request('sort') == 'city_asc' ? 'selected' : '' }}>Thành phố A-Z
                            </option>
                            <option value="city_desc" {{ request('sort') == 'city_desc' ? 'selected' : '' }}>Thành phố Z-A
                            </option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-1"></i> Lọc
                        </button>
                        <a href="{{ route('manager.theaters.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Xóa lọc
                        </a>
                    </div>
                </form>

                <!-- Thống kê kết quả lọc -->
                @if(request()->has('search') || request()->has('city') || request()->has('sort'))
                    <div class="px-4 pt-3">
                        <div class="alert alert-info border-0 py-2 mb-0" style="background-color: #e7f1ff;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <small>
                                    @if(request('search'))
                                        <span class="badge bg-primary me-2">Tìm kiếm: "{{ request('search') }}"</span>
                                    @endif
                                    @if(request('city'))
                                        <span class="badge bg-secondary me-2">Thành phố: {{ request('city') }}</span>
                                    @endif
                                    @if(request('sort'))
                                        @php
                                            $sortLabels = [
                                                'name_asc' => 'Sắp xếp: Tên A-Z',
                                                'name_desc' => 'Sắp xếp: Tên Z-A',
                                                'city_asc' => 'Sắp xếp: Thành phố A-Z',
                                                'city_desc' => 'Sắp xếp: Thành phố Z-A',
                                                'newest' => 'Sắp xếp: Mới nhất',
                                                'oldest' => 'Sắp xếp: Cũ nhất'
                                            ];
                                        @endphp
                                        <span class="badge bg-success me-2">{{ $sortLabels[request('sort')] }}</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Bảng dữ liệu -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3 text-uppercase fw-semibold small">Tên rạp</th>
                                <th class="py-3 text-uppercase fw-semibold small">Địa chỉ</th>
                                <th class="py-3 text-uppercase fw-semibold small">Số điện thoại</th>
                                <th class="py-3 text-uppercase fw-semibold small">Thành phố</th>
                                <th class="py-3 text-uppercase fw-semibold small">Ngày tạo</th>
                                <th class="text-center py-3 text-uppercase fw-semibold small">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($theaters as $theater)
                                <tr class="border-bottom">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                                <i class="bi bi-building text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $theater->Name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $theater->Address }}">
                                            {{ $theater->Address }}
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-telephone me-1 text-muted"></i>
                                        {{ $theater->Phone }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $theater->City }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-2 text-muted"></i>
                                            <span class="text-nowrap">{{ $theater->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('manager.theaters.edit', $theater->TheaterID) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('manager.theaters.destroy', $theater->TheaterID) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                    onclick="return confirm('Bạn có chắc muốn xóa rạp {{ $theater->Name }}?')"
                                                    data-bs-toggle="tooltip" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="bi bi-building fs-1 text-muted opacity-50"></i>
                                            <h5 class="text-muted mt-3">
                                                @if(request()->has('search') || request()->has('city'))
                                                    Không tìm thấy rạp phim phù hợp
                                                @else
                                                    Chưa có rạp chiếu phim nào
                                                @endif
                                            </h5>
                                            <p class="text-muted mb-4">
                                                @if(request()->has('search') || request()->has('city'))
                                                    Hãy thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc
                                                @else
                                                    Hãy thêm rạp chiếu phim đầu tiên của bạn
                                                @endif
                                            </p>
                                            @if(request()->has('search') || request()->has('city'))
                                                <a href="{{ route('manager.theaters.index') }}"
                                                    class="btn btn-outline-primary px-4">
                                                    <i class="bi bi-x-circle me-2"></i> Xóa lọc
                                                </a>
                                            @endif
                                            <a href="{{ route('manager.theaters.create') }}" class="btn btn-primary px-4">
                                                <i class="bi bi-plus-lg me-2"></i> Thêm Rạp Mới
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer - Thống kê -->
                @if($theaters->count() > 0)
                    <div class="d-flex justify-content-between align-items-center p-4 border-top">
                        <div class="text-muted">
                            <strong>{{ $theaters->count() }}</strong> rạp chiếu phim
                            @if(request('search') || request('city') || request('sort'))
                                (đã lọc)
                            @endif
                        </div>
                        <div class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Hiển thị tất cả kết quả (không phân trang)
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 12px;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .btn {
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.04);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .alert-info {
            border-radius: 8px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Auto submit khi thay đổi select
            document.querySelectorAll('select[name="city"], select[name="sort"]').forEach(select => {
                select.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            });
        });
    </script>
@endpush