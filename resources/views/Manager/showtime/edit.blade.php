@extends('Manager.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card card-frame border-0">
                <div class="card-header p-4 bg-gradient-primary text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-edit me-2"></i>Chỉnh Sửa Suất Chiếu</h4>
                            <p class="mb-0 opacity-8">Cập nhật thông tin suất chiếu #{{ $showtime->ShowtimeID }}</p>
                        </div>
                        <a href="{{ route('manager.showtimes.index') }}" class="btn btn-light btn-sm shadow-sm">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Có lỗi xảy ra!</h5>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Thông tin hiện tại -->
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fs-4 text-info"></i>
                            <div>
                                <h6 class="alert-heading mb-2">Thông tin hiện tại</h6>
                                <div class="row small">
                                    <div class="col-md-3"><strong>Phim:</strong> {{ $showtime->movie->Title ?? 'N/A' }}</div>
                                    <div class="col-md-3"><strong>Phòng:</strong> {{ $showtime->room->RoomName ?? 'N/A' }}</div>
                                    <div class="col-md-3"><strong>Bắt đầu:</strong> {{ \Carbon\Carbon::parse($showtime->StartTime)->format('d/m/Y H:i') }}</div>
                                    <div class="col-md-3"><strong>Giá vé:</strong> {{ number_format($showtime->Price, 0, ',', '.') }} VNĐ</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('manager.showtimes.update', $showtime->ShowtimeID) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Phim -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="MovieID" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-film me-2  text-success"></i>Phim <span class="text-danger">*</span>
                                    </label>
                                    <select name="MovieID" id="MovieID" class="form-select form-select-lg shadow-sm" required>
                                        <option value="" disabled>-- Chọn phim --</option>
                                        @foreach($movies as $movie)
                                            <option value="{{ $movie->MovieID }}" 
                                                {{ old('MovieID', $showtime->MovieID) == $movie->MovieID ? 'selected' : '' }}
                                                data-duration="{{ $movie->Duration }}">
                                                {{ $movie->Title }} ({{ $movie->Duration }} phút)
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>Vui lòng chọn phim.
                                    </div>
                                </div>
                            </div>

                            <!-- Phòng chiếu -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="RoomID" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-door-open me-2  text-success"></i>Phòng chiếu <span class="text-danger">*</span>
                                    </label>
                                    <select name="RoomID" id="RoomID" class="form-select form-select-lg shadow-sm" required>
                                        <option value="" disabled>-- Chọn phòng chiếu --</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->RoomID }}" 
                                                {{ old('RoomID', $showtime->RoomID) == $room->RoomID ? 'selected' : '' }}
                                                data-theater="{{ $room->theater->Name ?? '' }}"
                                                data-capacity="{{ $room->Capacity ?? '' }}">
                                                {{ $room->RoomName }} - {{ $room->theater->Name ?? 'Chưa gán rạp' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text mt-2">
                                        <small id="roomInfo" class="text-muted">
                                            @if($showtime->room)
                                                Sức chứa: {{ $showtime->room->Capacity }} chỗ
                                            @else
                                                Chọn phòng để xem thông tin chi tiết
                                            @endif
                                        </small>
                                    </div>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>Vui lòng chọn phòng chiếu.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Giờ bắt đầu -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="StartTime" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-clock me-2  text-success"></i>Giờ bắt đầu <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" name="StartTime" id="StartTime"
                                        class="form-control form-control-lg shadow-sm"
                                        value="{{ old('StartTime', \Carbon\Carbon::parse($showtime->StartTime)->format('Y-m-d\TH:i')) }}"
                                        min="{{ \Carbon\Carbon::now()->addHours(2)->format('Y-m-d\TH:i') }}"
                                        required>
                                    <div class="form-text mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i> 
                                            Giờ kết thúc sẽ được tính tự động theo thời lượng phim.
                                        </small>
                                    </div>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>Vui lòng chọn giờ bắt đầu hợp lệ.
                                    </div>
                                </div>
                            </div>

                            <!-- Giá vé -->
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="Price" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-ticket-alt me-2 text-muted"></i>Giá vé (VNĐ) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg shadow-sm">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-money-bill-wave text-success"></i>
                                        </span>
                                        <input type="number" name="Price" id="Price" class="form-control border-start-0" 
                                            min="0" step="1000" 
                                            value="{{ old('Price', $showtime->Price) }}" 
                                            placeholder="Nhập giá vé" required>
                                        <span class="input-group-text bg-light border-start-0">VNĐ</span>
                                    </div>
                                    <div class="form-text mt-2">
                                        <small class="text-muted">Giá vé tối thiểu: 50,000 VNĐ</small>
                                    </div>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>Vui lòng nhập giá vé hợp lệ.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label fw-semibold text-dark">
                                        <i class="fas fa-toggle-on me-2  text-success"></i>Trạng thái suất chiếu
                                    </label>
                                    <select name="Status" id="status" class="form-select form-select-lg shadow-sm">
                                        <option value="Scheduled" {{ old('Status', $showtime->Status) == 'Scheduled' ? 'selected' : '' }}>
                                            <i class="fas fa-clock me-1  text-success"></i> Sắp chiếu
                                        </option>
                                        <option value="Showing" {{ old('Status', $showtime->Status) == 'Showing' ? 'selected' : '' }}>
                                            <i class="fas fa-play-circle me-1 text-success"></i> Đang chiếu
                                        </option>
                                        <option value="Cancelled" {{ old('Status', $showtime->Status) == 'Cancelled' ? 'selected' : '' }}>
                                            <i class="fas fa-times-circle me-1 text-danger"></i> Đã hủy
                                        </option>
                                    </select>
                                    <div class="form-text mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-exclamation-triangle me-1  text-success"></i>
                                            Hủy suất chiếu sẽ không thể khôi phục
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Thông tin tự động -->
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-none h-100">
                                    <div class="card-body">
                                        <h6 class="mb-3  text-success">
                                            <i class="fas fa-calculator me-2"></i>Thông tin tự động
                                        </h6>
                                        <div class="row small">
                                            <div class="col-6">
                                                <small class="text-muted">Giờ kết thúc:</small>
                                                <div id="endTimePreview" class="fw-semibold text-dark">
                                                    {{ \Carbon\Carbon::parse($showtime->EndTime)->format('H:i d/m/Y') }}
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Rạp:</small>
                                                <div id="theaterPreview" class="fw-semibold text-dark">
                                                    {{ $showtime->room->theater->Name ?? '--' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="d-flex justify-content-end mt-4 pt-4 border-top">
                            <button type="reset" class="btn btn-outline-secondary me-3 px-4 btn-lg">
                                <i class="fas fa-undo me-2"></i> Đặt lại
                            </button>
                            <button type="submit" class="btn btn-success px-4 btn-lg shadow-sm">
                                <i class="fas fa-save me-2"></i> Cập nhật suất chiếu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-frame {
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    .form-control-lg, .form-select-lg {
        border-radius: 12px;
        padding: 14px 20px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .form-control-lg:focus, .form-select-lg:focus {
        border-color: #764ba2;
        box-shadow: 0 0 0 3px rgba(255, 179, 0, 0.1);
    }
    
    .btn {
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-lg {
        padding: 12px 30px;
    }
    
    .input-group-text {
        border-radius: 12px;
    }
    
    .alert {
        border-radius: 12px;
        border: none;
    }
    
    .form-group {
        position: relative;
    }
    
    .form-label {
        font-size: 0.9rem;
        margin-bottom: 8px;
        display: block;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>

<script>
    // Validation form
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()

    // Cập nhật thông tin phòng
    document.getElementById('RoomID').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const theater = selectedOption.getAttribute('data-theater');
        const capacity = selectedOption.getAttribute('data-capacity');
        
        document.getElementById('theaterPreview').textContent = theater || '--';
        document.getElementById('roomInfo').textContent = capacity ? `Sức chứa: ${capacity} chỗ` : 'Chọn phòng để xem thông tin chi tiết';
    });

    // Tính giờ kết thúc tự động
    function calculateEndTime() {
        const startTime = document.getElementById('StartTime').value;
        const movieSelect = document.getElementById('MovieID');
        const selectedMovie = movieSelect.options[movieSelect.selectedIndex];
        
        if (startTime && selectedMovie.value) {
            const duration = selectedMovie.getAttribute('data-duration');
            if (duration) {
                const startDate = new Date(startTime);
                const endDate = new Date(startDate.getTime() + parseInt(duration) * 60000);
                
                const formattedEnd = endDate.toLocaleString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                document.getElementById('endTimePreview').textContent = formattedEnd;
            }
        }
    }

    // Event listeners
    document.getElementById('StartTime').addEventListener('change', calculateEndTime);
    document.getElementById('MovieID').addEventListener('change', calculateEndTime);

    // Khởi tạo thông tin ban đầu
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('RoomID').dispatchEvent(new Event('change'));
        calculateEndTime();
    });
</script>
@endsection