@extends('Manager.layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1"><i class="bi bi-plus-circle text-success me-2"></i>Thêm Phim Mới</h2>
            <p class="text-muted mb-0">Thêm thông tin phim mới vào hệ thống quản lý</p>
        </div>
        <a href="{{ route('manager.movies.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    {{-- Hiển thị lỗi validate --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Có lỗi xảy ra!</strong> Vui lòng kiểm tra lại thông tin.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            
            <ul class="mt-2 mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            {{-- CHÚ Ý: ĐÃ THÊM enctype="multipart/form-data" ĐỂ HỖ TRỢ UPLOAD FILE POSTER --}}
            <form action="{{ route('manager.movies.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                @csrf

                {{-- PHẦN 1: THÔNG TIN CƠ BẢN --}}
                <div class="col-12">
                    <h5 class="mb-3 text-primary border-bottom pb-2">1. Thông Tin Chung</h5>
                </div>

                {{-- Tên phim --}}
                <div class="col-md-8">
                    <label for="title" class="form-label fw-medium">Tên phim <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-film text-primary"></i></span>
                        <input type="text" id="title" name="title" 
                            class="form-control" placeholder="Nhập tên phim (Ví dụ: The Matrix)" 
                            value="{{ old('title') }}" required>
                    </div>
                </div>

                {{-- Thời lượng --}}
                <div class="col-md-4">
                    <label for="duration" class="form-label fw-medium">Thời lượng (phút) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-clock text-primary"></i></span>
                        <input type="number" id="duration" name="duration" 
                            class="form-control" placeholder="Thời lượng" 
                            value="{{ old('duration') }}" required min="1">
                    </div>
                </div>
                
                <!-- Đảm bảo rằng bạn đã lặp qua biến $genres được truyền từ Controller -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Chọn Thể loại</label>
                    <div class="mt-2 space-y-2 border p-3 rounded-lg bg-gray-50">
                        <!-- Vòng lặp này phải có trong view của bạn -->
                        @foreach ($genres as $genre)
                            <div class="flex items-center">
                                <!-- QUAN TRỌNG: name PHẢI là "genres[]" và value PHẢI là GenreName -->
                                <input id="genre_{{ $genre->GenreID }}" 
                                    name="genres[]" 
                                    type="checkbox" 
                                    value="{{ $genre->GenreName }}" 
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="genre_{{ $genre->GenreID }}" class="ml-3 text-sm text-gray-900">
                                    {{ $genre->GenreName }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>


                {{-- Ngôn ngữ --}}
                <div class="col-md-6">
                    <label for="language" class="form-label fw-medium">Ngôn ngữ</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-translate text-primary"></i></span>
                        <input type="text" id="language" name="language" 
                            class="form-control" placeholder="Ví dụ: Tiếng Việt, Tiếng Anh" 
                            value="{{ old('language') }}">
                    </div>
                </div>
                
                {{-- Mô tả --}}
                <div class="col-12">
                    <label for="description" class="form-label fw-medium">Mô tả phim</label>
                    <textarea id="description" name="description" class="form-control" rows="5" 
                                placeholder="Nhập mô tả nội dung phim chi tiết...">{{ old('description') }}</textarea>
                </div>
                
                {{-- PHẦN 2: THÔNG SỐ KỸ THUẬT & PHÂN LOẠI --}}
                <div class="col-12 mt-5">
                    <h5 class="mb-3 text-primary border-bottom pb-2">2. Phân Loại & Đánh Giá</h5>
                </div>

                {{-- Ngày phát hành --}}
                <div class="col-md-4">
                    <label for="release_date" class="form-label fw-medium">Ngày phát hành</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar-event text-primary"></i></span>
                        <input type="date" id="release_date" name="release_date" 
                            class="form-control" value="{{ old('release_date') }}">
                    </div>
                </div>

                {{-- Giới hạn độ tuổi --}}
                <div class="col-md-4">
                    <label for="age_restriction" class="form-label fw-medium">Giới hạn độ tuổi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person-badge text-primary"></i></span>
                        <select id="age_restriction" name="age_restriction" class="form-select">
                            <option value="">Chọn độ tuổi</option>
                            <option value="0" {{ old('age_restriction') == '0' ? 'selected' : '' }}>P - Mọi lứa tuổi</option>
                            <option value="13" {{ old('age_restriction') == '13' ? 'selected' : '' }}>13+</option>
                            <option value="16" {{ old('age_restriction') == '16' ? 'selected' : '' }}>16+</option>
                            <option value="18" {{ old('age_restriction') == '18' ? 'selected' : '' }}>18+</option>
                        </select>
                    </div>
                </div>

                {{-- Rating --}}
                <div class="col-md-4">
                    <label for="rating" class="form-label fw-medium">Đánh giá (IMDb)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-star-fill text-warning"></i></span>
                        <input type="number" id="rating" name="rating" 
                            class="form-control" placeholder="Ví dụ: 8.5" 
                            value="{{ old('rating') }}" step="0.1" min="0" max="10">
                    </div>
                    <div class="form-text">Giá trị từ 0.0 đến 10.0</div>
                </div>
                
                {{-- PHẦN 3: POSTER VÀ TRAILER --}}
                <div class="col-12 mt-5">
                    <h5 class="mb-3 text-primary border-bottom pb-2">3. Media</h5>
                </div>
                
                {{-- Poster (File Upload or URL) --}}
                <div class="col-md-6">
                    <label class="form-label fw-medium">Poster Phim</label>
                    
                    {{-- Tab Navigation for switching between File/URL --}}
                    <ul class="nav nav-pills mb-2" id="posterTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active btn-sm" id="upload-tab" data-bs-toggle="pill" data-bs-target="#upload" type="button" role="tab" aria-controls="upload" aria-selected="true" onclick="togglePosterInput('file')">Tải lên File</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm" id="url-tab" data-bs-toggle="pill" data-bs-target="#url" type="button" role="tab" aria-controls="url" aria-selected="false" onclick="togglePosterInput('url')">Sử dụng URL</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="posterTabContent">
                        {{-- File Upload Input --}}
                        <div class="tab-pane fade show active" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                            {{-- poster_file sẽ được gửi nếu tab này active --}}
                            <input type="file" id="poster_file" name="poster_file" class="form-control" accept="image/*">
                            <div class="form-text">Tải lên hình ảnh poster (JPEG, PNG).</div>
                            {{-- Dùng input hidden để Laravel có thể lấy old('poster_url') nếu lỗi xảy ra --}}
                            <input type="hidden" id="poster_url_hidden" name="poster_url" value="">
                        </div>
                        
                        {{-- URL Input (Hidden initially for file context) --}}
                        <div class="tab-pane fade" id="url" role="tabpanel" aria-labelledby="url-tab">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-link-45deg text-primary"></i></span>
                                {{-- poster_url sẽ được gửi nếu tab này active --}}
                                <input type="url" id="poster_url_input" 
                                    class="form-control" placeholder="https://example.com/poster.jpg" 
                                    value="{{ old('poster_url') }}" disabled>
                            </div>
                            <div class="form-text">Nhập URL hình ảnh poster phim.</div>
                        </div>
                    </div>
                </div>

                {{-- Trailer URL --}}
                <div class="col-md-6">
                    <label for="trailer_url" class="form-label fw-medium">Trailer (URL)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-youtube text-danger"></i></span>
                        <input type="url" id="trailer_url" name="trailer_url" 
                            class="form-control" placeholder="https://youtube.com/watch?v=..." 
                            value="{{ old('trailer_url') }}">
                    </div>
                    <div class="form-text">Nhập URL trailer YouTube hoặc Vimeo</div>
                </div>
                
                {{-- Poster Preview Area --}}
                <div class="col-12">
                    <div id="posterPreviewContainer" class="mt-2">
                        <div class="border rounded p-3 text-center bg-light-subtle">
                            <img src="" alt="Poster preview" id="poster_preview_img" class="img-fluid rounded shadow-sm" style="max-height: 250px; display: none;">
                            <p id="poster_preview_placeholder" class="text-muted small mb-0 mt-2">Preview poster sẽ hiển thị ở đây</p>
                        </div>
                    </div>
                </div>


                {{-- Trạng thái --}}
                <div class="col-12 mt-5">
                    <h5 class="mb-3 text-primary border-bottom pb-2">4. Trạng Thái</h5>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label fw-medium">Trạng thái hoạt động</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Phim đang hoạt động
                        </label>
                    </div>
                    <div class="form-text">Bật/tắt trạng thái hiển thị phim trên trang chủ.</div>
                </div>

                {{-- Nút bấm --}}
                <div class="col-12 mt-5 pt-3">
                    <hr class="mt-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('manager.movies.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i> Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-plus-circle me-1"></i> Thêm phim
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px;
    }
    
    .form-label {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 0.5rem;
    }
    
    .input-group-text {
        border-right: none;
        background-color: #f1f3f5; /* Light grey background for icons */
        color: #495057;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4c6ef5; /* A subtle blue focus color */
        box-shadow: 0 0 0 0.25rem rgba(76, 110, 245, 0.15);
    }
    
    .btn {
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }
    
    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    .form-text {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* Styling for the tab buttons */
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        background-color: #4c6ef5;
    }
</style>

<script>
    /**
     * Cập nhật hình ảnh preview poster
     * @param {string} src - Nguồn hình ảnh (URL hoặc Data URL)
     */
    function updatePosterPreview(src) {
        const img = document.getElementById('poster_preview_img');
        const placeholder = document.getElementById('poster_preview_placeholder');
        
        if (src) {
            img.src = src;
            img.style.display = 'block';
            placeholder.style.display = 'none';
        } else {
            img.src = '';
            img.style.display = 'none';
            placeholder.style.display = 'block';
        }
    }
    
    /**
     * Chuyển đổi giữa chế độ nhập File và nhập URL cho Poster
     * Bằng cách quản lý thuộc tính 'disabled' và 'name'
     * @param {string} type - 'file' hoặc 'url'
     */
    function togglePosterInput(type) {
        const fileInput = document.getElementById('poster_file');
        const urlInput = document.getElementById('poster_url_input');
        
        // Luôn clear cả hai trường khi chuyển đổi để tránh gửi dư thừa data
        fileInput.value = '';
        urlInput.value = '';

        if (type === 'file') {
            // Kích hoạt input file, Vô hiệu hóa input URL
            fileInput.disabled = false;
            urlInput.disabled = true;
            
            // Đảm bảo chỉ 'poster_file' được gửi
            fileInput.name = 'poster_file';
            urlInput.removeAttribute('name');
            
            updatePosterPreview(''); // Clear preview
        } else if (type === 'url') {
            // Vô hiệu hóa input file, Kích hoạt input URL
            fileInput.disabled = true;
            urlInput.disabled = false;
            
            // Đảm bảo chỉ 'poster_url' được gửi
            urlInput.name = 'poster_url';
            fileInput.removeAttribute('name');

            // Do Laravel blade sử dụng old('poster_url') để fill value sau khi lỗi validate, 
            // ta cần đọc giá trị đó và hiển thị preview (nếu có)
            const oldUrl = "{{ old('poster_url') }}";
            if (oldUrl) {
                urlInput.value = oldUrl;
                updatePosterPreview(oldUrl);
            } else {
                 updatePosterPreview(''); // Clear preview
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Focus vào trường đầu tiên
        document.getElementById('title').focus();
        
        const fileInput = document.getElementById('poster_file');
        const urlInput = document.getElementById('poster_url_input');
        const uploadTab = document.getElementById('upload-tab');
        const urlTab = document.getElementById('url-tab');

        // Khởi tạo trạng thái Poster Input
        // Nếu old('poster_url') có giá trị, chuyển sang chế độ URL, nếu không, dùng chế độ FILE
        const oldUrl = "{{ old('poster_url') }}";
        if (oldUrl) {
            // Cần click tab URL và set giá trị lại cho input URL (vì đã clear trong toggle)
            urlTab.click(); 
        } else {
            // Mặc định là chế độ File Upload
            uploadTab.click();
        }

        // --- Live Preview Listeners ---

        // 1. Live Preview cho URL
        urlInput.addEventListener('input', function() {
            if (!urlInput.disabled) {
                updatePosterPreview(this.value);
            }
        });

        // 2. Live Preview cho File Upload
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0] && !fileInput.disabled) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    updatePosterPreview(e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
            } else if (!fileInput.disabled) {
                updatePosterPreview('');
            }
        });
        
        // Gán sự kiện click cho các tab để đảm bảo việc chuyển đổi name/disabled hoạt động đúng
        uploadTab.addEventListener('click', () => togglePosterInput('file'));
        urlTab.addEventListener('click', () => togglePosterInput('url'));
    });
</script>
@endsection
