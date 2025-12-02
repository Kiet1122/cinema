@extends('customer.layouts.app')

@section('title', 'Tìm Rạp Chiếu Phim')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Search Container -->
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8 mb-8">
            <!-- Search Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-3">
                    Tìm Rạp Chiếu Phim
                </h1>
                <p class="text-gray-600 text-sm">
                    Chỉ hiển thị rạp khi bạn tìm kiếm
                </p>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('customer.contact') }}" class="space-y-6" id="searchForm">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-city mr-2"></i>Chọn thành phố
                    </label>
                    <select name="city" id="city" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 appearance-none">
                        <option value="">-- Chọn thành phố --</option>
                        @if(isset($cities))
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="space-y-3">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 transform hover:scale-[1.02] font-semibold">
                        <i class="fas fa-search mr-2"></i> Tìm Rạp Chiếu Phim
                    </button>
                    
                    @if(request()->has('city'))
                        <a href="{{ route('customer.contact') }}" 
                           class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 inline-block text-center font-semibold">
                            <i class="fas fa-times mr-2"></i> Xóa Tìm Kiếm
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Results Container (Only shown when searching) -->
        @if(request()->has('city'))
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8">
                <!-- Results Header -->
                <div class="text-center mb-8 pb-6 border-b">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        Rạp Chiếu Phim Tại {{ request('city') }}
                    </h2>
                    <p class="text-gray-600 text-sm">
                        Kết quả tìm kiếm của bạn
                    </p>
                </div>

                @if(isset($randomTheater))
                    <!-- Theater Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100 hover:shadow-lg transition duration-300">
                        <!-- Theater Header -->
                        <div class="flex items-start mb-6 pb-6 border-b border-blue-200">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-film text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $randomTheater->Name }}</h3>
                                <span class="inline-block bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $randomTheater->City }}
                                </span>
                            </div>
                        </div>

                        <!-- Theater Details -->
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Địa chỉ:</p>
                                    <p class="text-gray-600">{{ $randomTheater->Address }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-phone text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Số điện thoại:</p>
                                    <a href="tel:{{ $randomTheater->Phone }}" 
                                       class="text-blue-600 hover:text-blue-800 transition duration-200">
                                        {{ $randomTheater->Phone }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Note -->
                        <div class="mt-6 pt-6 border-t border-blue-200">
                            <p class="text-center text-gray-600 text-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                Đây là một trong những rạp chiếu phim tại {{ request('city') }}
                            </p>
                        </div>
                    </div>
                @else
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-search text-gray-500 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Không tìm thấy rạp chiếu phim</h3>
                        <p class="text-gray-600 mb-6">
                            Không có rạp chiếu phim nào tại {{ request('city') }}
                        </p>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 mb-2">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                Hãy thử tìm kiếm với thành phố khác
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const citySelect = document.getElementById('city');
    const searchForm = document.getElementById('searchForm');
    
    // Auto submit when city is selected
    if (citySelect) {
        citySelect.addEventListener('change', function() {
            if (this.value) {
                searchForm.submit();
            }
        });
    }
    
    // Smooth scroll to results if they exist
    @if(request()->has('city'))
        setTimeout(() => {
            const resultContainer = document.querySelector('.max-w-2xl.mx-auto.bg-white.rounded-xl.shadow-lg.p-8:nth-child(2)');
            if (resultContainer) {
                resultContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        }, 300);
    @endif
});
</script>
@endsection