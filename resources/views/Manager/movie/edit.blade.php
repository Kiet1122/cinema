@extends('Manager.layouts.app')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="h3 mb-1 text-dark">
                <span class="badge bg-primary-subtle text-primary p-2 me-2 rounded-3">
                    <i class="bi bi-pencil-square fs-5"></i>
                </span>
                Chỉnh sửa Phim
            </h2>
            <p class="text-secondary mb-0">Cập nhật thông tin chi tiết của phim cho hệ thống.</p>
        </div>
        <a href="{{ route('manager.movies.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại Danh sách
        </a>
    </div>

    {{-- Hiển thị lỗi validate --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-start border-5 border-danger shadow-sm mb-4" role="alert">
            <h5 class="alert-heading fs-6"><i class="bi bi-exclamation-octagon-fill me-2"></i><strong>Lỗi Xác Thực Dữ Liệu!</strong></h5>
            <p class="mb-0 small">Vui lòng kiểm tra và sửa các lỗi sau trước khi cập nhật:</p>
            
            <ul class="mt-2 mb-0 small list-unstyled">
                @foreach ($errors->all() as $error)
                    <li class="d-flex align-items-start">
                        <i class="bi bi-x-circle-fill text-danger me-2 mt-1"></i>
                        <span>{{ $error }}</span>
                    </li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('manager.movies.update', $movie->MovieID) }}" method="POST" class="row g-3" enctype="multipart/form-data" id="movieEditForm">
                @csrf
                @method('PUT')

                <!-- 1. Tiêu đề -->
                <div class="col-12">
                    <label for="Title" class="form-label fw-semibold text-dark">Tên phim <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light-subtle border-end-0"><i class="bi bi-film text-primary"></i></span>
                        <input type="text" id="Title" name="Title"
                               class="form-control border-start-0" placeholder="Nhập tên phim"
                               value="{{ old('Title', $movie->Title) }}" required>
                    </div>
                </div>

                <!-- 2. Thể loại -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Thể loại <span class="text-danger">*</span></label>
                    <div class="border rounded-3 p-3 bg-light-subtle genre-box" style="max-height: 200px; overflow-y: auto;">
                        @php
                            $currentGenres = $movie->genres->pluck('GenreName')->toArray();
                            $oldGenres = old('genres', $currentGenres);
                        @endphp
                        
                        <div class="row row-cols-2 g-2">
                            @foreach ($genres as $genre)
                                <div class="col">
                                    <div class="form-check form-check-inline m-0 p-0 w-100">
                                        <input class="form-check-input visually-hidden" 
                                               type="checkbox" 
                                               value="{{ $genre->GenreName }}" 
                                               id="genre_{{ $genre->GenreID }}"
                                               name="genres[]" 
                                               {{ in_array($genre->GenreName, $oldGenres) ? 'checked' : '' }}>
                                        <label class="form-check-label genre-label d-block rounded-2 p-2 text-center small" for="genre_{{ $genre->GenreID }}">
                                            {{ $genre->GenreName }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-text mt-1 text-secondary">
                        Chọn ít nhất một thể loại.
                    </div>
                </div>

                <!-- 3. Thông tin cơ bản -->
                <div class="col-md-6">
                    <div class="row g-3">
                        {{-- Thời lượng --}}
                        <div class="col-md-6">
                            <label for="Duration" class="form-label fw-semibold text-dark">Thời lượng (phút) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle"><i class="bi bi-clock text-primary"></i></span>
                                <input type="number" id="Duration" name="Duration"
                                       class="form-control" min="1" step="1"
                                       value="{{ old('Duration', $movie->Duration) }}" required>
                            </div>
                        </div>

                        {{-- Ngày phát hành --}}
                        <div class="col-md-6">
                            <label for="ReleaseDate" class="form-label fw-semibold text-dark">Ngày phát hành</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle"><i class="bi bi-calendar-date text-primary"></i></span>
                                <input type="date" id="ReleaseDate" name="ReleaseDate"
                                       class="form-control"
                                       value="{{ old('ReleaseDate', optional($movie->ReleaseDate)->format('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- Ngôn ngữ --}}
                        <div class="col-md-6">
                            <label for="Language" class="form-label fw-semibold text-dark">Ngôn ngữ</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle"><i class="bi bi-translate text-primary"></i></span>
                                <input type="text" id="Language" name="Language"
                                       class="form-control" placeholder="Ví dụ: Tiếng Việt"
                                       value="{{ old('Language', $movie->Language) }}">
                            </div>
                        </div>

                        {{-- Giới hạn tuổi --}}
                        <div class="col-md-6">
                            <label for="AgeRestriction" class="form-label fw-semibold text-dark">Giới hạn tuổi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle"><i class="bi bi-person-lock text-primary"></i></span>
                                <select id="AgeRestriction" name="AgeRestriction" class="form-select">
                                    <option value="">Chọn giới hạn độ tuổi</option>
                                    <option value="0" {{ old('AgeRestriction', $movie->AgeRestriction) == '0' ? 'selected' : '' }}>P - Mọi lứa tuổi (0+)</option>
                                    <option value="13" {{ old('AgeRestriction', $movie->AgeRestriction) == '13' ? 'selected' : '' }}>13+</option>
                                    <option value="16" {{ old('AgeRestriction', $movie->AgeRestriction) == '16' ? 'selected' : '' }}>16+</option>
                                    <option value="18" {{ old('AgeRestriction', $movie->AgeRestriction) == '18' ? 'selected' : '' }}>18+</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Rating --}}
                        <div class="col-12">
                            <label for="Rating" class="form-label fw-semibold text-dark">Đánh giá (IMDb)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-subtle"><i class="bi bi-star-fill text-warning"></i></span>
                                <input type="number" id="Rating" name="Rating"
                                       class="form-control" placeholder="Ví dụ: 8.5" step="0.1" min="0" max="10"
                                       value="{{ old('Rating', $movie->Rating) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <hr class="my-3">
                </div>
                
                <!-- 4. Quản lý Poster -->
                <div class="col-12">
                    <h5 class="fw-semibold text-primary mb-3">
                        <i class="bi bi-image me-1"></i> Quản lý Poster
                    </h5>
                </div>
                
                <div class="col-md-6">
                    <label for="PosterFile" class="form-label fw-semibold text-dark">Tải lên Poster (File)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light-subtle"><i class="bi bi-file-earmark-image text-success"></i></span>
                        <input type="file" id="PosterFile" name="PosterFile" accept="image/*" class="form-control">
                    </div>
                    <div class="form-text">
                        Chọn file ảnh mới để thay thế. (Nếu chọn file, URL sẽ bị bỏ qua).
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="PosterURL" class="form-label fw-semibold text-dark">Poster (URL)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light-subtle"><i class="bi bi-link-45deg text-success"></i></span>
                        <input type="url" id="PosterURL" name="PosterURL"
                               class="form-control" placeholder="https://example.com/poster.jpg"
                               value="{{ old('PosterURL', $movie->PosterURL) }}">
                    </div>
                    <div class="form-text">
                        Hoặc dán đường link ảnh poster. (Nếu dán URL, File sẽ bị bỏ qua).
                    </div>
                </div>

                <!-- Poster Preview -->
                <div class="col-12">
                    <label class="form-label fw-semibold text-dark mb-2">Xem trước Poster</label>
                    <div id="posterPreviewContainer" class="p-3 border rounded-3 bg-light-subtle d-flex align-items-center justify-content-center" style="min-height: 120px;">
                        @if($movie->PosterURL)
                            <div id="currentPosterDisplay" class="d-flex align-items-center">
                                <p class="small text-muted me-3 mb-0">Poster hiện tại:</p>
                                <img src="{{ $movie->PosterURL }}" alt="Poster hiện tại" class="img-fluid rounded shadow-sm current-poster-img" style="max-height: 100px;">
                            </div>
                        @else
                            <p class="text-muted small mb-0 text-center">Poster sẽ được hiển thị ở đây khi bạn nhập URL hoặc chọn file.</p>
                        @endif
                    </div>
                </div>
                
                <div class="col-12">
                    <hr class="my-3">
                </div>

                <!-- 5. Thông tin bổ sung -->
                <div class="col-12">
                    <h5 class="fw-semibold text-primary mb-3">
                        <i class="bi bi-info-circle me-1"></i> Thông tin bổ sung
                    </h5>
                </div>

                <div class="col-md-6">
                    <label for="TrailerURL" class="form-label fw-semibold text-dark">Trailer (URL YouTube)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light-subtle"><i class="bi bi-youtube text-danger"></i></span>
                        <input type="url" id="TrailerURL" name="TrailerURL"
                               class="form-control" placeholder="https://youtube.com/embed/..."
                               value="{{ old('TrailerURL', $movie->TrailerURL) }}">
                    </div>
                    @if($movie->TrailerURL)
                        <div class="form-text mt-2">
                            <a href="{{ $movie->TrailerURL }}" target="_blank" class="text-decoration-none text-info small">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Xem trailer hiện tại
                            </a>
                        </div>
                    @endif
                </div>
                
                {{-- Trạng thái --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark mb-2">Trạng thái Phim</label>
                    <div class="form-check form-switch">
                        <input type="hidden" name="IsActive" value="0">
                        <input class="form-check-input" type="checkbox" id="IsActive" name="IsActive" value="1" 
                               {{ old('IsActive', $movie->IsActive) ? 'checked' : '' }} style="transform: scale(1.2);">
                        <label class="form-check-label fw-medium ms-2" for="IsActive">
                            <span class="{{ old('IsActive', $movie->IsActive) ? 'text-success' : 'text-danger' }}">
                                Phim đang {{ old('IsActive', $movie->IsActive) ? 'Hoạt động (Hiển thị)' : 'Tạm ẩn' }}
                            </span>
                        </label>
                    </div>
                    <div class="form-text">Bật/tắt trạng thái hiển thị phim trên trang web.</div>
                </div>

                <!-- 6. Mô tả -->
                <div class="col-12">
                    <label for="Description" class="form-label fw-semibold text-dark">Mô tả phim</label>
                    <textarea id="Description" name="Description" class="form-control" rows="5" 
                                 placeholder="Nhập mô tả nội dung phim chi tiết...">{{ old('Description', $movie->Description) }}</textarea>
                </div>

                <!-- 7. Nút bấm -->
                <div class="col-12 mt-4">
                    <hr class="mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('manager.movies.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-x-circle me-1"></i> Hủy bỏ
                        </a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-danger rounded-pill px-4">
                                <i class="bi bi-arrow-clockwise me-1"></i> Đặt lại
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm update-btn">
                                <i class="bi bi-check-circle me-1"></i> Cập nhật
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Tổng quan */
    .card {
        border-radius: 0.75rem;
    }
    
    /* Input Group */
    .input-group-text {
        background-color: #f8f9fa;
        color: #0d6efd;
        border: 1px solid #dee2e6;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    
    /* Buttons */
    .btn {
        border-radius: 50rem;
        font-weight: 500;
    }

    .update-btn {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .update-btn:hover {
        background-color: #0b5ed7;
        border-color: #0b5ed7;
    }
    
    /* Genre Checkbox */
    .genre-label {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        color: #495057;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .form-check-input:checked + .genre-label {
        background-color: #0d6efd;
        color: #ffffff;
        border-color: #0d6efd;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
    }

    /* Poster Preview */
    .current-poster-img {
        border: 2px solid #0d6efd;
        max-width: 150px;
        height: auto;
    }

    /* Trạng thái Switch */
    .form-switch .form-check-input {
        background-color: #adb5bd;
        border-color: #adb5bd;
    }
    
    .form-switch .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem !important;
        }
        
        .btn {
            padding: 0.5rem 1rem;
        }
        
        .genre-box {
            max-height: 150px !important;
        }
    }
</style>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('movieEditForm');
        const titleInput = document.getElementById('Title');
        const posterUrlInput = document.getElementById('PosterURL');
        const posterFileInput = document.getElementById('PosterFile');
        const previewContainer = document.getElementById('posterPreviewContainer');
        const isActiveCheckbox = document.getElementById('IsActive');
        const isActiveLabel = document.querySelector('label[for="IsActive"] span');

        // Initial focus for better UX
        titleInput.focus();

        // --- Logic Preview Poster ---
        const defaultPosterHtml = '<p class="text-muted small mb-0 text-center">Poster sẽ được hiển thị ở đây khi bạn nhập URL hoặc chọn file.</p>';

        // Hàm cập nhật preview
        const updatePreview = (url, isFile = false) => {
            previewContainer.innerHTML = '';
            
            if (url) {
                previewContainer.classList.add('d-flex', 'align-items-center', 'justify-content-center');
                previewContainer.innerHTML = `
                    <div class="text-center">
                        <p class="small text-muted mb-2">Preview Poster ${isFile ? '(File đã chọn)' : '(URL)'}</p>
                        <img src="${url}" alt="Poster preview" class="img-fluid rounded shadow-sm current-poster-img" 
                              onerror="this.onerror=null; this.src='https://placehold.co/150x100/CCCCCC/333333?text=Ảnh+Lỗi';">
                    </div>
                `;
            } else {
                previewContainer.classList.remove('d-flex', 'align-items-center', 'justify-content-center');
                previewContainer.innerHTML = defaultPosterHtml;
            }
        };
        
        // Khởi tạo trạng thái disabled ban đầu
        const initialPosterUrl = posterUrlInput.value.trim();
        if (initialPosterUrl) {
            posterFileInput.disabled = true;
        } else if (!posterFileInput.files.length) {
            updatePreview(null);
        }

        // 1. Xử lý khi nhập URL
        posterUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                posterFileInput.disabled = true;
                updatePreview(url);
            } else {
                posterFileInput.disabled = false;
                if (!posterFileInput.files.length) {
                    updatePreview(null);
                }
            }
        });

        // 2. Xử lý khi chọn File
        posterFileInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    posterUrlInput.disabled = true;
                    updatePreview(e.target.result, true);
                }
                reader.readAsDataURL(e.target.files[0]);
            } else {
                posterUrlInput.disabled = false;
                if (!posterUrlInput.value.trim()) {
                    updatePreview(null);
                } else {
                    updatePreview(posterUrlInput.value);
                }
            }
        });

        // 3. Xử lý trạng thái IsActive
        function updateIsActiveLabel() {
            if (isActiveCheckbox.checked) {
                isActiveLabel.textContent = 'Hoạt động (Hiển thị)';
                isActiveLabel.classList.remove('text-danger');
                isActiveLabel.classList.add('text-success');
            } else {
                isActiveLabel.textContent = 'Tạm ẩn';
                isActiveLabel.classList.remove('text-success');
                isActiveLabel.classList.add('text-danger');
            }
        }
        
        isActiveCheckbox.addEventListener('change', updateIsActiveLabel);
        updateIsActiveLabel();

        // 4. Đảm bảo không có trường nào bị disabled khi submit
        form.addEventListener('submit', function() {
            posterUrlInput.disabled = false;
            posterFileInput.disabled = false;
        });

        // 5. Xử lý Reset button
        form.addEventListener('reset', function() {
            setTimeout(() => {
                posterUrlInput.disabled = false;
                posterFileInput.disabled = false;

                const restoredUrl = posterUrlInput.defaultValue; 
                if (restoredUrl) {
                    posterFileInput.disabled = true;
                    updatePreview(restoredUrl);
                } else {
                    updatePreview(null);
                }
                
                updateIsActiveLabel();
            }, 10);
        });
    });
</script>
@endsection
@endsection