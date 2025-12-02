@extends('customer.layouts.app')
@section('title', 'Cinema Booking - Trang Chủ')

@section('content')

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @php
        $customer = $customer ?? null;
        $activeMovies = $activeMovies ?? collect(); // Phim đang chiếu
        $endedMovies = $endedMovies ?? collect();   // Phim ngừng chiếu
        $upcomingBookings = $upcomingBookings ?? collect();
        $hotMovies = $activeMovies->take(3); // Lấy 3 phim đang chiếu làm phim hot
    @endphp

    <!-- Hero Section -->
    <section class="mb-12">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-3xl p-8 lg:p-12 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-20 -right-20 w-40 h-40 bg-white rounded-full"></div>
                <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-white rounded-full"></div>
            </div>

            <div class="relative z-10 max-w-4xl">
                @if(Auth::check() && $customer)
                    <h1 class="text-4xl lg:text-6xl font-black mb-4 leading-tight">
                        Chào mừng,<br>
                        <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
                            {{ $customer->FullName }}!
                        </span>
                    </h1>
                    <p class="text-xl lg:text-2xl text-indigo-100 mb-8">
                        Sẵn sàng cho một trải nghiệm điện ảnh đáng nhớ?
                    </p>
                @else
                    <h1 class="text-4xl lg:text-6xl font-black mb-4 leading-tight">
                        Trải Nghiệm<br>
                        <span class="bg-gradient-to-r from-yellow-300 to-orange-300 bg-clip-text text-transparent">
                            Điện Ảnh Đỉnh Cao
                        </span>
                    </h1>
                    <p class="text-xl lg:text-2xl text-indigo-100 mb-8">
                        Khám phá những bộ phim hay nhất với công nghệ tiên tiến
                    </p>
                @endif

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#movie-categories"
                        class="bg-white text-indigo-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-bold text-lg transition duration-300 transform hover:scale-105 shadow-2xl">
                        <i class="fas fa-play-circle mr-2"></i>Xem Phim Đang Chiếu
                    </a>
                    @if(!Auth::check())
                        <a href=""
                            class="border-2 border-white text-white hover:bg-white hover:text-indigo-600 px-8 py-4 rounded-xl font-bold text-lg transition duration-300 transform hover:scale-105">
                            <i class="fas fa-user-plus mr-2"></i>Đăng Ký Ngay
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($hotMovies->count() > 0)
    <!-- Hot Movies Carousel -->
    <section class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-fire text-red-500 mr-3"></i>
                    Phim Hot Trong Tuần
                </h2>
                <p class="text-gray-600">Những bộ phim được yêu thích nhất hiện nay</p>
            </div>
        </div>

        <div class="hot-movies-carousel relative overflow-hidden rounded-2xl">
            <div class="hot-slides-container flex transition-transform duration-500 ease-in-out">
                @foreach ($hotMovies as $index => $movie)
                <div class="hot-slide w-full flex-shrink-0 {{ $index === 0 ? 'block' : 'hidden' }}">
                    <div class="bg-gradient-to-r from-gray-900 to-black rounded-2xl overflow-hidden relative h-96 lg:h-[500px]">
                        <!-- Background Image -->
                        <img src="{{ $movie->PosterURL ?? 'https://placehold.co/1200x600/4f46e5/ffffff?text=' . urlencode($movie->Title) }}"
                            alt="{{ $movie->Title }}" class="w-full h-full object-cover opacity-60">

                        <!-- Overlay Content -->
                        <div class="absolute inset-0 flex items-center">
                            <div class="container mx-auto px-8">
                                <div class="max-w-2xl">
                                    <!-- Hot Badge -->
                                    <div class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-bold inline-flex items-center mb-4">
                                        <i class="fas fa-fire mr-2"></i> PHIM HOT TRONG TUẦN
                                    </div>

                                    <h3 class="text-3xl lg:text-5xl font-black text-white mb-4 leading-tight">
                                        {{ $movie->Title }}
                                    </h3>

                                    <div class="flex items-center space-x-6 text-white mb-6">
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-yellow-400 mr-2"></i>
                                            <span class="font-semibold">{{ $movie->Rating ?? '8.0' }}/10</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2"></i>
                                            <span class="font-semibold">{{ $movie->DurationMinutes ?? '120' }} phút</span>
                                        </div>
                                    </div>

                                    <p class="text-gray-200 text-lg mb-6 line-clamp-3">
                                        {{ $movie->Description ?? 'Một bộ phim đầy cảm xúc và kịch tính...' }}
                                    </p>

                                    <div class="flex space-x-4">
                                        @if(Auth::check())
                                            <a href="{{ route('customer.movie.details', ['id' => $movie->MovieID]) }}"
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                                                <i class="fas fa-ticket-alt mr-2"></i>Đặt Vé Ngay
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}"
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                                                <i class="fas fa-sign-in-alt mr-2"></i>Đăng Nhập Để Đặt Vé
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide Indicators -->
                        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-2">
                            @foreach ($hotMovies as $indicatorIndex => $indicatorMovie)
                                <button class="hot-indicator w-3 h-3 rounded-full transition duration-300 {{ $indicatorIndex === 0 ? 'bg-white' : 'bg-white/50' }}"
                                    data-slide="{{ $indicatorIndex }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Movie Categories Tabs -->
    <section id="movie-categories" class="mb-16">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200">
            <!-- Tabs Header -->
            <div class="border-b border-gray-200">
                <div class="flex space-x-8 px-6 overflow-x-auto">
                    <button class="category-tab py-4 font-semibold text-gray-900 border-b-2 border-indigo-600 text-indigo-600 transition duration-300 whitespace-nowrap"
                        data-category="now-playing">
                        <i class="fas fa-play-circle mr-2"></i>Phim Đang Chiếu
                    </button>
                    <button class="category-tab py-4 font-semibold text-gray-600 hover:text-gray-900 transition duration-300 whitespace-nowrap"
                        data-category="ended">
                        <i class="fas fa-flag-checkered mr-2"></i>Phim Ngừng Chiếu
                    </button>
                </div>
            </div>

            <!-- Tabs Content -->
            <div class="p-6">
                <!-- Now Playing Movies -->
                <div id="now-playing" class="category-content active">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-film text-indigo-600 mr-2"></i>
                            Phim Đang Chiếu
                        </h3>
                        <p class="text-gray-600">Các bộ phim đang được công chiếu tại rạp</p>
                    </div>

                    @if($activeMovies->isEmpty())
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl p-12 text-center border-2 border-dashed border-yellow-200">
                            <i class="fas fa-film text-yellow-500 text-5xl mb-4"></i>
                            <h3 class="text-2xl font-bold text-yellow-700 mb-2">Đang cập nhật phim mới</h3>
                            <p class="text-yellow-600">Chúng tôi đang chuẩn bị những bộ phim hay nhất cho bạn. Vui lòng quay lại sau!</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                            @foreach ($activeMovies as $movie)
                            <div class="group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition duration-300 border border-gray-100">
                                <div class="aspect-[2/3] bg-gray-200 relative overflow-hidden">
                                    <img src="{{ $movie->PosterURL ?? 'https://placehold.co/300x450/4f46e5/ffffff?text=' . urlencode($movie->Title) }}"
                                        alt="{{ $movie->Title }}"
                                        class="w-full h-full object-cover transition duration-500 group-hover:scale-110"
                                        onerror="this.onerror=null;this.src='https://placehold.co/300x450/4f46e5/ffffff?text=POSTER';">

                                    <div class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg">
                                        ĐANG CHIẾU
                                    </div>

                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-70 transition duration-500 flex items-center justify-center">
                                        <div class="opacity-0 group-hover:opacity-100 transition duration-500 transform translate-y-4 group-hover:translate-y-0 text-white text-center">
                                            @if(Auth::check())
                                                <a href="{{ route('customer.movie.details', ['id' => $movie->MovieID]) }}"
                                                    class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg font-semibold inline-block transition duration-300">
                                                    <i class="fas fa-ticket-alt mr-2"></i>Đặt Vé
                                                </a>
                                            @else
                                                <a href="{{ route('login') }}"
                                                    class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg font-semibold inline-block transition duration-300">
                                                    <i class="fas fa-sign-in-alt mr-2"></i>Đăng Nhập
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3">
                                    <h4 class="font-bold text-gray-900 truncate mb-1" title="{{ $movie->Title }}">
                                        {{ $movie->Title }}
                                    </h4>
                                    <div class="flex justify-between items-center text-xs text-gray-600">
                                        <span>{{ $movie->DurationMinutes ?? '120' }} phút</span>
                                        <span class="flex items-center">
                                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                                            {{ $movie->Rating ?? '8.0' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Ended Movies -->
                <div id="ended" class="category-content hidden">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-flag-checkered text-gray-600 mr-2"></i>
                            Phim Ngừng Chiếu
                        </h3>
                        <p class="text-gray-600">Các bộ phim đã kết thúc thời gian công chiếu</p>
                    </div>

                    @if($endedMovies->isEmpty())
                        <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-12 text-center border-2 border-dashed border-gray-200">
                            <i class="fas fa-flag-checkered text-gray-400 text-5xl mb-4"></i>
                            <h3 class="text-2xl font-bold text-gray-700 mb-2">Không có phim ngừng chiếu</h3>
                            <p class="text-gray-600">Hiện tại tất cả phim đều đang được công chiếu</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                            @foreach ($endedMovies as $movie)
                            <div class="group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition duration-300 border border-gray-100 opacity-80 hover:opacity-100">
                                <div class="aspect-[2/3] bg-gray-200 relative overflow-hidden">
                                    <img src="{{ $movie->PosterURL ?? 'https://placehold.co/300x450/4f46e5/ffffff?text=' . urlencode($movie->Title) }}"
                                        alt="{{ $movie->Title }}"
                                        class="w-full h-full object-cover transition duration-500 group-hover:scale-110"
                                        onerror="this.onerror=null;this.src='https://placehold.co/300x450/4f46e5/ffffff?text=POSTER';">

                                    <div class="absolute top-3 left-3 bg-gray-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg">
                                        NGỪNG CHIẾU
                                    </div>

                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                        <div class="text-white text-center p-4">
                                            <p class="text-sm mb-3">Phim đã kết thúc công chiếu</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3">
                                    <h4 class="font-bold text-gray-900 truncate mb-1" title="{{ $movie->Title }}">
                                        {{ $movie->Title }}
                                    </h4>
                                    <div class="flex justify-between items-center text-xs text-gray-600">
                                        <span>{{ $movie->DurationMinutes ?? '120' }} phút</span>
                                        <span class="flex items-center">
                                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                                            {{ $movie->Rating ?? '8.0' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if(Auth::check() && $customer && !$upcomingBookings->isEmpty())
    <!-- Upcoming Bookings Section -->
    <section class="mb-12">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-ticket-alt text-indigo-600 mr-3"></i>
                Vé Sắp Tới Của Bạn
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($upcomingBookings->take(2) as $booking)
            @php
                $showtime = $booking->showtime;
                $movie = $showtime->movie ?? (object) ['Title' => 'Phim không xác định', 'PosterURL' => null, 'MovieID' => null];
                $startTime = \Carbon\Carbon::parse($showtime->StartTime);
            @endphp

            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 shadow-lg border border-indigo-100 hover:shadow-xl transition duration-300">
                <div class="flex items-start space-x-4">
                    <div class="w-24 h-32 flex-shrink-0 rounded-xl overflow-hidden shadow-lg">
                        <img src="{{ $movie->PosterURL ?? 'https://placehold.co/96x128/4f46e5/ffffff?text=MOVIE' }}"
                            alt="{{ $movie->Title }}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex-grow">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $movie->Title }}</h3>

                        <div class="space-y-2">
                            <div class="flex items-center text-gray-700">
                                <i class="far fa-calendar text-indigo-500 w-5 mr-3"></i>
                                <span class="font-medium">{{ $startTime->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center text-gray-700">
                                <i class="far fa-clock text-indigo-500 w-5 mr-3"></i>
                                <span class="font-medium">{{ $startTime->format('H:i') }}</span>
                            </div>
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-video text-indigo-500 w-5 mr-3"></i>
                                <span>{{ $showtime->room->theater->Name ?? 'Rạp Cinema' }}</span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-ticket-alt mr-1"></i>
                                {{ $booking->bookingDetails->count() }} vé
                            </span>
                            <a href="{{ route('customer.booking.show', ['id' => $booking->BookingID]) }}"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                                Chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

</div>

<style>
    .category-content {
        display: none;
    }
    .category-content.active {
        display: block;
    }
    .category-tab.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
    }
    .hot-slide {
        transition: opacity 0.5s ease-in-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Category Tabs Functionality
        const tabs = document.querySelectorAll('.category-tab');
        const contents = document.querySelectorAll('.category-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                const category = this.getAttribute('data-category');

                // Update active tab
                tabs.forEach(t => {
                    t.classList.remove('active', 'text-indigo-600', 'border-indigo-600');
                    t.classList.add('text-gray-600');
                });
                this.classList.add('active', 'text-indigo-600', 'border-indigo-600');
                this.classList.remove('text-gray-600');

                // Update active content
                contents.forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });
                document.getElementById(category).classList.add('active');
                document.getElementById(category).classList.remove('hidden');
            });
        });

        // Hot Movies Carousel
        const hotSlides = document.querySelectorAll('.hot-slide');
        const hotIndicators = document.querySelectorAll('.hot-indicator');
        let currentSlide = 0;

        function showSlide(index) {
            hotSlides.forEach(slide => slide.classList.add('hidden'));
            hotSlides.forEach(slide => slide.classList.remove('block'));
            hotIndicators.forEach(indicator => indicator.classList.remove('bg-white'));
            hotIndicators.forEach(indicator => indicator.classList.add('bg-white/50'));

            hotSlides[index].classList.remove('hidden');
            hotSlides[index].classList.add('block');
            hotIndicators[index].classList.remove('bg-white/50');
            hotIndicators[index].classList.add('bg-white');

            currentSlide = index;
        }

        function nextSlide() {
            let nextIndex = (currentSlide + 1) % hotSlides.length;
            showSlide(nextIndex);
        }

        // Auto rotate slides every 5 seconds
        if (hotSlides.length > 1) {
            setInterval(nextSlide, 5000);
        }

        // Event listeners for indicators
        hotIndicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => showSlide(index));
        });
    });
</script>
@endsection