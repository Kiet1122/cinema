@extends('Manager.layouts.app') {{-- Sử dụng layout cha theo yêu cầu --}}

@section('title', 'Chỉnh sửa Thể loại: ' . $genre->GenreName)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">✍️ Chỉnh sửa Thể loại Phim</h1>
        <a href="{{ route('manager.genre.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ID: {{ $genre->GenreID }} | Tên cũ: {{ $genre->GenreName }}</h6>
        </div>
        <div class="card-body">
            
            {{-- Form bắt đầu. Dùng PUT/PATCH method và gửi đến route manager.genres.update --}}
            <form action="{{ route('manager.genre.update', $genre->GenreID) }}" method="POST">
                @csrf
                @method('PUT') {{-- Bắt buộc phải có để gửi yêu cầu PATCH/PUT --}}

                {{-- 1. Tên Thể loại --}}
                <div class="form-group">
                    <label for="GenreName">Tên Thể loại (<span class="text-danger">*</span>)</label>
                    <input type="text" 
                           class="form-control @error('GenreName') is-invalid @enderror" 
                           id="GenreName" 
                           name="GenreName" 
                           value="{{ old('GenreName', $genre->GenreName) }}" {{-- Hiển thị dữ liệu cũ hoặc dữ liệu từ DB --}}
                           required 
                           autofocus>
                    @error('GenreName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 2. Mô tả --}}
                <div class="form-group">
                    <label for="Description">Mô tả</label>
                    <textarea class="form-control @error('Description') is-invalid @enderror" 
                              id="Description" 
                              name="Description" 
                              rows="4">{{ old('Description', $genre->Description) }}</textarea> {{-- Hiển thị dữ liệu cũ hoặc dữ liệu từ DB --}}
                    @error('Description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Nút Submit --}}
                <button type="submit" class="btn btn-success mt-3">
                    <i class="fas fa-sync-alt"></i> Cập nhật Thể loại
                </button>
            </form>
            
        </div>
    </div>
</div>
@endsection