@extends('Manager.layouts.app')

@section('title', 'Thêm Thể loại mới')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">🎬 Tạo Thể loại Phim mới</h1>
        <a href="{{ route('manager.genre.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            
            {{-- Form bắt đầu. POST đến route manager.genres.store --}}
            <form action="{{ route('manager.genre.store') }}" method="POST">
                @csrf

                {{-- 1. Tên Thể loại --}}
                <div class="form-group">
                    <label for="GenreName">Tên Thể loại (<span class="text-danger">*</span>)</label>
                    <input type="text" 
                           class="form-control @error('GenreName') is-invalid @enderror" 
                           id="GenreName" 
                           name="GenreName" 
                           value="{{ old('GenreName') }}" 
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
                              rows="4">{{ old('Description') }}</textarea>
                    @error('Description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Nút Submit --}}
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Lưu Thể loại
                </button>
            </form>
            
        </div>
    </div>
</div>
@endsection