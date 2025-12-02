@extends('manager.layouts.app')

@section('title', 'Danh sách Thành viên')

@section('content')
<div class="container py-4">
    
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 text-dark">
                <i class="fas fa-users text-primary me-2"></i>Quản lý Thành viên
            </h2>
            <p class="text-muted mb-0 small">Quản lý thông tin các thành viên trong hệ thống</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Đã loại bỏ nút Tặng Voucher Hàng Loạt theo yêu cầu --}}
            <a href="{{ route('manager.member.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus me-1"></i> Thêm Thành viên
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 py-2 small" role="alert">
            <i class="fas fa-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 py-2 small" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter and Search Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Tìm kiếm</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Tìm theo tên, email hoặc SĐT...">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Trạng thái</label>
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option value="">Tất cả trạng thái</option>
                        <option value="Active">Active</option>
                        <option value="Banned">Banned</option>
                        <option value="Unknown">Unknown</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="clearFilters" class="btn btn-outline-secondary btn-sm w-100" style="display: none;">
                        <i class="fas fa-times me-1"></i> Xóa lọc
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="card shadow-sm border-0">
        @if($customers->isEmpty())
            <div class="card-body text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-users display-4 text-muted mb-3"></i>
                    <h5 class="text-muted mb-2">Chưa có thành viên nào</h5>
                    <p class="text-muted small mb-3">Hãy thêm thành viên đầu tiên vào hệ thống</p>
                    <a href="{{ route('manager.member.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Thêm thành viên
                    </a>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 small text-uppercase text-muted fw-semibold">ID</th>
                            <th class="small text-uppercase text-muted fw-semibold">Thành viên</th>
                            <th class="small text-uppercase text-muted fw-semibold">Email</th>
                            <th class="small text-uppercase text-muted fw-semibold">SĐT</th>
                            <th class="small text-uppercase text-muted fw-semibold">Voucher</th>
                            <th class="small text-uppercase text-muted fw-semibold">Trạng thái</th>
                            <th class="text-end pe-3 small text-uppercase text-muted fw-semibold">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="memberTableBody">
                        @foreach($customers as $customer)
                        <tr class="member-row" 
                            {{-- We keep strtolower in PHP to ensure base-level case-insensitivity --}}
                            data-fullname="{{ strtolower($customer->FullName ?? '') }}"
                            data-email="{{ strtolower(optional($customer->user)->Email ?? 'n/a') }}"
                            data-phone="{{ strtolower($customer->Phone ?? 'n/a') }}"
                            data-status="{{ optional($customer->user)->Status ?? 'Unknown' }}">
                            
                            <!-- ID -->
                            <td class="ps-3">
                                <span class="text-muted small">#{{ $customer->CustomerID }}</span>
                            </td>
                            
                            <!-- Full Name -->
                            <td>
                                <div class="fw-medium text-dark">{{ $customer->FullName }}</div>
                            </td>
                            
                            <!-- Email -->
                            <td>
                                <span class="small text-muted">{{ optional($customer->user)->Email ?? 'N/A' }}</span>
                            </td>
                            
                            <!-- Phone -->
                            <td>
                                <span class="small text-muted">{{ $customer->Phone ?? 'Chưa cung cấp' }}</span>
                            </td>

                            <!-- Voucher Count -->
                            <td>
                                @php
                                    $unusedVouchers = optional($customer->customerVouchers)->where('IsUsed', 0)->count() ?? 0;
                                @endphp
                                <span class="badge {{ $unusedVouchers > 0 ? 'bg-info bg-opacity-10 text-info border border-info border-opacity-25' : 'bg-light text-muted border' }} small">
                                    {{ $unusedVouchers }} voucher
                                </span>
                            </td>
                            
                            <!-- Status -->
                            <td>
                                @php
                                    $status = optional($customer->user)->Status ?? 'Unknown';
                                    $badgeClass = match($status) {
                                        'Active' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                                        'Banned' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
                                        default => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} small">{{ $status }}</span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <!-- Voucher Management -->
                                    <a href="{{ route('manager.member.show', $customer->CustomerID) }}" 
                                        class="btn btn-outline-primary btn-xs rounded-1 px-2 py-1"
                                        data-bs-toggle="tooltip" title="Quản lý Voucher">
                                        <i class="fas fa-ticket-alt small"></i>
                                    </a>
                                    
                                    <!-- Edit -->
                                    <a href="{{ route('manager.member.edit', $customer->CustomerID) }}" 
                                        class="btn btn-outline-secondary btn-xs rounded-1 px-2 py-1"
                                        data-bs-toggle="tooltip" title="Chỉnh sửa thông tin">
                                        <i class="fas fa-edit small"></i>
                                    </a>
                                    
                                    <!-- Delete (Using Modal Trigger) -->
                                    {{-- Replaced the forbidden onclick="return confirm(...)" with a modal trigger --}}
                                    <button type="button" 
                                        class="btn btn-outline-danger btn-xs rounded-1 px-2 py-1 delete-member-btn"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteConfirmationModal"
                                        data-member-id="{{ $customer->CustomerID }}" 
                                        data-member-name="{{ $customer->FullName }}"
                                        data-bs-toggle="tooltip" title="Xóa thành viên">
                                        <i class="fas fa-trash-alt small"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Results Counter -->
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Hiển thị <span id="visibleCount" class="fw-semibold">{{ $customers->count() }}</span> 
                        của {{ $customers->count() }} thành viên
                    </div>
                    @if($customers->count() > 10)
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Sử dụng bộ lọc để tìm kiếm nhanh
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Đã loại bỏ Mass Assign Voucher Modal --}}

