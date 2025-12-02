@extends('Manager.layouts.app')

@section('title', 'Cập Nhật Trạng Thái - #' . str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Cập Nhật Trạng Thái</h1>
                    <p class="text-muted">Cập nhật trạng thái đặt vé</p>
                </div>
                <a href="{{ route('manager.bookings.show', $booking->BookingID) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay Lại
                </a>
            </div>

            <!-- Thông báo Flash -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Card chính -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Cập Nhật Trạng Thái Booking
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Thông tin booking -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="fw-bold text-gray-800 mb-3">Thông Tin Booking</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <span class="text-muted">Mã Booking:</span><br>
                                    <strong class="text-primary">#{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}</strong>
                                </p>
                                <p class="mb-2">
                                    <span class="text-muted">Khách hàng:</span><br>
                                    <strong>{{ $booking->customer->FullName ?? 'N/A' }}</strong>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <span class="text-muted">Phim:</span><br>
                                    <strong>{{ $booking->showtime->movie->Title ?? 'N/A' }}</strong>
                                </p>
                                <p class="mb-2">
                                    <span class="text-muted">Tổng tiền:</span><br>
                                    <strong class="text-success">{{ number_format($booking->TotalAmount, 0, ',', '.') }} VNĐ</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Form cập nhật -->
                    <form action="{{ route('manager.bookings.updateStatus', $booking->BookingID) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Trạng thái hiện tại -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-gray-700">Trạng Thái Hiện Tại</label>
                            <div class="p-3 rounded 
                                @if($booking->Status == 'Created') bg-warning bg-opacity-10
                                @elseif($booking->Status == 'Confirmed') bg-success bg-opacity-10
                                @elseif($booking->Status == 'Cancelled') bg-danger bg-opacity-10
                                @else bg-secondary bg-opacity-10 @endif">
                                @php
                                    $statusConfig = [
                                        'Created' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Chờ Xử Lý'],
                                        'Confirmed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Đã Xác Nhận'],
                                        'Cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Đã Hủy'],
                                    ];
                                    $currentStatus = $statusConfig[$booking->Status] ?? ['class' => 'secondary', 'icon' => 'question-circle', 'text' => $booking->Status];
                                @endphp
                                <span class="badge bg-{{ $currentStatus['class'] }} fs-6 d-inline-flex align-items-center">
                                    <i class="fas fa-{{ $currentStatus['icon'] }} me-2"></i>
                                    {{ $currentStatus['text'] }}
                                </span>
                                <small class="text-muted d-block mt-1">
                                    Cập nhật lần cuối: {{ $booking->updated_at->format('H:i d/m/Y') }}
                                </small>
                            </div>
                        </div>

                        <!-- Chọn trạng thái mới -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-bold text-gray-700">Chọn Trạng Thái Mới <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Chọn trạng thái --</option>
                                <option value="Created" {{ old('status', $booking->Status) == 'Created' ? 'selected' : '' }} data-icon="clock" data-color="warning">
                                    ⏳ Chờ Xử Lý
                                </option>
                                <option value="Confirmed" {{ old('status', $booking->Status) == 'Confirmed' ? 'selected' : '' }} data-icon="check-circle" data-color="success">
                                    ✅ Đã Xác Nhận
                                </option>
                                <option value="Cancelled" {{ old('status', $booking->Status) == 'Cancelled' ? 'selected' : '' }} data-icon="times-circle" data-color="danger">
                                    ❌ Đã Hủy
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview trạng thái mới -->
                        <div class="mb-4" id="statusPreview" style="display: none;">
                            <label class="form-label fw-bold text-gray-700">Trạng Thái Mới Sẽ Hiển Thị</label>
                            <div class="p-3 rounded" id="previewContent">
                                <!-- Nội dung preview sẽ được cập nhật bằng JavaScript -->
                            </div>
                        </div>

                        <!-- Ghi chú (tùy chọn) -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold text-gray-700">Ghi Chú</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3" placeholder="Thêm ghi chú về lý do thay đổi trạng thái (tùy chọn)...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Thông báo quan trọng -->
                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Lưu Ý Quan Trọng</h6>
                                    <ul class="mb-0 ps-3">
                                        <li>Khi chuyển sang trạng thái <strong>"Đã Xác Nhận"</strong>, hệ thống sẽ tự động gửi email xác nhận cho khách hàng</li>
                                        <li>Khi chuyển sang trạng thái <strong>"Đã Hủy"</strong>, vé sẽ không thể khôi phục</li>
                                        <li>Thay đổi trạng thái sẽ được ghi lại trong lịch sử hệ thống</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-save me-2"></i>Cập Nhật Trạng Thái
                            </button>
                            <a href="{{ route('manager.bookings.show', $booking->BookingID) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lịch sử trạng thái (nếu có) -->
            @if(isset($statusHistory) && $statusHistory->count() > 0)
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Lịch Sử Trạng Thái
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($statusHistory as $history)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-{{ $history->color ?? 'primary' }}"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-gray-800">{{ $history->status_text ?? $history->status }}</strong>
                                    <small class="text-muted">{{ $history->created_at->format('H:i d/m/Y') }}</small>
                                </div>
                                @if($history->notes)
                                <p class="text-muted mb-0 mt-1">{{ $history->notes }}</p>
                                @endif
                                <small class="text-muted">Bởi: {{ $history->user->name ?? 'Hệ thống' }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}
.timeline-item {
    position: relative;
    padding-bottom: 1rem;
}
.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    border-left: 3px solid #e9ecef;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const statusPreview = document.getElementById('statusPreview');
    const previewContent = document.getElementById('previewContent');

    function updatePreview() {
        const selectedOption = statusSelect.options[statusSelect.selectedIndex];
        const statusValue = selectedOption.value;
        const statusIcon = selectedOption.getAttribute('data-icon');
        const statusColor = selectedOption.getAttribute('data-color');
        const statusText = selectedOption.text.trim();

        if (statusValue) {
            const statusConfig = {
                'Created': { class: 'warning', icon: 'clock', text: 'Chờ Xử Lý' },
                'Confirmed': { class: 'success', icon: 'check-circle', text: 'Đã Xác Nhận' },
                'Cancelled': { class: 'danger', icon: 'times-circle', text: 'Đã Hủy' }
            };

            const config = statusConfig[statusValue] || { class: 'secondary', icon: 'question-circle', text: statusValue };

            previewContent.innerHTML = `
                <span class="badge bg-${config.class} fs-6 d-inline-flex align-items-center">
                    <i class="fas fa-${config.icon} me-2"></i>
                    ${config.text}
                </span>
                <small class="text-muted d-block mt-2">
                    Booking sẽ được chuyển sang trạng thái này sau khi cập nhật
                </small>
            `;
            previewContent.parentElement.className = `p-3 rounded bg-${config.class} bg-opacity-10`;
            statusPreview.style.display = 'block';
        } else {
            statusPreview.style.display = 'none';
        }
    }

    statusSelect.addEventListener('change', updatePreview);
    
    // Khởi tạo preview khi trang load
    updatePreview();
});
</script>
@endsection