@extends('Manager.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header với gradient đẹp -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-gradient text-primary">Quản lý Suất Chiếu</h1>
                <p class="mb-0">Quản lý và theo dõi các suất chiếu trong hệ thống</p>
            </div>
            <a href="{{ route('manager.showtimes.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i> Thêm Suất Chiếu
            </a>
        </div>

        {{-- Thông báo với thiết kế đẹp hơn --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Bộ lọc được thiết kế lại --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light py-3">
                <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Bộ lọc & Tìm kiếm</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Tìm kiếm --}}
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tìm kiếm</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Tìm tên phim hoặc rạp...">
                        </div>
                    </div>

                    {{-- Trạng thái --}}
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Trạng thái</label>
                        <select id="statusFilter" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="Scheduled">Sắp chiếu</option>
                            <option value="Showing">Đang chiếu</option>
                            <option value="Finished">Đã chiếu</option>
                            <option value="Cancelled">Đã hủy</option>
                        </select>
                    </div>

                    {{-- Rạp chiếu --}}
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Rạp chiếu</label>
                        <select id="theaterFilter" class="form-select">
                            <option value="">Tất cả rạp</option>
                            @foreach($theaters as $theater)
                                <option value="{{ $theater->Name }}">{{ $theater->Name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Khoảng thời gian --}}
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Khoảng thời gian</label>
                        <div class="input-group">
                            <input type="date" id="startDate" class="form-control" placeholder="Từ ngày">
                            <span class="input-group-text bg-light">đến</span>
                            <input type="date" id="endDate" class="form-control" placeholder="Đến ngày">
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Thống kê nhanh --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Tổng số suất chiếu</h6>
                                <h4 class="mb-0 mt-2">{{ $showtimes->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-film fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Sắp chiếu</h6>
                                <h4 class="mb-0 mt-2">{{ $showtimes->where('Status', 'Scheduled')->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Đang chiếu</h6>
                                <h4 class="mb-0 mt-2">{{ $showtimes->where('Status', 'Showing')->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-play-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Đã chiếu</h6>
                                <h4 class="mb-0 mt-2">{{ $showtimes->where('Status', 'Finished')->count() }}</h4>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danh sách suất chiếu với thiết kế bảng đẹp hơn --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Danh sách Suất Chiếu</h5>
                <div class="text-muted small">
                    Hiển thị <span id="visibleCount">{{ $showtimes->count() }}</span> / {{ $showtimes->count() }} suất chiếu
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="showtimeTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Phim</th>
                                <th>Phòng</th>
                                <th>Rạp</th>
                                <th>Địa chỉ</th>
                                <th>Bắt đầu</th>
                                <th>Kết thúc</th>
                                <th>Giá vé</th>
                                <th>Trạng thái</th>
                                <th class="text-center pe-4">Quản lý</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($showtimes as $showtime)
                                <tr class="showtime-row">
                                    <td class="ps-4 movie">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="bg-light rounded" style="width: 40px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-film text-muted"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $showtime->movie->Title ?? '-' }}</h6>
                                                <small class="text-muted">{{ $showtime->movie->Duration ?? '' }} phút</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $showtime->room->RoomName ?? '-' }}</span>
                                    </td>
                                    <td class="theater">{{ $showtime->room->theater->Name ?? '-' }}</td>
                                    <td>
                                        <small class="text-muted">{{ $showtime->room->theater->City ?? '-' }}</small>
                                    </td>
                                    <td class="start">
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($showtime->StartTime)->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($showtime->StartTime)->format('H:i') }}</small>
                                    </td>
                                    <td class="end">
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($showtime->EndTime)->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($showtime->EndTime)->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ number_format($showtime->Price, 0, ',', '.') }} VNĐ</span>
                                    </td>
                                    <td class="status" data-status="{{ $showtime->Status }}">
                                        @if($showtime->Status == 'Scheduled')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Sắp chiếu</span>
                                        @elseif($showtime->Status == 'Showing')
                                            <span class="badge bg-success"><i class="fas fa-play-circle me-1"></i> Đang chiếu</span>
                                        @elseif($showtime->Status == 'Finished')
                                            <span class="badge bg-secondary"><i class="fas fa-check-circle me-1"></i> Đã chiếu</span>
                                        @elseif($showtime->Status == 'Cancelled')
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Đã hủy</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($showtime->Status != 'Finished')
                                                <a href="{{ route('manager.showtimes.edit', $showtime->ShowtimeID) }}"
                                                    class="btn btn-outline-primary" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('manager.showtimes.destroy', $showtime->ShowtimeID) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"
                                                    onclick="return confirm('Bạn có chắc muốn xóa suất chiếu này?')" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="py-5">
                                            <i class="fas fa-film fa-3x text-light mb-3"></i>
                                            <h5 class="text-muted">Không có suất chiếu nào</h5>
                                            <p class="text-muted">Hãy thêm suất chiếu đầu tiên để bắt đầu quản lý</p>
                                            <a href="{{ route('manager.showtimes.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i> Thêm Suất Chiếu
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Script lọc JS được cải thiện --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput");
            const statusFilter = document.getElementById("statusFilter");
            const theaterFilter = document.getElementById("theaterFilter");
            const startDate = document.getElementById("startDate");
            const endDate = document.getElementById("endDate");
            const resetBtn = document.getElementById("resetBtn");
            const visibleCount = document.getElementById("visibleCount");

            const rows = document.querySelectorAll("#showtimeTable tbody .showtime-row");

            function filterTable() {
                const search = searchInput.value.toLowerCase();
                const status = statusFilter.value;
                const theater = theaterFilter.value.toLowerCase();
                const start = startDate.value ? new Date(startDate.value) : null;
                const end = endDate.value ? new Date(endDate.value) : null;

                let visibleRows = 0;

                rows.forEach(row => {
                    const movieText = row.querySelector(".movie").innerText.toLowerCase();
                    const theaterText = row.querySelector(".theater").innerText.toLowerCase();
                    const statusValue = row.querySelector(".status").getAttribute("data-status");
                    const startText = row.querySelector(".start").innerText;
                    const endText = row.querySelector(".end").innerText;
                    
                    // Chuyển đổi định dạng ngày từ d/m/Y sang Date object
                    const startParts = startText.split('\n')[0].split('/');
                    const startDateObj = new Date(`${startParts[2]}-${startParts[1]}-${startParts[0]}`);
                    
                    const endParts = endText.split('\n')[0].split('/');
                    const endDateObj = new Date(`${endParts[2]}-${endParts[1]}-${endParts[0]}`);

                    let visible = true;

                    if (search && !movieText.includes(search) && !theaterText.includes(search)) {
                        visible = false;
                    }
                    if (status && statusValue !== status) {
                        visible = false;
                    }
                    if (theater && !theaterText.includes(theater)) {
                        visible = false;
                    }
                    if (start && startDateObj < start) {
                        visible = false;
                    }
                    if (end && endDateObj > end) {
                        visible = false;
                    }

                    row.style.display = visible ? "" : "none";
                    if (visible) visibleRows++;
                });

                visibleCount.textContent = visibleRows;
                
                // Hiển thị thông báo nếu không có kết quả
                if (visibleRows === 0) {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.innerHTML = `
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Không tìm thấy suất chiếu phù hợp với bộ lọc</p>
                        </td>
                    `;
                    
                    // Kiểm tra xem đã có hàng thông báo chưa
                    const existingNoResults = document.querySelector('.no-results-row');
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }
                    
                    noResultsRow.classList.add('no-results-row');
                    document.querySelector("#showtimeTable tbody").appendChild(noResultsRow);
                } else {
                    const existingNoResults = document.querySelector('.no-results-row');
                    if (existingNoResults) {
                        existingNoResults.remove();
                    }
                }
            }

            // Gán sự kiện
            searchInput.addEventListener("input", filterTable);
            statusFilter.addEventListener("change", filterTable);
            theaterFilter.addEventListener("change", filterTable);
            startDate.addEventListener("change", filterTable);
            endDate.addEventListener("change", filterTable);

            resetBtn.addEventListener("click", function () {
                searchInput.value = "";
                statusFilter.value = "";
                theaterFilter.value = "";
                startDate.value = "";
                endDate.value = "";
                filterTable();
            });

            // Tự động lọc khi trang được tải (nếu có giá trị trong các bộ lọc)
            filterTable();
        });
    </script>

    <style>
        .card {
            border: none;
            border-radius: 12px;
        }
        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        .table td {
            border-top: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
        }
        .badge {
            border-radius: 6px;
            font-weight: 500;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            border-radius: 8px 0 0 8px;
        }
    </style>
@endsection