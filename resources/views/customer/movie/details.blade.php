@extends('customer.layouts.app')

@section('title', $movie->Title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <div class="relative h-96 bg-black">
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-black/40"></div>
        
        @if($movie->PosterURL && file_exists(public_path('storage/movies/' . $movie->PosterURL)))
            <img src="{{ asset('storage/movies/' . $movie->PosterURL) }}" alt="{{ $movie->Title }}" 
                 class="w-full h-full object-cover">
        @elseif($movie->PosterURL && Str::startsWith($movie->PosterURL, ['http://', 'https://']))
            <img src="{{ $movie->PosterURL }}" alt="{{ $movie->Title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                <div class="text-center text-white">
                    <i class="fas fa-film text-6xl mb-4 opacity-50"></i>
                    <p class="text-xl font-semibold">Không có poster</p>
                </div>
            </div>
        @endif
        
        <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
            <div class="container mx-auto">
                <h1 class="text-5xl font-bold mb-4">{{ $movie->Title }}</h1>
                <div class="flex items-center space-x-6 text-lg">
                    <div class="flex items-center space-x-2">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating ?? 0))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= ($averageRating ?? 0))
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <span>{{ number_format($averageRating ?? 0, 1) }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ $movie->Duration }} phút</span>
                    <span>•</span>
                    <span>{{ $movie->AgeRestriction ? $movie->AgeRestriction . '+' : 'PG' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 -mt-20 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Movie Info Card -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold mb-6">Thông tin phim</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-gray-500 text-sm">Ngày khởi chiếu</label>
                                <p class="font-medium">{{ \Carbon\Carbon::parse($movie->ReleaseDate)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-sm">Ngôn ngữ</label>
                                <p class="font-medium">{{ $movie->Language ?? 'Tiếng Việt' }}</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-sm">Thể loại</label>
                                <p class="font-medium">
                                    @if($movie->genres->isNotEmpty())
                                        {{ $movie->genres->pluck('GenreName')->join(', ') }}
                                    @else
                                        Đang cập nhật
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-gray-500 text-sm">Đánh giá trung bình</label>
                                <p class="font-medium">{{ number_format($averageRating ?? 0, 1) }}/5</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-sm">Tổng đánh giá</label>
                                <p class="font-medium">{{ $movie->reviews->count() }} lượt</p>
                            </div>
                            <div>
                                <label class="text-gray-500 text-sm">Trạng thái</label>
                                <p class="font-medium">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $movie->IsActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $movie->IsActive ? 'Đang chiếu' : 'Ngừng chiếu' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-gray-500 text-sm">Nội dung phim</label>
                        <p class="text-gray-700 leading-relaxed mt-2">{{ $movie->Description ?? 'Đang cập nhật...' }}</p>
                    </div>

                    @if($movie->TrailerURL)
                    <div class="mt-6">
                        <label class="text-gray-500 text-sm mb-3 block">Trailer</label>
                        <div class="aspect-w-16 aspect-h-9">
                            <iframe src="{{ $movie->TrailerURL }}" class="w-full h-64 rounded-lg" frameborder="0"
                                    allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Showtimes Section - Redesigned with City Selection -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Lịch chiếu</h2>
                            <p class="text-gray-500 text-sm mt-1">Chọn thành phố, rạp và ngày để xem suất chiếu</p>
                        </div>
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="text-gray-600">Còn chỗ</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-orange-400 rounded-full"></div>
                                <span class="text-gray-600">Sắp chiếu</span>
                            </div>
                        </div>
                    </div>

                    @if(count($showtimesByDate) > 0)
                        <!-- City Selection -->
                        @php
                            $cities = [];
                            foreach($showtimesByDate as $dateData) {
                                foreach($dateData['showtimes'] as $theaterName => $showtimes) {
                                    $theater = $showtimes->first()->room->theater ?? null;
                                    if($theater && $theater->City) {
                                        $cities[$theater->City] = $theater->City;
                                    }
                                }
                            }
                            $cities = array_unique($cities);
                        @endphp

                        @if(count($cities) > 0)
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Chọn thành phố</h3>
                            <div>
                                <select id="cityDropdown" class="w-full md:w-1/3 border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all duration-200">
                                    <option value="">-- Chọn thành phố --</option>
                                    <option value="all">Tất cả thành phố</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <!-- Date Selection -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Chọn ngày xem</h3>
                            <div class="flex space-x-3 overflow-x-auto pb-4 custom-scrollbar">
                                @foreach($showtimesByDate as $dateString => $data)
                                    @php
                                        $date = \Carbon\Carbon::parse($dateString);
                                        $isToday = $date->isToday();
                                        $isTomorrow = $date->isTomorrow();
                                    @endphp
                                    <button class="date-tab flex-shrink-0 px-6 py-4 rounded-xl border-2 transition-all duration-200 min-w-28
                                        {{ $loop->first ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-sm' : 'border-gray-200 text-gray-600 hover:border-blue-300' }}"
                                            data-date="{{ $dateString }}">
                                        <div class="text-center">
                                            @if($isToday)
                                                <div class="text-xs font-semibold text-blue-600 mb-1">HÔM NAY</div>
                                            @elseif($isTomorrow)
                                                <div class="text-xs font-semibold text-green-600 mb-1">NGÀY MAI</div>
                                            @else
                                                <div class="text-xs font-semibold text-gray-500 mb-1">{{ $date->format('D') }}</div>
                                            @endif
                                            <div class="text-lg font-bold">{{ $date->format('d') }}</div>
                                            <div class="text-xs text-gray-500 mt-1">{{ $date->format('m/Y') }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Theaters & Showtimes -->
                        <div id="showtimesContent">
                            <!-- Initial State - No city selected -->
                            <div id="noCitySelected" class="text-center py-16">
                                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-map-marker-alt text-blue-500 text-3xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">Chọn thành phố để xem lịch chiếu</h3>
                                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                    Vui lòng chọn thành phố để xem các rạp chiếu và suất chiếu có sẵn.
                                </p>
                            </div>

                            @foreach($showtimesByDate as $dateString => $data)
                                <div class="date-content {{ $loop->first ? 'block' : 'hidden' }}" data-date="{{ $dateString }}">
                                    <div class="space-y-6">
                                        @if(count($data['showtimes']) > 0)
                                            <!-- Group theaters by city -->
                                            @php
                                                $theatersByCity = [];
                                                foreach($data['showtimes'] as $theaterName => $showtimes) {
                                                    $theater = $showtimes->first()->room->theater ?? null;
                                                    $city = $theater->City ?? 'Khác';
                                                    if(!isset($theatersByCity[$city])) {
                                                        $theatersByCity[$city] = [];
                                                    }
                                                    $theatersByCity[$city][$theaterName] = [
                                                        'showtimes' => $showtimes,
                                                        'theater' => $theater
                                                    ];
                                                }
                                            @endphp

                                            @foreach($theatersByCity as $city => $theaters)
                                                <div class="city-content hidden" data-city="{{ $city }}">
                                                    <!-- City Header -->
                                                    <div class="flex items-center mb-4 p-4 bg-gray-50 rounded-xl">
                                                        <i class="fas fa-city text-blue-500 text-xl mr-3"></i>
                                                        <div>
                                                            <h4 class="font-bold text-lg text-gray-900">{{ $city }}</h4>
                                                            <p class="text-gray-500 text-sm">{{ count($theaters) }} rạp đang chiếu</p>
                                                        </div>
                                                    </div>

                                                    <!-- Theaters in this city -->
                                                    <div class="space-y-4">
                                                        @foreach($theaters as $theaterName => $theaterData)
                                                            @php
                                                                $showtimes = $theaterData['showtimes'];
                                                                $theater = $theaterData['theater'];
                                                            @endphp
                                                            <div class="bg-gradient-to-r from-gray-50 to-white rounded-2xl p-6 border border-gray-200">
                                                                <!-- Theater Header -->
                                                                <div class="flex items-start justify-between mb-6">
                                                                    <div class="flex items-start space-x-4">
                                                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                                                            <i class="fas fa-film text-white text-lg"></i>
                                                                        </div>
                                                                        <div class="flex-1">
                                                                            <h3 class="font-bold text-xl text-gray-900">{{ $theaterName }}</h3>
                                                                            <p class="text-gray-500 text-sm mt-1 flex items-center">
                                                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                                                @if($theater)
                                                                                    {{ $theater->Address }}
                                                                                    @if($theater->Phone)
                                                                                        • <i class="fas fa-phone ml-2 mr-1"></i>{{ $theater->Phone }}
                                                                                    @endif
                                                                                @else
                                                                                    Đang cập nhật
                                                                                @endif
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <div class="text-sm text-gray-500">Có</div>
                                                                        <div class="text-lg font-bold text-green-600">{{ count($showtimes) }} suất</div>
                                                                    </div>
                                                                </div>

                                                                <!-- Room Types -->
                                                                <div class="mb-4">
                                                                    <div class="flex flex-wrap gap-2">
                                                                        @php
                                                                            $roomTypes = $showtimes->pluck('room.RoomType')->unique();
                                                                        @endphp
                                                                        @foreach($roomTypes as $type)
                                                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                                                                                {{ $type }}
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                <!-- Showtimes Grid -->
                                                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                                                                    @foreach($showtimes as $showtime)
                                                                        @php
                                                                            $startTime = \Carbon\Carbon::parse($showtime->StartTime);
                                                                            $endTime = \Carbon\Carbon::parse($showtime->EndTime);
                                                                            $isPast = $startTime->isPast();
                                                                            $isSoon = $startTime->diffInHours(now()) <= 2;
                                                                            $roomType = $showtime->room->RoomType ?? '2D';
                                                                            $price = number_format($showtime->Price, 0);
                                                                        @endphp
                                                                        
                                                                        @if(!$isPast)
                                                                            <a href="{{ route('customer.booking.create', ['showtime_id' => $showtime->ShowtimeID]) }}"
                                                                               class="group relative block transform transition-all duration-200 hover:scale-105">
                                                                                <div class="bg-white rounded-xl p-3 border-2 
                                                                                    {{ $isSoon ? 'border-orange-200 bg-orange-50' : 'border-green-200' }} 
                                                                                    shadow-sm hover:shadow-md transition-all duration-200">
                                                                                    <!-- Time -->
                                                                                    <div class="text-center mb-2">
                                                                                        <div class="text-lg font-bold text-gray-900 group-hover:text-blue-600">
                                                                                            {{ $startTime->format('H:i') }}
                                                                                        </div>
                                                                                        <div class="text-xs text-gray-500 group-hover:text-blue-500">
                                                                                            ~ {{ $endTime->format('H:i') }}
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Room Type Badge -->
                                                                                    <div class="text-center mb-2">
                                                                                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold
                                                                                            {{ $roomType === 'IMAX' ? 'bg-purple-100 text-purple-700' : 
                                                                                               ($roomType === '3D' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                                                                            {{ $roomType }}
                                                                                        </span>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Price -->
                                                                                    <div class="text-center">
                                                                                        <div class="text-sm font-bold text-green-600">{{ $price }}₫</div>
                                                                                        @if($isSoon)
                                                                                            <div class="text-xs text-orange-600 font-medium mt-1">
                                                                                                <i class="fas fa-clock mr-1"></i>Sắp chiếu
                                                                                            </div>
                                                                                        @else
                                                                                            <div class="text-xs text-gray-400 mt-1">Còn chỗ</div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        @else
                                                                            <div class="group relative block opacity-60 cursor-not-allowed">
                                                                                <div class="bg-gray-100 rounded-xl p-3 border-2 border-gray-300">
                                                                                    <!-- Time -->
                                                                                    <div class="text-center mb-2">
                                                                                        <div class="text-lg font-bold text-gray-500">
                                                                                            {{ $startTime->format('H:i') }}
                                                                                        </div>
                                                                                        <div class="text-xs text-gray-400">
                                                                                            ~ {{ $endTime->format('H:i') }}
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Room Type Badge -->
                                                                                    <div class="text-center mb-2">
                                                                                        <span class="inline-block px-2 py-1 bg-gray-200 text-gray-500 rounded-full text-xs">
                                                                                            {{ $roomType }}
                                                                                        </span>
                                                                                    </div>
                                                                                    
                                                                                    <!-- Status -->
                                                                                    <div class="text-center">
                                                                                        <div class="text-sm text-gray-500 line-through">{{ $price }}₫</div>
                                                                                        <div class="text-xs text-gray-400 mt-1">
                                                                                            <i class="fas fa-ban mr-1"></i>Đã qua
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center py-12 bg-gray-50 rounded-2xl">
                                                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">Không có suất chiếu</h3>
                                                <p class="text-gray-500">Không có suất chiếu nào trong ngày này</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16 bg-gradient-to-br from-gray-50 to-white rounded-2xl border-2 border-dashed border-gray-300">
                            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-film text-blue-500 text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Chưa có lịch chiếu</h3>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                Hiện tại chưa có suất chiếu nào cho phim này trong 7 ngày tới. 
                                Vui lòng quay lại sau để cập nhật lịch chiếu mới nhất.
                            </p>
                            <div class="flex justify-center space-x-4">
                                <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    <i class="fas fa-sync-alt mr-2"></i>Thử lại
                                </button>
                                <a href="{{ route('customer.movies') }}" 
                                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                    <i class="fas fa-arrow-left mr-2"></i>Xem phim khác
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Reviews Section -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold">Đánh giá từ khán giả</h2>
                        @if(Auth::check())
                            <button id="openReviewModal" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Viết đánh giá
                            </button>
                        @endif
                    </div>

                    @if($movie->reviews->isNotEmpty())
                        <div class="space-y-6">
                            @foreach($movie->reviews as $review)
                                <div class="border-b border-gray-100 pb-6 last:border-0">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <p class="font-semibold text-gray-900">
                                                {{ $review->customer->FullName ?? 'Khách hàng' }}
                                            </p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <div class="flex text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->Rating)
                                                            <i class="fas fa-star text-sm"></i>
                                                        @else
                                                            <i class="far fa-star text-sm"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-gray-500 text-sm">{{ $review->Rating }}/5</span>
                                                @if($review->IsEdited)
                                                    <span class="text-gray-400 text-xs">(Đã chỉnh sửa)</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-gray-400 text-sm">
                                            {{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700">{{ $review->Comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="far fa-star text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Chưa có đánh giá nào cho phim này</p>
                            @if(!Auth::check())
                                <p class="text-sm text-gray-400 mt-2">
                                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700">Đăng nhập</a> để viết đánh giá đầu tiên
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Action Card -->
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                    @if(Auth::check())
                        <a href="#showtimesContent" 
                           class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 rounded-lg font-semibold text-center block transition-all duration-200 mb-4 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            Đặt vé ngay
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 rounded-lg font-semibold text-center block transition-all duration-200 mb-4 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Đăng nhập để đặt vé
                        </a>
                    @endif
                    
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Thời lượng:</span>
                            <span class="font-medium text-gray-900">{{ $movie->Duration }} phút</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Ngôn ngữ:</span>
                            <span class="font-medium text-gray-900">{{ $movie->Language ?? 'Tiếng Việt' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Giới hạn tuổi:</span>
                            <span class="font-medium text-gray-900">{{ $movie->AgeRestriction ? $movie->AgeRestriction . '+' : 'Mọi lứa tuổi' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Khởi chiếu:</span>
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($movie->ReleaseDate)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Rating Summary -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-semibold mb-4 text-gray-900">Đánh giá trung bình</h3>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($averageRating ?? 0, 1) }}</div>
                        <div class="flex justify-center text-yellow-400 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($averageRating ?? 0))
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= ($averageRating ?? 0))
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-gray-500 text-sm">{{ $movie->reviews->count() }} lượt đánh giá</p>
                    </div>
                </div>

                <!-- Poster Preview -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-semibold mb-4 text-gray-900">Poster</h3>
                    <div class="text-center">
                        @if($movie->PosterURL && file_exists(public_path('storage/movies/' . $movie->PosterURL)))
                            <img src="{{ asset('storage/movies/' . $movie->PosterURL) }}" 
                                 alt="{{ $movie->Title }}"
                                 class="w-48 h-64 object-cover rounded-lg mx-auto shadow-md">
                        @elseif($movie->PosterURL && Str::startsWith($movie->PosterURL, ['http://', 'https://']))
                            <img src="{{ $movie->PosterURL }}" 
                                 alt="{{ $movie->Title }}"
                                 class="w-48 h-64 object-cover rounded-lg mx-auto shadow-md">
                        @else
                            <div class="w-48 h-64 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg mx-auto flex items-center justify-center">
                                <div class="text-white text-center">
                                    <i class="fas fa-film text-3xl mb-2 opacity-50"></i>
                                    <p class="text-sm">No poster</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Viết đánh giá</h3>
                <button id="closeReviewModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('customer.movie.review.store', ['id' => $movie->MovieID]) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Đánh giá của bạn</label>
                    <div class="flex space-x-1 text-2xl cursor-pointer" id="ratingStars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="far fa-star text-yellow-400 hover:text-yellow-500 transition-colors" data-value="{{ $i }}"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" value="0" required>
                    <div id="ratingError" class="text-red-500 text-sm mt-2 hidden">Vui lòng chọn số sao!</div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Nhận xét</label>
                    <textarea name="comment" rows="4" 
                              class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                              placeholder="Chia sẻ cảm nhận của bạn về bộ phim..."
                              required></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" id="cancelReview" 
                            class="flex-1 border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Hủy
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Gửi đánh giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Review Modal Elements
    const modal = document.getElementById('reviewModal');
    const modalContent = modal.querySelector('.bg-white');
    const openBtn = document.getElementById('openReviewModal');
    const closeBtns = [
        document.getElementById('closeReviewModal'),
        document.getElementById('cancelReview')
    ];

    // Star Rating Elements
    const stars = document.querySelectorAll('#ratingStars i');
    const ratingInput = document.getElementById('ratingInput');
    const ratingError = document.getElementById('ratingError');

    // Date Tabs for Showtimes
    const dateTabs = document.querySelectorAll('.date-tab');
    const dateContents = document.querySelectorAll('.date-content');
    
    // City Dropdown
    const cityDropdown = document.getElementById('cityDropdown');
    const cityContents = document.querySelectorAll('.city-content');
    const noCitySelected = document.getElementById('noCitySelected');

    // Initial state - hide all city contents and show "no city selected" message
    cityContents.forEach(content => {
        content.classList.add('hidden');
    });
    noCitySelected.classList.remove('hidden');

    // City Dropdown Functionality
    cityDropdown.addEventListener('change', function() {
        const selectedCity = this.value;
        
        // Hide "no city selected" message
        noCitySelected.classList.add('hidden');
        
        // Show corresponding city content
        cityContents.forEach(content => {
            content.classList.add('hidden');
            if (selectedCity === 'all' || content.getAttribute('data-city') === selectedCity) {
                content.classList.remove('hidden');
            }
        });

        // If no city selected, show the message again
        if (!selectedCity) {
            noCitySelected.classList.remove('hidden');
            cityContents.forEach(content => {
                content.classList.add('hidden');
            });
        }
    });

    // Date Tab Functionality
    dateTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const date = this.getAttribute('data-date');
            
            // Update active date tab
            dateTabs.forEach(t => {
                t.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700', 'shadow-sm');
                t.classList.add('border-gray-200', 'text-gray-600');
            });
            this.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700', 'shadow-sm');
            this.classList.remove('border-gray-200', 'text-gray-600');
            
            // Show corresponding date content
            dateContents.forEach(content => {
                content.classList.add('hidden');
                if (content.getAttribute('data-date') === date) {
                    content.classList.remove('hidden');
                }
            });
        });
    });

    // Modal Functions
    function openModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            resetRating();
        }, 300);
    }

    // Star Rating Functions
    function resetRating() {
        stars.forEach(star => {
            star.classList.remove('fas', 'text-yellow-500');
            star.classList.add('far');
        });
        ratingInput.value = '0';
        ratingError.classList.add('hidden');
    }

    function setRating(value) {
        stars.forEach((star, index) => {
            if (index < value) {
                star.classList.remove('far');
                star.classList.add('fas', 'text-yellow-500');
            } else {
                star.classList.remove('fas', 'text-yellow-500');
                star.classList.add('far');
            }
        });
        ratingInput.value = value;
        ratingError.classList.add('hidden');
    }

    // Event Listeners
    openBtn?.addEventListener('click', openModal);
    
    closeBtns.forEach(btn => {
        btn?.addEventListener('click', closeModal);
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Star Rating Interaction
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const value = parseInt(this.getAttribute('data-value'));
            setRating(value);
        });

        star.addEventListener('mouseover', function() {
            const value = parseInt(this.getAttribute('data-value'));
            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.add('text-yellow-400');
                } else {
                    s.classList.remove('text-yellow-400');
                }
            });
        });

        star.addEventListener('mouseout', function() {
            const currentRating = parseInt(ratingInput.value);
            stars.forEach((s, index) => {
                if (index >= currentRating) {
                    s.classList.remove('text-yellow-400');
                }
            });
        });
    });

    // Form Validation
    const form = modal.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (ratingInput.value === '0') {
            e.preventDefault();
            ratingError.classList.remove('hidden');
            modalContent.classList.add('animate-shake');
            setTimeout(() => {
                modalContent.classList.remove('animate-shake');
            }, 500);
        }
    });
});
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    height: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.animate-shake {
    animation: shake 0.3s ease-in-out;
}

#ratingStars i {
    transition: all 0.2s ease;
}

.showtime-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.showtime-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endsection