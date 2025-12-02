@extends('Manager.layouts.app')

@section('content')
    <div class="container mt-4">
        <!-- Header với thiết kế mới -->
        <div class="cinema-header mb-4">
            <div class="d-flex align-items-center">
                <div class="header-icon rounded-circle border border-primary">
                    <i class="fas fa-door-open fa-1x"></i>
                </div>
                <div>
                    <h1 class="h4 mb-0">Thêm Phòng Chiếu Mới</h1>
                    <p class="mb-0 opacity-75 small">Quản lý rạp chiếu phim</p>
                </div>
            </div>
        </div>

        <div class="card shadow-lg rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('manager.rooms.store') }}" method="POST">
                    @csrf

                    {{-- Chọn rạp chiếu --}}
                    <div class="mb-3">
                        <label for="TheaterID" class="form-label fw-semibold required-field">Rạp chiếu</label>
                        <select class="form-select @error('TheaterID') is-invalid @enderror" name="TheaterID" id="TheaterID"
                            required>
                            <option value="">-- Chọn rạp chiếu --</option>
                            @foreach($theaters as $theater)
                                <option value="{{ $theater->TheaterID }}" {{ old('TheaterID') == $theater->TheaterID ? 'selected' : '' }}>
                                    {{ $theater->Name }} ({{ $theater->City }})
                                </option>
                            @endforeach
                        </select>
                        @error('TheaterID')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Chọn rạp chiếu mà phòng sẽ thuộc về</div>
                    </div>

                    {{-- Tên phòng --}}
                    <div class="mb-3">
                        <label for="RoomName" class="form-label fw-semibold required-field">Tên phòng</label>
                        <input type="text" class="form-control @error('RoomName') is-invalid @enderror" id="RoomName"
                            name="RoomName" value="{{ old('RoomName') }}" placeholder="Nhập tên phòng" required>
                        @error('RoomName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Ví dụ: Phòng 1, Phòng IMAX, Phòng VIP</div>
                    </div>

                    {{-- Sức chứa --}}
                    <div class="mb-3">
                        <label for="Capacity" class="form-label fw-semibold required-field">Sức chứa</label>
                        <input type="number" class="form-control @error('Capacity') is-invalid @enderror" id="Capacity"
                            name="Capacity" value="{{ old('Capacity') }}" min="1" max="300" placeholder="Nhập sức chứa"
                            required>
                        @error('Capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Số lượng ghế tối đa trong phòng</div>
                    </div>

                    {{-- Loại phòng --}}
                    <div class="mb-3">
                        <label for="RoomType" class="form-label fw-semibold required-field">Loại phòng</label>
                        <select class="form-select @error('RoomType') is-invalid @enderror" name="RoomType" id="RoomType"
                            required>
                            <option value="">-- Chọn loại phòng --</option>
                            <option value="2D" {{ old('RoomType') == '2D' ? 'selected' : '' }}>2D - Phòng chiếu thường
                            </option>
                            <option value="3D" {{ old('RoomType') == '3D' ? 'selected' : '' }}>3D - Phòng chiếu 3D</option>
                            <option value="IMAX" {{ old('RoomType') == 'IMAX' ? 'selected' : '' }}>IMAX - Công nghệ IMAX
                            </option>
                        </select>
                        @error('RoomType')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Chọn công nghệ chiếu phim của phòng</div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('manager.rooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Lưu phòng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* :root {
                        --primary-color: #4361ee;
                        --secondary-color: #3f37c9;
                        --accent-color: #4895ef;
                        --light-bg: #f8f9fa;
                        --dark-bg: #212529;
                        --success-color: #4cc9f0;
                        --border-radius: 10px;
                        --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
                        --transition: all 0.3s ease;
                    } */

        body {
            font-size: 0.9rem;
        }

        .cinema-header {
            color: #7112ecff;
            border-radius: var(--border-radius);
            padding: 16px;
            margin-bottom: 25px;
            box-shadow: var(--box-shadow);
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }

        .btn-primary {
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 600;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 600;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }

        .header-icon {
            background-color: rgba(255, 255, 255, 0.2);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .required-field::after {
            content: "*";
            color: #e53e3e;
            margin-left: 3px;
        }

        .form-text {
            color: #6c757d;
            font-size: 0.8rem;
            margin-top: 0.4rem;
        }

        .invalid-feedback {
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .cinema-header {
                text-align: center;
                padding: 14px;
            }

            .header-icon {
                margin: 0 auto 12px;
                width: 40px;
                height: 40px;
            }

            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }

            .d-flex.justify-content-between .btn {
                width: 100%;
            }
        }
    </style>
@endsection