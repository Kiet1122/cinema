@extends('Manager.layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-white text-primary rounded-circle p-2 me-3">
                                <i class="fas fa-edit fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">Chỉnh sửa Ghế - {{ $room->RoomName }}</h4>
                                <small class="opacity-75">Cập nhật thông tin ghế trong phòng chiếu</small>
                            </div>
                        </div>
                        <a href="{{ route('manager.seats.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <h6 class="fw-bold mb-0">Có lỗi xảy ra:</h6>
                            </div>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('manager.seats.update', $room->RoomID) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="fas fa-info-circle me-2 text-primary"></i>Thông Tin Phòng
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Tên Phòng</label>
                                                    <p class="form-control-plaintext fw-bold text-primary">{{ $room->RoomName }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Sức Chứa</label>
                                                    <p class="form-control-plaintext fw-bold" id="roomCapacityDisplay">{{ $room->Capacity }} ghế</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="fas fa-sliders-h me-2 text-primary"></i>Điều Chỉnh Số Lượng & Giá Ghế
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Ghế Standard -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all bg-light-primary">
                                                    <div class="card-body text-center">
                                                        <div class="bg-primary rounded-circle p-3 d-inline-flex mb-3">
                                                            <i class="fas fa-chair text-white fa-xl"></i>
                                                        </div>
                                                        <h6 class="fw-bold text-dark mb-2">Ghế Standard</h6>
                                                        <input type="number" name="Standard" id="Standard" 
                                                               class="form-control text-center border-2 mb-2" 
                                                               value="{{ old('Standard', $standardCount) }}" min="0" required>
                                                        <small class="text-muted mb-2 d-block">Số lượng</small>
                                                        
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text">₫</span>
                                                            <input type="number" name="StandardPrice" id="StandardPrice" 
                                                                   class="form-control text-center" 
                                                                   value="{{ old('StandardPrice', $standardPrice) }}" min="0" step="1000" required>
                                                        </div>
                                                        <small class="text-muted d-block">Giá (VND)</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Ghế VIP -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all bg-light-warning">
                                                    <div class="card-body text-center">
                                                        <div class="bg-warning rounded-circle p-3 d-inline-flex mb-3">
                                                            <i class="fas fa-crown text-white fa-xl"></i>
                                                        </div>
                                                        <h6 class="fw-bold text-dark mb-2">Ghế VIP</h6>
                                                        <input type="number" name="VIP" id="VIP" 
                                                               class="form-control text-center border-2 mb-2" 
                                                               value="{{ old('VIP', $vipCount) }}" min="0" required>
                                                        <small class="text-muted mb-2 d-block">Số lượng</small>
                                                        
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text">₫</span>
                                                            <input type="number" name="VIPPrice" id="VIPPrice" 
                                                                   class="form-control text-center" 
                                                                   value="{{ old('VIPPrice', $vipPrice) }}" min="0" step="1000" required>
                                                        </div>
                                                        <small class="text-muted d-block">Giá (VND)</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Ghế Couple -->
                                            <div class="col-md-4">
                                                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all bg-light-danger">
                                                    <div class="card-body text-center">
                                                        <div class="bg-danger rounded-circle p-3 d-inline-flex mb-3">
                                                            <i class="fas fa-heart text-white fa-xl"></i>
                                                        </div>
                                                        <h6 class="fw-bold text-dark mb-2">Ghế Couple</h6>
                                                        <input type="number" name="Couple" id="Couple" 
                                                               class="form-control text-center border-2 mb-2" 
                                                               value="{{ old('Couple', $coupleCount) }}" min="0" required>
                                                        <small class="text-muted mb-2 d-block">Số lượng</small>
                                                        
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text">₫</span>
                                                            <input type="number" name="CouplePrice" id="CouplePrice" 
                                                                   class="form-control text-center" 
                                                                   value="{{ old('CouplePrice', $couplePrice) }}" min="0" step="1000" required>
                                                        </div>
                                                        <small class="text-muted d-block">Giá (VND)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Thống kê tổng ghế và doanh thu -->
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body">
                                                        <div class="row text-center">
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <small class="text-muted">Tổng ghế đã thêm:</small>
                                                                    <div class="fw-bold fs-5 text-primary" id="totalSeatsAdded">0 ghế</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <small class="text-muted">Ghế còn trống:</small>
                                                                    <div class="fw-bold fs-5" id="remainingSeats">0 ghế</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <small class="text-muted">Tỷ lệ sử dụng:</small>
                                                                    <div class="fw-bold fs-5" id="usagePercentage">0%</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <small class="text-muted">Doanh thu tiềm năng:</small>
                                                                    <div class="fw-bold fs-5 text-success" id="potentialRevenue">0 ₫</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="progress mt-2" style="height: 10px;">
                                                            <div id="capacityProgress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info border-0 mt-4 d-flex align-items-center">
                                            <i class="fas fa-lightbulb text-info me-2 fa-lg"></i>
                                            <div>
                                                <strong>Lưu ý:</strong> Mỗi ghế đôi (Couple) được tính là 2 chỗ ngồi. 
                                                Tổng số ghế không được vượt quá sức chứa của phòng.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3 justify-content-end mt-4 pt-3 border-top">
                                    <a href="{{ route('manager.seats.index') }}" class="btn btn-outline-secondary px-5 py-2 fw-semibold">
                                        <i class="fas fa-times me-2"></i>Hủy bỏ
                                    </a>
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold gradient-btn">
                                        <i class="fas fa-save me-2"></i>Cập nhật Ghế
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="fas fa-projector me-2 text-primary"></i>Sơ Đồ Phòng Chiếu
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Màn hình -->
                                        <div class="text-center mb-4">
                                            <div class="bg-dark text-white py-2 rounded mx-auto" style="max-width: 300px;">
                                                <i class="fas fa-film me-2"></i>MÀN HÌNH
                                            </div>
                                        </div>

                                        <!-- Sơ đồ ghế -->
                                        <div class="seating-chart-container" style="max-height: 500px; overflow-y: auto;">
                                            <div class="seating-chart">
                                                <!-- Hàng ghế Standard -->
                                                @if($standardCount > 0)
                                                <div class="row-seat mb-3">
                                                    <div class="row-label fw-bold text-primary mb-2">
                                                        S - STANDARD 
                                                        <small class="text-muted">({{ number_format($standardPrice, 0, ',', '.') }} ₫)</small>
                                                    </div>
                                                    <div class="seats-row d-flex justify-content-center flex-wrap gap-2">
                                                        @for($i = 1; $i <= $standardCount; $i++)
                                                            <div class="seat standard-seat" data-bs-toggle="tooltip" 
                                                                 title="Ghế Standard {{ $i }} - {{ number_format($standardPrice, 0, ',', '.') }} ₫">
                                                                S{{ $i }}
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Hàng ghế VIP -->
                                                @if($vipCount > 0)
                                                <div class="row-seat mb-3">
                                                    <div class="row-label fw-bold text-warning mb-2">
                                                        V - VIP 
                                                        <small class="text-muted">({{ number_format($vipPrice, 0, ',', '.') }} ₫)</small>
                                                    </div>
                                                    <div class="seats-row d-flex justify-content-center flex-wrap gap-2">
                                                        @for($i = 1; $i <= $vipCount; $i++)
                                                            <div class="seat vip-seat" data-bs-toggle="tooltip" 
                                                                 title="Ghế VIP {{ $i }} - {{ number_format($vipPrice, 0, ',', '.') }} ₫">
                                                                V{{ $i }}
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Hàng ghế Couple -->
                                                @if($coupleCount > 0)
                                                <div class="row-seat mb-3">
                                                    <div class="row-label fw-bold text-danger mb-2">
                                                        C - COUPLE 
                                                        <small class="text-muted">({{ number_format($couplePrice, 0, ',', '.') }} ₫)</small>
                                                    </div>
                                                    <div class="seats-row d-flex justify-content-center flex-wrap gap-2">
                                                        @for($i = 1; $i <= $coupleCount; $i++)
                                                            <div class="seat couple-seat" data-bs-toggle="tooltip" 
                                                                 title="Ghế Couple {{ $i }} - {{ number_format($couplePrice, 0, ',', '.') }} ₫">
                                                                C{{ $i }}
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Chú thích -->
                                        <div class="legend mt-4 pt-3 border-top">
                                            <h6 class="fw-bold mb-3">Chú Thích:</h6>
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat-legend standard-seat me-2"></div>
                                                        <div>
                                                            <small>Standard (S)</small>
                                                            <div class="fw-bold text-primary">{{ number_format($standardPrice, 0, ',', '.') }} ₫</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat-legend vip-seat me-2"></div>
                                                        <div>
                                                            <small>VIP (V)</small>
                                                            <div class="fw-bold text-warning">{{ number_format($vipPrice, 0, ',', '.') }} ₫</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="seat-legend couple-seat me-2"></div>
                                                        <div>
                                                            <small>Couple (C)</small>
                                                            <div class="fw-bold text-danger">{{ number_format($couplePrice, 0, ',', '.') }} ₫</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-primary { background-color: rgba(67, 97, 238, 0.08); }
    .bg-light-warning { background-color: rgba(252, 163, 17, 0.08); }
    .bg-light-danger { background-color: rgba(230, 57, 70, 0.08); }
    
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
    }
    
    .transition-all {
        transition: all 0.3s ease;
    }
    
    .gradient-btn {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border: none;
        font-size: 1.1rem;
    }
    
    .gradient-btn:hover {
        background: linear-gradient(135deg, #3a0ca3 0%, #4361ee 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.25);
    }
    
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    /* Sơ đồ ghế */
    .seat {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: bold;
        cursor: default;
        transition: all 0.3s ease;
    }
    
    .standard-seat {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        color: white;
        border: 2px solid #4361ee;
    }
    
    .vip-seat {
        background: linear-gradient(135deg, #fca311, #e85d04);
        color: white;
        border: 2px solid #fca311;
    }
    
    .couple-seat {
        background: linear-gradient(135deg, #e63946, #d00000);
        color: white;
        border: 2px solid #e63946;
        width: 60px !important;
    }
    
    .seat:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .row-seat {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #dee2e6;
    }
    
    .row-label {
        font-size: 0.9rem;
        text-align: center;
    }
    
    .seat-legend {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 2px solid;
    }
    
    .seat-legend.standard-seat {
        background: #4361ee;
        border-color: #3a0ca3;
    }
    
    .seat-legend.vip-seat {
        background: #fca311;
        border-color: #e85d04;
    }
    
    .seat-legend.couple-seat {
        background: #e63946;
        border-color: #d00000;
    }
    
    .sticky-top {
        position: sticky;
        z-index: 10;
    }
    
    .progress-bar {
        transition: width 0.5s ease;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.needs-validation');
        const seatInputs = document.querySelectorAll('input[type="number"]');
        const priceInputs = document.querySelectorAll('input[name$="Price"]');
        const roomCapacity = {{ $room->Capacity }};
        
        // Format tiền tệ
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { 
                style: 'currency', 
                currency: 'VND' 
            }).format(amount);
        }
        
        // Cập nhật thống kê
        function updateStatistics() {
            const standardCount = parseInt(document.getElementById('Standard').value) || 0;
            const vipCount = parseInt(document.getElementById('VIP').value) || 0;
            const coupleCount = parseInt(document.getElementById('Couple').value) || 0;
            const standardPrice = parseInt(document.getElementById('StandardPrice').value) || 0;
            const vipPrice = parseInt(document.getElementById('VIPPrice').value) || 0;
            const couplePrice = parseInt(document.getElementById('CouplePrice').value) || 0;
            
            // Tính tổng số ghế (ghế couple tính 2 chỗ)
            const totalSeats = standardCount + vipCount + coupleCount;
            const remainingSeats = roomCapacity - totalSeats;
            const usagePercentage = roomCapacity > 0 ? Math.min(100, (totalSeats / roomCapacity) * 100) : 0;
            
            // Tính doanh thu tiềm năng
            const potentialRevenue = (standardCount * standardPrice) + 
                                   (vipCount * vipPrice) + 
                                   (coupleCount * couplePrice);
            
            // Cập nhật UI
            document.getElementById('totalSeatsAdded').textContent = totalSeats.toLocaleString() + ' ghế';
            document.getElementById('remainingSeats').textContent = remainingSeats.toLocaleString() + ' ghế';
            document.getElementById('usagePercentage').textContent = usagePercentage.toFixed(1) + '%';
            document.getElementById('potentialRevenue').textContent = formatCurrency(potentialRevenue);
            
            // Cập nhật progress bar
            const progressBar = document.getElementById('capacityProgress');
            progressBar.style.width = usagePercentage + '%';
            
            // Đổi màu progress bar theo mức độ
            if (usagePercentage >= 100) {
                progressBar.className = 'progress-bar bg-danger';
                document.getElementById('remainingSeats').classList.add('text-danger');
            } else if (usagePercentage >= 80) {
                progressBar.className = 'progress-bar bg-warning';
                document.getElementById('remainingSeats').classList.remove('text-danger');
            } else {
                progressBar.className = 'progress-bar bg-success';
                document.getElementById('remainingSeats').classList.remove('text-danger');
            }
            
            // Hiển thị cảnh báo màu đỏ nếu vượt quá capacity
            if (totalSeats > roomCapacity) {
                document.getElementById('totalSeatsAdded').classList.add('text-danger');
                document.getElementById('potentialRevenue').classList.add('text-danger');
            } else {
                document.getElementById('totalSeatsAdded').classList.remove('text-danger');
                document.getElementById('potentialRevenue').classList.remove('text-danger');
            }
        }
        
        // Cập nhật sơ đồ ghế real-time
        function updateSeatingChart() {
            const standardCount = parseInt(document.getElementById('Standard').value) || 0;
            const vipCount = parseInt(document.getElementById('VIP').value) || 0;
            const coupleCount = parseInt(document.getElementById('Couple').value) || 0;
            const standardPrice = parseInt(document.getElementById('StandardPrice').value) || 0;
            const vipPrice = parseInt(document.getElementById('VIPPrice').value) || 0;
            const couplePrice = parseInt(document.getElementById('CouplePrice').value) || 0;
            
            // Cập nhật hàng ghế Standard
            updateSeatRow('.row-seat:first-child', 'S', standardCount, 'standard', standardPrice);
            
            // Cập nhật hàng ghế VIP
            updateSeatRow('.row-seat:nth-child(2)', 'V', vipCount, 'vip', vipPrice);
            
            // Cập nhật hàng ghế Couple
            updateSeatRow('.row-seat:last-child', 'C', coupleCount, 'couple', couplePrice);
            
            // Cập nhật thống kê
            updateStatistics();
        }
        
        function updateSeatRow(selector, prefix, count, type, price) {
            const row = document.querySelector(selector);
            if (!row) return;
            
            const seatsContainer = row.querySelector('.seats-row');
            const rowLabel = row.querySelector('.row-label');
            
            // Cập nhật giá trong label
            rowLabel.innerHTML = `${prefix} - ${type.toUpperCase()} <small class="text-muted">(${formatCurrency(price)})</small>`;
            
            // Xóa ghế hiện tại
            seatsContainer.innerHTML = '';
            
            // Thêm ghế mới
            for (let i = 1; i <= count; i++) {
                const seat = document.createElement('div');
                seat.className = `seat ${type}-seat`;
                seat.setAttribute('data-bs-toggle', 'tooltip');
                seat.setAttribute('title', `Ghế ${type} ${i} - ${formatCurrency(price)}`);
                seat.textContent = `${prefix}${i}`;
                seatsContainer.appendChild(seat);
            }
            
            // Ẩn/hiện hàng ghế
            if (count === 0) {
                row.style.display = 'none';
            } else {
                row.style.display = 'block';
            }
        }
        
        // Validation form
        form.addEventListener('submit', function(event) {
            const standardCount = parseInt(document.getElementById('Standard').value) || 0;
            const vipCount = parseInt(document.getElementById('VIP').value) || 0;
            const coupleCount = parseInt(document.getElementById('Couple').value) || 0;
            
            const totalSeats = standardCount + vipCount + coupleCount;
            
            if (totalSeats > roomCapacity) {
                event.preventDefault();
                event.stopPropagation();
                alert(`Tổng số ghế (${totalSeats}) vượt quá sức chứa của phòng (${roomCapacity}). Vui lòng điều chỉnh lại.`);
            }
            
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
        
        // Lắng nghe sự kiện thay đổi số lượng ghế và giá
        seatInputs.forEach(input => {
            input.addEventListener('input', updateSeatingChart);
        });
        
        priceInputs.forEach(input => {
            input.addEventListener('input', updateSeatingChart);
        });
        
        // Khởi tạo tooltip
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Khởi tạo thống kê ban đầu
        updateStatistics();
    });
</script>
@endsection