<!-- Delete Confirmation Modal (NEW) -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title" id="deleteConfirmationModalLabel">Xác nhận Xóa</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteMemberForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body small text-center">
                    <i class="fas fa-exclamation-triangle text-danger display-6 mb-3"></i>
                    <p class="mb-0">Bạn có chắc chắn muốn xóa thành viên <strong id="memberNamePlaceholder"></strong>?</p>
                    <p class="text-muted mt-1 small">Thao tác này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger btn-sm fw-semibold">
                        <i class="fas fa-trash-alt me-1"></i> Xóa vĩnh viễn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* ... (CSS remains the same) ... */
    .table {
        font-size: 0.875rem;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .table td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
    }
    
    .empty-state {
        padding: 2rem 1rem;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        font-weight: 500;
    }
    
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .form-control-sm, .form-select-sm {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .alert {
        font-size: 0.875rem;
    }
    
    .card {
        border-radius: 0.5rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // -----------------------------------------------------
    // 1. Tooltip Initialization
    // -----------------------------------------------------
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // -----------------------------------------------------
    // 2. Diacritic Insensitive Search Utility
    // -----------------------------------------------------
    /**
     * Removes diacritics (accents/tones) from a string, 
     * essential for robust Vietnamese search functionality.
     */
    function removeDiacritics(str) {
        if (!str) return '';
        // Normalize: decomposes combined characters (e.g., 'é' to 'e' + accent mark)
        // Regex: removes all combining diacritical marks (\u0300-\u036f)
        return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }


    // -----------------------------------------------------
    // 3. Filtering Logic
    // -----------------------------------------------------
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const memberRows = document.querySelectorAll('.member-row');
    const visibleCountElement = document.getElementById('visibleCount');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const totalCount = memberRows.length;
    const tableBody = document.getElementById('memberTableBody');

    // Add event listeners for filtering
    searchInput.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    clearFiltersBtn.addEventListener('click', clearFilters);

    // Function to apply all filters
    function applyFilters() {
        // Normalize search text once for case and diacritic insensitivity
        const rawSearchText = searchInput.value.toLowerCase().trim();
        const searchText = removeDiacritics(rawSearchText); 
        const selectedStatus = statusFilter.value;
        let visibleCount = 0;

        memberRows.forEach(row => {
            // Data attributes are already lowercased in PHP for basic case-insensitivity
            const rawFullName = row.getAttribute('data-fullname');
            const rawEmail = row.getAttribute('data-email');
            const rawPhone = row.getAttribute('data-phone');
            const status = row.getAttribute('data-status');

            // Normalize row data for diacritic-insensitive search
            const normalizedFullName = removeDiacritics(rawFullName);
            const normalizedEmail = removeDiacritics(rawEmail);
            const normalizedPhone = removeDiacritics(rawPhone);

            // Check Search (match Normalized FullName OR Email OR Phone)
            // We compare the normalized row data (e.g., 'nguyen van a') 
            // with the normalized search text (e.g., 'nguyen')
            const matchesSearch = 
                !searchText ||
                normalizedFullName.includes(searchText) || 
                normalizedEmail.includes(searchText) || 
                normalizedPhone.includes(searchText);

            // Check Status Filter
            const matchesStatus = selectedStatus === '' || status === selectedStatus;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update visible count
        if (visibleCountElement) {
            visibleCountElement.textContent = visibleCount;
        }

        // Show/hide clear filters button
        const isFiltered = rawSearchText || selectedStatus;
        clearFiltersBtn.style.display = isFiltered ? 'block' : 'none';

        // Show empty message if no results
        showEmptyMessage(visibleCount === 0 && totalCount > 0);
    }

    // Function to show/hide empty message
    function showEmptyMessage(show) {
        let emptyRow = tableBody.querySelector('.empty-filter-state');
        
        // Remove existing empty message if conditions change
        if (emptyRow) {
            emptyRow.remove();
        }

        if (show) {
            // Create empty filter message
            const newRow = document.createElement('tr');
            newRow.classList.add('empty-filter-state');
            newRow.innerHTML = `
                <td colspan="7" class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-search display-4 text-muted mb-3"></i>
                        <p class="text-muted mb-2">Không tìm thấy thành viên nào phù hợp</p>
                        <p class="text-muted small">Hãy thử điều chỉnh từ khóa tìm kiếm hoặc bộ lọc</p>
                    </div>
                </td>
            `;
            tableBody.appendChild(newRow);
        }
    }

    // Clear all filters
    function clearFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        applyFilters();
    }

    // Initialize filters
    applyFilters();


    // -----------------------------------------------------
    // 4. Delete Modal Logic
    // -----------------------------------------------------
    const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
    if (deleteConfirmationModal) {
        deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;
            
            // Extract info from data-bs-* attributes
            const memberId = button.getAttribute('data-member-id');
            const memberName = button.getAttribute('data-member-name');
            const deleteForm = document.getElementById('deleteMemberForm');
            const namePlaceholder = document.getElementById('memberNamePlaceholder');

            // Update the form's action URL and modal content
            if (deleteForm && namePlaceholder) {
                // Assuming route('manager.member.destroy', [id]) expects the ID
                // Note: You must ensure this base path is correct for your Laravel setup
                const actionUrl = `/manager/members/${memberId}`; 

                deleteForm.action = actionUrl;
                namePlaceholder.textContent = memberName;
            }
        });
    }
});
</script>
@endsection
