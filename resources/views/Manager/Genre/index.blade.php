@extends('Manager.layouts.app')

@section('title', 'Quản lý Thể loại Phim')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">🎬 Danh sách Thể loại</h1>
        <a href="{{ route('manager.genre.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm Thể loại mới
        </a>
    </div>

    {{-- Hiển thị thông báo (từ session, sau khi Store/Update/Destroy) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    {{-- Bảng hiển thị danh sách Thể loại --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tên Thể loại</th>
                            <th>Mô tả</th>
                            <th width="15%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($genres as $genre)
                        <tr>
                            <td><strong>{{ $genre->GenreName }}</strong></td>
                            <td>{{ Str::limit($genre->Description, 80) }}</td> {{-- Giới hạn độ dài mô tả --}}
                            <td>
                                {{-- Nút Sửa --}}
                                <a href="{{ route('manager.genre.edit', $genre->GenreID) }}" 
                                   class="btn btn-sm btn-info" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Nút Xóa (Dùng Form để gửi DELETE request) --}}
                                <form action="{{ route('manager.genre.destroy', $genre->GenreID) }}" 
                                      method="POST" 
                                      style="display:inline;"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa thể loại {{ $genre->GenreName }}? Thao tác này sẽ xóa các liên kết với phim.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Chưa có thể loại nào được thêm.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Có thể thêm mã JS để khởi tạo DataTable tại đây --}}
{{-- <script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script> --}}
@endpush