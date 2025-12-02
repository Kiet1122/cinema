@extends('Manager.layouts.app')

@section('content')
    <div class="container mt-3">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h4 mb-1"><i class="bi bi-film text-primary me-2"></i>Danh sách Phim</h2>
                <p class="text-muted mb-0 small">Quản lý thông tin các bộ phim trong hệ thống</p>
            </div>
            <a href="{{ route('manager.movies.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Thêm phim
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter and Search Section -->
        <div class="card shadow-sm mb-3">
            <div class="card-body py-2">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent py-1"><i class="bi bi-search small"></i></span>
                            <input type="text" id="searchInput" class="form-control form-control-sm py-1" placeholder="Tìm kiếm theo tên phim...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm py-1" id="statusFilter">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active">Đang chiếu</option>
                            <option value="inactive">Ngưng chiếu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm py-1" id="genreFilter">
                            <option value="">Tất cả thể loại</option>
                            @php
                                // FIXED: Collect all unique genres using the relationship
                                $allGenres = $movies->flatMap(function ($movie) {
                                    return $movie->genres->pluck('GenreName');
                                })->unique()->sort()->filter()->toArray();
                            @endphp
                            @foreach($allGenres as $genre)
                                @if($genre)
                                    <option value="{{ $genre }}">{{ $genre }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm py-1" id="languageFilter">
                            <option value="">Tất cả ngôn ngữ</option>
                            @php
                                $languages = array_unique($movies->pluck('Language')->filter()->toArray());
                                sort($languages);
                            @endphp
                            @foreach($languages as $language)
                                @if($language)
                                    <option value="{{ $language }}">{{ $language }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Movies Table -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 70px;">Poster</th>
                                <th>Tiêu đề</th>
                                <th>Thể loại</th>
                                <th>Thời lượng</th>
                                <th>Ngày chiếu</th>
                                <th>Ngôn ngữ</th>
                                <th>Đánh giá</th>
                                <th>Tuổi</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-3" style="width: 90px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="moviesTableBody">
                            @forelse($movies as $movie)
                                @php
                                    // Tính rating trung bình từ bảng review
                                    $averageRating = \App\Models\Review::where('MovieID', $movie->MovieID)->avg('Rating');
                                    $reviewCount = \App\Models\Review::where('MovieID', $movie->MovieID)->count();
                                @endphp
                                <tr class="movie-row" 
                                    data-title="{{ strtolower($movie->Title) }}"
                                    data-genre="{{ $movie->genres->pluck('GenreName')->implode(',') }}"
                                    data-language="{{ $movie->Language ?? '' }}"
                                    data-status="{{ $movie->IsActive ? 'active' : 'inactive' }}">

                                    {{-- Poster --}}
                                    <td class="ps-3">
                                        @if($movie->PosterURL)
                                            <img src="{{ $movie->PosterURL }}" alt="Poster" width="50" height="70" class="rounded object-fit-cover">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 70px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Title --}}
                                    <td>
                                        <div class="fw-medium text-dark">{{ \Illuminate\Support\Str::limit($movie->Title, 25) }}</div>
                                        @if($movie->TrailerURL)
                                            <a href="{{ $movie->TrailerURL }}" target="_blank" class="text-primary text-decoration-none">
                                                <i class="bi bi-play-btn me-1"></i>Trailer
                                            </a>
                                        @endif
                                    </td>

                                    {{-- Genre --}}
                                    <td>
                                        @if($movie->genres && $movie->genres->count() > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($movie->genres as $genreItem)
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $genreItem->GenreName }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Thời lượng --}}
                                    <td>
                                        <span class="text-muted">{{ $movie->Duration }}p</span>
                                    </td>

                                    {{-- Ngày phát hành --}}
                                    <td>
                                        @if($movie->ReleaseDate)
                                            <span class="text-muted">{{ date('d/m/Y', strtotime($movie->ReleaseDate)) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Ngôn ngữ --}}
                                    <td>
                                        @if($movie->Language)
                                            <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-25">{{ $movie->Language }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Rating --}}
                                    <td>
                                        @if($reviewCount > 0)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-star-fill text-warning me-1"></i>
                                                <span class="fw-medium">{{ number_format($averageRating, 1) }}</span>
                                                <small class="text-muted ms-1">({{ $reviewCount }})</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Chưa có đánh giá</span>
                                        @endif
                                    </td>

                                    {{-- Giới hạn tuổi --}}
                                    <td>
                                        @if($movie->AgeRestriction)
                                            <span class="badge bg-dark bg-opacity-25 text-dark border border-dark border-opacity-25">{{ $movie->AgeRestriction }}+</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Trạng thái --}}
                                    <td>
                                        @if($movie->IsActive)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                                <i class="bi bi-play-circle me-1"></i>Đang chiếu
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                <i class="bi bi-pause-circle me-1"></i>Ngừng chiếu
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Thao tác --}}
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <!-- Nút toggle status -->
                                            <form action="{{ route('manager.movies.toggle', $movie->MovieID) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                @if($movie->IsActive)
                                                    <button type="submit" 
                                                            class="btn btn-outline-warning btn-xs rounded-circle p-1"
                                                            data-bs-toggle="tooltip" 
                                                            title="Ngừng chiếu"
                                                            onclick="return confirm('Bạn có chắc muốn ngừng chiếu phim này?')">
                                                        <i class="bi bi-pause" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" 
                                                            class="btn btn-outline-success btn-xs rounded-circle p-1"
                                                            data-bs-toggle="tooltip" 
                                                            title="Kích hoạt chiếu"
                                                            onclick="return confirm('Bạn có chắc muốn kích hoạt chiếu phim này?')">
                                                        <i class="bi bi-play" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- Nút edit -->
                                            <a href="{{ route('manager.movies.edit', $movie->MovieID) }}"
                                                class="btn btn-outline-primary btn-xs rounded-circle p-1"
                                                data-bs-toggle="tooltip" title="Sửa phim">
                                                <i class="bi bi-pencil" style="font-size: 0.7rem;"></i>
                                            </a>

                                            <!-- Nút delete -->
                                            <form action="{{ route('manager.movies.destroy', $movie->MovieID) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-xs rounded-circle p-1"
                                                        onclick="return confirm('Bạn có chắc muốn xóa phim này?')"
                                                        data-bs-toggle="tooltip" 
                                                        title="Xóa phim">
                                                    <i class="bi bi-trash" style="font-size: 0.7rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-film display-4 text-muted"></i>
                                            <p class="mt-3 text-muted">Chưa có phim nào trong hệ thống.</p>
                                            <a href="{{ route('manager.movies.create') }}" class="btn btn-primary btn-sm mt-2">
                                                <i class="bi bi-plus-circle me-1"></i> Thêm phim đầu tiên
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Results Counter -->
            <div class="card-footer bg-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Hiển thị <span id="visibleCount">{{ $movies->count() }}</span> của {{ $movies->count() }} phim
                    </div>
                    <button id="clearFilters" class="btn btn-outline-secondary btn-sm py-1" style="display: none;">
                        <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }

        .table td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
            font-size: 0.8rem;
        }

        .empty-state {
            padding: 1.5rem 1rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .badge {
            font-size: 0.7rem;
            padding: 0.3em 0.5em;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
        }

        .btn-xs {
            padding: 0.15rem 0.3rem;
            font-size: 0.7rem;
        }

        .form-control-sm, .form-select-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
        }

        .alert {
            font-size: 0.8rem;
        }

        /* Style cho các nút thao tác */
        .btn-xs.rounded-circle {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Get filter elements
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const genreFilter = document.getElementById('genreFilter');
            const languageFilter = document.getElementById('languageFilter');
            const movieRows = document.querySelectorAll('.movie-row');
            const visibleCountElement = document.getElementById('visibleCount');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const totalCount = movieRows.length;

            // Add event listeners for filtering
            searchInput.addEventListener('input', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            genreFilter.addEventListener('change', applyFilters);
            languageFilter.addEventListener('change', applyFilters);
            clearFiltersBtn.addEventListener('click', clearFilters);

            // Function to apply all filters
            function applyFilters() {
                const searchText = searchInput.value.toLowerCase();
                const selectedStatus = statusFilter.value;
                const selectedGenre = genreFilter.value;
                const selectedLanguage = languageFilter.value;

                let visibleCount = 0;

                movieRows.forEach(row => {
                    const title = row.getAttribute('data-title');
                    const genreString = row.getAttribute('data-genre'); // Comma-separated genre names
                    const language = row.getAttribute('data-language');
                    const status = row.getAttribute('data-status');

                    // Check if row matches all filters
                    const matchesSearch = title.includes(searchText) || searchText === '';
                    const matchesStatus = selectedStatus === '' || status === selectedStatus;
                    
                    // FIXED: Check if the movie's genre string includes the selected genre
                    const matchesGenre = selectedGenre === '' || genreString.includes(selectedGenre);
                    
                    const matchesLanguage = selectedLanguage === '' || language === selectedLanguage;

                    if (matchesSearch && matchesStatus && matchesGenre && matchesLanguage) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Update visible count
                if (visibleCountElement) {
                    visibleCountElement.textContent = visibleCount;
                }

                // Show/hide clear filters button
                clearFiltersBtn.style.display = (searchText || selectedStatus || selectedGenre || selectedLanguage) ? 'block' : 'none';

                // Show empty message if no results
                showEmptyMessage(visibleCount === 0 && totalCount > 0);
            }

            // Function to show/hide empty message
            function showEmptyMessage(show) {
                let emptyRow = document.querySelector('.empty-filter-state');
                
                if (show && !emptyRow) {
                    // Create empty filter message
                    const tbody = document.getElementById('moviesTableBody');
                    const newRow = document.createElement('tr');
                    newRow.className = 'empty-filter-row'; 
                    newRow.innerHTML = `
                        <td colspan="10" class="text-center py-4">
                            <div class="empty-filter-state">
                                <i class="bi bi-search display-4 text-muted"></i>
                                <p class="mt-3 text-muted small">Không tìm thấy phim nào phù hợp với bộ lọc.</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(newRow);
                } else if (!show) {
                    // Remove all rows with the empty filter class
                    document.querySelectorAll('.empty-filter-row').forEach(row => row.remove());
                }
            }

            // Clear all filters
            function clearFilters() {
                searchInput.value = '';
                statusFilter.value = '';
                genreFilter.value = '';
                languageFilter.value = '';
                applyFilters();
            }
            
            // Re-apply filters initially
            applyFilters();
        });
    </script>
@endsection