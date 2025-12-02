@extends('Manager.layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Chỉnh sửa Phòng Chiếu</h2>
        <a href="{{ route('manager.rooms.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0"><i class="fas fa-pencil-alt me-2"></i>Thông tin phòng chiếu</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('manager.rooms.update', $room->RoomID) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Chọn rạp chiếu --}}
                    <div class="col-md-6 mb-3">
                        <label for="TheaterID" class="form-label fw-semibold">
                            <i class="fas fa-building me-1 text-primary"></i>Rạp chiếu <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('TheaterID') is-invalid @enderror" 
                                name="TheaterID" id="TheaterID" required>
                            <option value="">-- Chọn rạp chiếu --</option>
                            @foreach($theaters as $theater)
                                <option value="{{ $theater->TheaterID }}" 
                                    {{ old('TheaterID', $room->TheaterID) == $theater->TheaterID ? 'selected' : '' }}>
                                    {{ $theater->Name }} ({{ $theater->City }})
                                </option>
                            @endforeach
                        </select>
                        @error('TheaterID')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tên phòng --}}
                    <div class="col-md-6 mb-3">
                        <label for="RoomName" class="form-label fw-semibold">
                            <i class="fas fa-signature me-1 text-primary"></i>Tên phòng <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('RoomName') is-invalid @enderror" 
                               id="RoomName" name="RoomName" 
                               value="{{ old('RoomName', $room->RoomName) }}" required>
                        @error('RoomName')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    {{-- Sức chứa --}}
                    <div class="col-md-6 mb-3">
                        <label for="Capacity" class="form-label fw-semibold">
                            <i class="fas fa-users me-1 text-primary"></i>Sức chứa <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control @error('Capacity') is-invalid @enderror" 
                               id="Capacity" name="Capacity" 
                               value="{{ old('Capacity', $room->Capacity) }}" min="1" required>
                        @error('Capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Loại phòng --}}
                    <div class="col-md-6 mb-3">
                        <label for="RoomType" class="form-label fw-semibold">
                            <i class="fas fa-film me-1 text-primary"></i>Loại phòng <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('RoomType') is-invalid @enderror" 
                                name="RoomType" id="RoomType" required>
                            <option value="">-- Chọn loại phòng --</option>
                            <option value="2D" {{ old('RoomType', $room->RoomType) == '2D' ? 'selected' : '' }}>2D</option>
                            <option value="3D" {{ old('RoomType', $room->RoomType) == '3D' ? 'selected' : '' }}>3D</option>
                            <option value="IMAX" {{ old('RoomType', $room->RoomType) == 'IMAX' ? 'selected' : '' }}>IMAX</option>
                        </select>
                        @error('RoomType')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Nút hành động --}}
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="{{ route('manager.rooms.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
        border: none;
    }
    .card-header {
        border-radius: 8px 8px 0 0;
    }
    .btn {
        border-radius: 6px;
    }
    .form-label i {
        width: 20px;
    }
</style>
@endsection