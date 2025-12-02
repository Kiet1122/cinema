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
                                    <i class="fas fa-plus fa-lg"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold">Thêm Ghế Mới</h4>
                                    <small class="opacity-75">Quản lý hệ thống ghế trong phòng chiếu</small>
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

                        <form action="{{ route('manager.seats.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="mb-4">
                                        <label for="RoomID" class="form-label fw-semibold text-dark mb-2">
                                            <i class="fas fa-door-open text-primary me-2"></i>Chọn Phòng Chiếu
                                        </label>
                                        <select name="RoomID" id="RoomID"
                                            class="form-select form-select-lg border-2 shadow-sm" required>
                                            <option value="" disabled selected>-- Chọn phòng chiếu --</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->RoomID }}" {{ old('RoomID') == $room->RoomID ? 'selected' : '' }} class="py-2">
                                                    {{ $room->RoomName }} (Sức chứa: {{ $room->Capacity }} ghế)
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-muted mt-1">
                                            <i class="fas fa-info-circle me-1"></i>Vui lòng chọn phòng chiếu để thêm ghế
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold text-dark mb-3">
                                            <i class="fas fa-chair text-primary me-2"></i>Phân Loại Ghế
                                        </label>
                                        <div class="row g-3">
                                            <div class="col-xl-4 col-md-6">
                                                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                                    <div class="card-body text-center">
                                                        <div class="bg-light-primary rounded-circle p-3 d-inline-flex mb-3">
                                                            <i class="fas fa-chair text-primary fa-xl"></i>
                                                        </div>
                                                        <h6 class="fw-bold text-dark mb-2">Ghế Thường</h6>
                                                        <input type="number" name="Standard" id="Standard"
                                                            class="form-control form-control-lg text-center border-2 mb-2"
                                                            value="{{ old('Standard', 0) }}" min="0" required>
                                                        <label class="form-label small text-muted mb-1">Giá (VND)</label>
                                                        <input type="number" name="StandardPrice" id="StandardPrice"
                                                            class="form-control form-control-lg text-center border-2"
                                                            value="{{ old('StandardPrice', 50000) }}" min="0" step="1000" required>
                                                        <small class="text-muted mt-2 d-block">Standard Seats</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-md-6">
                                                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                                    <div class="card-body text-center">
                                                        <div class="bg-light-warning rounded-circle p-3 d-inline-flex mb-3">
                                                            <i class="fas fa-crown text-warning fa-xl"></i>
                                                        </div>
                                                        <h6 class="fw-bold text-dark mb-2">Ghế VIP</h6>
                                                        <input type="number" name="VIP" id="VIP"
                                                            class="form-control form-control-lg text-center border-2 mb-2"
                                                            value="{{ old('VIP', 0) }}" min="0" >
                                                        <label class="form-label small text-muted mb-1">Giá (VND)</label>
                                                        <input type="number" name="VIPPrice" id="VIPPrice"
                                                            class="form-control form-control-lg text-center border-2"
                                                            value="{{ old('VIPPrice', 80000) }}" min="0" step="1000" required>
                                                        <small class="text-muted mt-2 d-block">VIP Seats</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-md-6">
                                                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                                    <div class="card-body text-center">
                                                        <div class="bg-light-danger rounded-circle p-3 d-inline-flex mb-3">
                                                            <i class="fas fa-heart text-danger fa-xl"></i>
                                                        </div>
                                                        <h6 class="fw-bold text-dark mb-2">Ghế Đôi</h6>
                                                        <input type="number" name="Couple" id="Couple"
                                                            class="form-control form-control-lg text-center border-2 mb-2"
                                                            value="{{ old('Couple', 0) }}" min="0" required>
                                                        <label class="form-label small text-muted mb-1">Giá (VND)</label>
                                                        <input type="number" name="CouplePrice" id="CouplePrice"
                                                            class="form-control form-control-lg text-center border-2"
                                                            value="{{ old('CouplePrice', 120000) }}" min="0" step="1000" required>
                                                        <small class="text-muted mt-2 d-block">Couple Seats</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="fas fa-chart-bar me-2 text-primary"></i>Thống Kê
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <small class="text-muted">Sức chứa phòng:</small>
                                                <div class="fw-bold fs-5 text-primary" id="roomCapacity">0 ghế</div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted">Tổng ghế đã thêm:</small>
                                                <div class="fw-bold fs-5" id="totalSeats">0 ghế</div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted">Ghế còn trống:</small>
                                                <div class="fw-bold fs-5" id="remainingSeats">0 ghế</div>
                                            </div>
                                            <div class="progress mb-3" style="height: 8px;">
                                                <div id="capacityProgress" class="progress-bar bg-success"
                                                    role="progressbar" style="width: 0%"></div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted">Tổng giá trị:</small>
                                                <div class="fw-bold fs-5 text-success" id="totalValue">0 VND</div>
                                            </div>
                                            <div class="alert alert-info border-0 small">
                                                <i class="fas fa-lightbulb me-1"></i>
                                                <strong>Lưu ý:</strong> Mỗi ghế đôi được tính là 2 chỗ ngồi
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-3 justify-content-end pt-3 border-top">
                                        <a href="{{ route('manager.seats.index') }}"
                                            class="btn btn-outline-secondary px-5 py-2 fw-semibold">
                                            <i class="fas fa-times me-2"></i>Hủy bỏ
                                        </a>
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold gradient-btn">
                                            <i class="fas fa-save me-2"></i>Thêm Ghế
                                        </button>
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
        .bg-light-primary {
            background-color: rgba(67, 97, 238, 0.1);
        }

        .bg-light-warning {
            background-color: rgba(252, 163, 17, 0.1);
        }

        .bg-light-danger {
            background-color: rgba(230, 57, 70, 0.1);
        }

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

        .form-control:focus,
        .form-select:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.3rem rgba(67, 97, 238, 0.25);
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }

        .form-control-lg {
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
        }

        .sticky-top {
            position: sticky;
            z-index: 10;
        }

        .progress-bar {
            transition: width 0.5s ease;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.needs-validation');
            const roomSelect = document.getElementById('RoomID');
            const seatInputs = document.querySelectorAll('input[type="number"]');
            const roomCapacityEl = document.getElementById('roomCapacity');
            const totalSeatsEl = document.getElementById('totalSeats');
            const remainingSeatsEl = document.getElementById('remainingSeats');
            const totalValueEl = document.getElementById('totalValue');
            const capacityProgress = document.getElementById('capacityProgress');

            // Định dạng số tiền
            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            // Cập nhật thống kê
            function updateStatistics() {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];
                const capacity = selectedOption.value ?
                    parseInt(selectedOption.text.match(/Sức chứa: (\d+)/)[1]) : 0;

                let totalSeats = 0;
                let totalValue = 0;

                // Tính tổng số ghế và tổng giá trị
                const standardCount = parseInt(document.getElementById('Standard').value) || 0;
                const standardPrice = parseInt(document.getElementById('StandardPrice').value) || 0;
                totalSeats += standardCount;
                totalValue += standardCount * standardPrice;

                const vipCount = parseInt(document.getElementById('VIP').value) || 0;
                const vipPrice = parseInt(document.getElementById('VIPPrice').value) || 0;
                totalSeats += vipCount;
                totalValue += vipCount * vipPrice;

                const coupleCount = parseInt(document.getElementById('Couple').value) || 0;
                const couplePrice = parseInt(document.getElementById('CouplePrice').value) || 0;
                totalSeats += coupleCount; // Ghế đôi tính 2 chỗ
                totalValue += coupleCount * couplePrice;

                const remaining = capacity - totalSeats;
                const percentage = capacity > 0 ? Math.min(100, (totalSeats / capacity) * 100) : 0;

                // Cập nhật UI
                roomCapacityEl.textContent = capacity.toLocaleString() + ' ghế';
                totalSeatsEl.textContent = totalSeats.toLocaleString() + ' ghế';
                remainingSeatsEl.textContent = remaining.toLocaleString() + ' ghế';
                totalValueEl.textContent = formatCurrency(totalValue);

                // Cập nhật progress bar
                capacityProgress.style.width = percentage + '%';

                // Đổi màu progress bar theo mức độ
                if (percentage >= 100) {
                    capacityProgress.classList.remove('bg-warning', 'bg-success');
                    capacityProgress.classList.add('bg-danger');
                } else if (percentage >= 80) {
                    capacityProgress.classList.remove('bg-success', 'bg-danger');
                    capacityProgress.classList.add('bg-warning');
                } else {
                    capacityProgress.classList.remove('bg-warning', 'bg-danger');
                    capacityProgress.classList.add('bg-success');
                }

                // Hiển thị cảnh báo nếu vượt quá capacity
                if (totalSeats > capacity) {
                    totalSeatsEl.classList.add('text-danger');
                    remainingSeatsEl.classList.add('text-danger');
                } else {
                    totalSeatsEl.classList.remove('text-danger');
                    remainingSeatsEl.classList.remove('text-danger');
                }
            }

            // Lắng nghe sự kiện thay đổi
            roomSelect.addEventListener('change', updateStatistics);
            seatInputs.forEach(input => {
                input.addEventListener('input', updateStatistics);
            });

            // Validation form
            form.addEventListener('submit', function (event) {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];
                const capacity = selectedOption.value ?
                    parseInt(selectedOption.text.match(/Sức chứa: (\d+)/)[1]) : 0;

                let totalSeats = 0;
                const standardCount = parseInt(document.getElementById('Standard').value) || 0;
                const vipCount = parseInt(document.getElementById('VIP').value) || 0;
                const coupleCount = parseInt(document.getElementById('Couple').value) || 0;
                
                totalSeats = standardCount + vipCount + coupleCount;

                // Kiểm tra giá hợp lệ
                const standardPrice = parseInt(document.getElementById('StandardPrice').value) || 0;
                const vipPrice = parseInt(document.getElementById('VIPPrice').value) || 0;
                const couplePrice = parseInt(document.getElementById('CouplePrice').value) || 0;

                // if (standardPrice < 1000 || vipPrice < 1000 || couplePrice < 1000) {
                //     event.preventDefault();
                //     event.stopPropagation();
                //     alert('Giá ghế phải lớn hơn hoặc bằng 1,000 VND');
                //     return;
                // }

                if (totalSeats > capacity) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert(`Tổng số ghế (${totalSeats}) vượt quá sức chứa của phòng (${capacity}). Vui lòng điều chỉnh lại.`);
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);

            // Khởi tạo thống kê ban đầu
            updateStatistics();
        });
    </script>
@endsection