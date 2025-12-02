@extends('Manager.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-gradient text-primary">
                    <i class="fas fa-chair me-2"></i>Quản Lý Ghế
                </h2>
                <p class="mb-0 text-muted">Quản lý danh sách ghế theo từng phòng chiếu</p>
            </div>
            <a href="{{ route('manager.seats.create') }}" class="btn btn-success btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i> Thêm Ghế
            </a>
        </div>

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Thống kê nhanh --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Tổng số phòng</h6>
                                <h3 class="mb-0 mt-2">{{ $rooms->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-door-open fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Ghế Standard</h6>
                                <h3 class="mb-0 mt-2">{{ $totalSeatsByType['Standard'] ?? 0 }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chair fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Ghế VIP</h6>
                                <h3 class="mb-0 mt-2">{{ $totalSeatsByType['VIP'] ?? 0 }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-crown fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-0">Ghế Couple</h6>
                                <h3 class="mb-0 mt-2">{{ $totalSeatsByType['Couple'] ?? 0 }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-heart fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danh sách phòng và ghế --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-3">
                <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Danh sách Ghế Theo Phòng</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Phòng Chiếu</th>
                                <th>Rạp</th>
                                <th>Ghế Standard</th>
                                <th>Ghế VIP</th>
                                <th>Ghế Couple</th>
                                <th>Tổng Ghế</th>
                                <th class="text-center pe-4">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                                @php
                                    $seatsByType = $room->seats->groupBy('SeatType');
                                    $totalSeats = $room->seats->count();
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="fas fa-door-open text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $room->RoomName }}</h6>
                                                <small class="text-muted">Sức chứa: {{ $room->Capacity }} chỗ</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-building me-1 text-primary"></i>
                                            {{ $room->theater->Name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(isset($seatsByType['Standard']))
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">{{ $seatsByType['Standard']->count() }}</span>
                                                <small class="text-muted">
                                                    {{ $seatsByType['Standard']->pluck('SeatNumber')->slice(0, 3)->join(', ') }}
                                                    @if($seatsByType['Standard']->count() > 3)
                                                        ...
                                                    @endif
                                                </small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($seatsByType['VIP']))
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-info me-2">{{ $seatsByType['VIP']->count() }}</span>
                                                <small class="text-muted">
                                                    {{ $seatsByType['VIP']->pluck('SeatNumber')->slice(0, 2)->join(', ') }}
                                                    @if($seatsByType['VIP']->count() > 2)
                                                        ...
                                                    @endif
                                                </small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($seatsByType['Couple']))
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-warning me-2">{{ $seatsByType['Couple']->count() }}</span>
                                                <small class="text-muted">
                                                    {{ $seatsByType['Couple']->pluck('SeatNumber')->slice(0, 2)->join(', ') }}
                                                    @if($seatsByType['Couple']->count() > 2)
                                                        ...
                                                    @endif
                                                </small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">{{ $room->Capacity }}</span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('manager.seats.edit', $room->RoomID) }}"
                                                class="btn btn-outline-primary btn-sm" title="Quản lý ghế">
                                                <i class="fas fa-edit me-1"></i> Quản lý
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-chair fa-3x text-light mb-3"></i>
                                            <h5 class="text-muted">Chưa có phòng chiếu nào</h5>
                                            <p class="text-muted">Hãy thêm phòng chiếu và ghế để bắt đầu quản lý</p>
                                            <a href="{{ route('manager.seats.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus me-1"></i> Thêm Ghế Đầu Tiên
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
    </style>

    <script>


        // Khởi tạo tooltip
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection