@extends('Manager.layouts.app')

@section('content')
    <div class="container mt-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1"><i class="bi bi-door-closed text-primary me-2"></i>Danh sách Phòng Chiếu</h2>
                <p class="text-muted mb-0">Quản lý thông tin các phòng chiếu trong hệ thống</p>
            </div>
            <a href="{{ route('manager.rooms.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> Thêm phòng chiếu
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter and Search Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm phòng chiếu...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="roomTypeFilter">
                            <option value="">Tất cả loại phòng</option>
                            <option value="2D">2D</option>
                            <option value="3D">3D</option>
                            <option value="IMAX">IMAX</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="theaterFilter">
                            <option value="">Tất cả rạp chiếu</option>
                            <!-- Options sẽ được thêm tự động bằng JavaScript -->
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rooms Table -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tên phòng</th>
                                <th>Sức chứa</th>
                                <th>Loại phòng</th>
                                <th>Rạp</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="roomsTableBody">
                            @forelse($rooms as $room)
                                <tr class="room-row" data-name="{{ strtolower($room->RoomName) }}"
                                    data-type="{{ $room->RoomType }}" data-theater="{{ $room->theater->Name ?? '' }}"
                                    data-theater-id="{{ $room->TheaterID }}">
                                    <td class="ps-4 fw-medium">{{ $room->RoomName }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-people-fill text-primary me-2"></i>
                                            {{ $room->Capacity }} chỗ
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-info';
                                            if ($room->RoomType == 'VIP')
                                                $badgeClass = 'bg-warning';
                                            if ($room->RoomType == 'IMAX')
                                                $badgeClass = 'bg-danger';
                                            if ($room->RoomType == 'Deluxe')
                                                $badgeClass = 'bg-success';
                                        @endphp
                                        <span
                                            class="badge {{ $badgeClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $badgeClass) }}">
                                            {{ $room->RoomType }}
                                        </span>
                                    </td>
                                    <td class="theater-name">{{ $room->theater->Name ?? 'N/A' }}</td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('manager.rooms.edit', $room->RoomID) }}"
                                                class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('manager.rooms.destroy', $room->RoomID) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa phòng chiếu này?')"
                                                    data-bs-toggle="tooltip" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-door-closed display-4 text-muted"></i>
                                            <p class="mt-3 text-muted">Chưa có phòng chiếu nào.</p>
                                            <a href="{{ route('manager.rooms.create') }}" class="btn btn-primary mt-2">
                                                <i class="bi bi-plus-circle me-2"></i> Thêm phòng chiếu đầu tiên
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($rooms->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Hiển thị <span id="visibleCount">{{ $rooms->count() }}</span> của {{ $rooms->total() }} kết quả
                        </div>
                        <div>
                            {{ $rooms->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            padding: 1rem 0.5rem;
        }

        .table td {
            padding: 1rem 0.5rem;
            vertical-align: middle;
        }

        .empty-state {
            padding: 2rem 1rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .badge {
            font-size: 0.85em;
            padding: 0.5em 0.75em;
        }

        .hidden-row {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Get filter elements
            const searchInput = document.getElementById('searchInput');
            const roomTypeFilter = document.getElementById('roomTypeFilter');
            const theaterFilter = document.getElementById('theaterFilter');
            const roomRows = document.querySelectorAll('.room-row');
            const visibleCountElement = document.getElementById('visibleCount');

            // Populate theater filter dropdown automatically
            populateTheaterFilter();

            // Add event listeners for filtering
            searchInput.addEventListener('input', applyFilters);
            roomTypeFilter.addEventListener('change', applyFilters);
            theaterFilter.addEventListener('change', applyFilters);

            // Function to populate theater filter
            function populateTheaterFilter() {
                const theaterNames = new Set();

                // Collect all unique theater names from the table
                roomRows.forEach(row => {
                    const theaterName = row.getAttribute('data-theater');
                    if (theaterName && theaterName !== 'N/A') {
                        theaterNames.add(theaterName);
                    }
                });

                // Add options to the theater filter
                theaterNames.forEach(name => {
                    const option = document.createElement('option');
                    option.value = name;
                    option.textContent = name;
                    theaterFilter.appendChild(option);
                });
            }

            // Function to apply all filters
            function applyFilters() {
                const searchText = searchInput.value.toLowerCase();
                const selectedType = roomTypeFilter.value;
                const selectedTheater = theaterFilter.value;

                let visibleCount = 0;

                roomRows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const type = row.getAttribute('data-type');
                    const theater = row.getAttribute('data-theater');

                    // Check if row matches all filters
                    const matchesSearch = name.includes(searchText) || searchText === '';
                    const matchesType = selectedType === '' || type === selectedType;
                    const matchesTheater = selectedTheater === '' || theater === selectedTheater;

                    if (matchesSearch && matchesType && matchesTheater) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update visible count if the element exists
                if (visibleCountElement) {
                    visibleCountElement.textContent = visibleCount;
                }

                // Show empty message if no results
                showEmptyMessage(visibleCount === 0);
            }

            // Function to show/hide empty message
            function showEmptyMessage(show) {
                let emptyRow = document.querySelector('.empty-state');

                if (show && !emptyRow) {
                    // Create empty message if it doesn't exist
                    const tbody = document.getElementById('roomsTableBody');
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                    <td colspan="5" class="text-center">
                        <div class="empty-state p-3">
                            <p class="m-0 text-muted small">Không tìm thấy kết quả nào phù hợp.</p>
                            <button class="btn btn-sm btn-outline-secondary mt-2" onclick="clearFilters()">
                                Xóa bộ lọc
                            </button>
                        </div>
                    </td>
                                                        `;
                    tbody.appendChild(newRow);
                } else if (!show && emptyRow) {
                    // Remove empty message if it exists
                    emptyRow.closest('tr').remove();
                }
            }

            // Clear all filters (called from empty message button)
            window.clearFilters = function () {
                searchInput.value = '';
                roomTypeFilter.value = '';
                theaterFilter.value = '';
                applyFilters();
            };
        });
    </script>
@endsection