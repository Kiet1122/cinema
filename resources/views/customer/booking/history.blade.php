@extends('customer.layouts.app')

@section('title', 'Lịch Sử Đặt Vé')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Lịch Sử Đặt Vé</h1>
                    <p class="text-gray-600 mt-2">Quản lý và theo dõi tất cả các đơn đặt vé của bạn</p>
                </div>
                <a href="{{ route('customer.home') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold flex items-center">
                    <i class="fas fa-ticket-alt mr-2"></i>Đặt Vé Mới
                </a>
            </div>

            @if($bookings->count() > 0)
                <!-- Tab Navigation -->
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                    <div class="flex space-x-1 bg-gray-100 p-1 rounded-xl">
                        <button id="tab-all" class="tab-button flex-1 py-3 px-4 rounded-lg font-semibold transition-all duration-200 active" data-status="all">
                            <i class="fas fa-list mr-2"></i>Tất Cả ({{ $bookings->count() }})
                        </button>
                        <button id="tab-confirmed" class="tab-button flex-1 py-3 px-4 rounded-lg font-semibold transition-all duration-200" data-status="confirmed">
                            <i class="fas fa-check-circle mr-2"></i>Đã Xác Nhận ({{ $bookings->where('Status', 'Confirmed')->count() }})
                        </button>
                        <button id="tab-created" class="tab-button flex-1 py-3 px-4 rounded-lg font-semibold transition-all duration-200" data-status="created">
                            <i class="fas fa-clock mr-2"></i>Chờ Thanh Toán ({{ $bookings->where('Status', 'Created')->count() }})
                        </button>
                        <button id="tab-cancelled" class="tab-button flex-1 py-3 px-4 rounded-lg font-semibold transition-all duration-200" data-status="cancelled">
                            <i class="fas fa-times-circle mr-2"></i>Đã Hủy ({{ $bookings->where('Status', 'Cancelled')->count() }})
                        </button>
                    </div>
                </div>

                <!-- Booking Cards Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Column 1: Chờ Thanh Toán -->
                    <div class="space-y-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4">
                            <div class="flex items-center mb-3">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <h3 class="font-bold text-yellow-800">Chờ Thanh Toán</h3>
                                <span class="ml-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $bookings->where('Status', 'Created')->count() }}
                                </span>
                            </div>
                            <p class="text-yellow-700 text-sm">Các vé đang chờ bạn thanh toán</p>
                        </div>
                        
                        <div class="space-y-4 created-bookings">
                            @foreach($bookings->where('Status', 'Created') as $booking)
                                @include('customer.booking.partials.booking-card', ['booking' => $booking])
                            @endforeach
                            
                            @if($bookings->where('Status', 'Created')->count() == 0)
                                <div class="bg-white rounded-xl p-6 text-center border-2 border-dashed border-gray-200">
                                    <i class="fas fa-clock text-gray-300 text-3xl mb-3"></i>
                                    <p class="text-gray-500 text-sm">Không có vé nào chờ thanh toán</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Column 2: Đã Xác Nhận -->
                    <div class="space-y-4">
                        <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
                            <div class="flex items-center mb-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <h3 class="font-bold text-green-800">Đã Xác Nhận</h3>
                                <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $bookings->where('Status', 'Confirmed')->count() }}
                                </span>
                            </div>
                            <p class="text-green-700 text-sm">Các vé đã được xác nhận thành công</p>
                        </div>
                        
                        <div class="space-y-4 confirmed-bookings">
                            @foreach($bookings->where('Status', 'Confirmed') as $booking)
                                @include('customer.booking.partials.booking-card', ['booking' => $booking])
                            @endforeach
                            
                            @if($bookings->where('Status', 'Confirmed')->count() == 0)
                                <div class="bg-white rounded-xl p-6 text-center border-2 border-dashed border-gray-200">
                                    <i class="fas fa-check-circle text-gray-300 text-3xl mb-3"></i>
                                    <p class="text-gray-500 text-sm">Chưa có vé nào được xác nhận</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Column 3: Đã Hủy -->
                    <div class="space-y-4">
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                            <div class="flex items-center mb-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                <h3 class="font-bold text-red-800">Đã Hủy</h3>
                                <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $bookings->where('Status', 'Cancelled')->count() }}
                                </span>
                            </div>
                            <p class="text-red-700 text-sm">Các vé đã bị hủy</p>
                        </div>
                        
                        <div class="space-y-4 cancelled-bookings">
                            @foreach($bookings->where('Status', 'Cancelled') as $booking)
                                @include('customer.booking.partials.booking-card', ['booking' => $booking])
                            @endforeach
                            
                            @if($bookings->where('Status', 'Cancelled')->count() == 0)
                                <div class="bg-white rounded-xl p-6 text-center border-2 border-dashed border-gray-200">
                                    <i class="fas fa-times-circle text-gray-300 text-3xl mb-3"></i>
                                    <p class="text-gray-500 text-sm">Không có vé nào bị hủy</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mobile View (Single Column) -->
                <div class="lg:hidden space-y-6 mt-8">
                    @foreach($bookings as $booking)
                        @include('customer.booking.partials.booking-card', ['booking' => $booking])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $bookings->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-ticket-alt text-gray-400 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Chưa có đặt vé nào</h2>
                    <p class="text-gray-600 mb-8">Bạn chưa đặt vé nào. Hãy khám phá các phim đang chiếu và đặt vé ngay!</p>
                    <a href="{{ route('customer.movies') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-flex items-center">
                        <i class="fas fa-film mr-2"></i>
                        Khám Phá Phim
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Booking Card Partial (resources/views/customer/booking/partials/booking-card.blade.php) -->
@endsection

@push('styles')
<style>
.tab-button {
    background: transparent;
    border: none;
    color: #6b7280;
}

.tab-button.active {
    background: white;
    color: #3b82f6;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.booking-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.booking-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.booking-card.created {
    border-left-color: #f59e0b;
}

.booking-card.confirmed {
    border-left-color: #10b981;
}

.booking-card.cancelled {
    border-left-color: #ef4444;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const bookingCards = document.querySelectorAll('.booking-card');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const status = this.dataset.status;
            
            // Show/hide booking cards based on status
            bookingCards.forEach(card => {
                if (status === 'all') {
                    card.style.display = 'block';
                } else {
                    const cardStatus = card.classList.contains(status) ? status : '';
                    card.style.display = cardStatus === status ? 'block' : 'none';
                }
            });
        });
    });
});
</script>
@endpush