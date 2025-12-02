@extends('manager.layouts.app')

@section('title', 'Quản Lý Voucher')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1 text-gray-800">
                    <i class="fas fa-ticket-alt text-primary me-2"></i>Quản Lý Voucher
                </h1>
                <p class="text-muted mb-0">Tạo và quản lý mã giảm giá cho khách hàng</p>
            </div>
            <a href="{{ route('manager.vouchers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tạo Voucher Mới
            </a>
        </div>

        <!-- Thông báo -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Thống kê nhanh -->
        <div class="row mb-3">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng Số Voucher
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalVouchers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Đang Hoạt Động
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeVouchers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow-sm h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Sắp Hết Hạn
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiringSoonVouchers }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách voucher -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="m-0 font-weight-bold text-gray-800">
                    <i class="fas fa-list me-2"></i>Danh Sách Voucher
                </h6>
                <span class="badge bg-light text-dark border">Tổng: {{ $vouchers->total() }} voucher</span>
            </div>
            <div class="card-body p-0">
                @if($vouchers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="dataTable" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%" class="ps-4">#</th>
                                    <th width="12%">Mã Voucher</th>
                                    <th width="10%">Loại Giảm</th>
                                    <th width="10%">Giá Trị</th>
                                    <th width="12%">Ngày Bắt Đầu</th>
                                    <th width="12%">Ngày Kết Thúc</th>
                                    <th width="8%">Trạng Thái</th>
                                    <th width="8%" class="text-center">Giới Hạn</th>
                                    <th width="8%" class="text-center">Đã Dùng</th>
                                    <th width="15%" class="text-center pe-4">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vouchers as $index => $voucher)
                                    <tr class="border-bottom">
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded border">{{ $voucher->Code }}</code>
                                        </td>
                                        <td>
                                            @if($voucher->DiscountType === 'Percent')
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fas fa-percentage me-1 text-warning"></i>Phần trăm
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark border">
                                                    <i class="fas fa-money-bill-wave me-1 text-success"></i>Tiền mặt
                                                </span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-gray-800">
                                            @if($voucher->DiscountType === 'Percent')
                                                {{ $voucher->Value }}%
                                            @else
                                                {{ number_format($voucher->Value, 0, ',', '.') }}₫
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($voucher->StartDate)->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($voucher->EndDate)->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($voucher->Status === 'Active')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                    <i class="fas fa-circle me-1 small"></i>Hoạt động
                                                </span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                                    <i class="fas fa-circle me-1 small"></i>Ngừng
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($voucher->UsageLimit)
                                                <span class="badge bg-light text-dark border">{{ $voucher->UsageLimit }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-gray-800">{{ $voucher->UsedCount ?? 0 }}</span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('manager.vouchers.show', $voucher->VoucherID) }}"
                                                    class="btn btn-outline-info border" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('manager.vouchers.edit', $voucher->VoucherID) }}"
                                                    class="btn btn-outline-warning border" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('manager.vouchers.destroy', $voucher->VoucherID) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa voucher {{ $voucher->Code }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger border" title="Xóa voucher">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    @if($vouchers->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Hiển thị {{ $vouchers->firstItem() }} - {{ $vouchers->lastItem() }} của {{ $vouchers->total() }} kết quả
                                </div>
                                {{ $vouchers->links() }}
                            </div>
                        </div>
                    @endif

                @else
                    <!-- Empty state -->
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-ticket-alt fa-4x text-gray-300"></i>
                        </div>
                        <h5 class="text-gray-500 mb-3">Chưa có voucher nào</h5>
                        <p class="text-gray-500 mb-4">Bắt đầu bằng cách tạo voucher đầu tiên cho khách hàng của bạn</p>
                        <a href="{{ route('manager.vouchers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tạo Voucher Đầu Tiên
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .card {
            border-radius: 0.5rem;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
        
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        
        .btn-outline-info:hover {
            background-color: #0dcaf0;
            border-color: #0dcaf0;
        }
        
        .btn-outline-warning:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
        }
        
        .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        code {
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
        
        .badge {
            font-weight: 500;
        }
    </style>
@endsection