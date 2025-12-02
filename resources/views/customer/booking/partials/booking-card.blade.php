<div class="booking-card bg-white rounded-xl shadow-sm p-6 {{ $booking->Status === 'Confirmed' ? 'confirmed' : ($booking->Status === 'Created' ? 'created' : 'cancelled') }}">
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-bold text-gray-900">{{ $booking->showtime->movie->Title }}</h3>
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold 
                    {{ $booking->Status === 'Confirmed' ? 'bg-green-100 text-green-800' : 
                       ($booking->Status === 'Created' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ $booking->Status === 'Confirmed' ? 'Đã xác nhận' : 
                       ($booking->Status === 'Created' ? 'Chờ thanh toán' : 'Đã hủy') }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Thông tin phim và rạp -->
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-film w-4 mr-2 text-blue-500"></i>
                        <span>{{ $booking->showtime->movie->Title }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-video w-4 mr-2 text-purple-500"></i>
                        <span>{{ $booking->showtime->room->RoomType }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-building w-4 mr-2 text-orange-500"></i>
                        <span>{{ $booking->showtime->room->theater->Name }}</span>
                    </div>
                </div>
                
                <!-- Thông tin thời gian và giá -->
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-calendar-alt w-4 mr-2 text-green-500"></i>
                        <span>{{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-clock w-4 mr-2 text-red-500"></i>
                        <span>{{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('H:i') }}</span>
                    </div>
                    <div class="flex items-center text-sm font-semibold text-blue-600">
                        <i class="fas fa-tag w-4 mr-2"></i>
                        <span>{{ number_format($booking->TotalAmount) }}₫</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between border-t border-gray-100 pt-4">
        <div class="flex-1">
            <p class="text-sm text-gray-600 mb-2">
                <i class="fas fa-ticket-alt mr-1 text-gray-400"></i>
                Mã đặt vé: <span class="font-semibold">#{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}</span>
            </p>
            <div class="flex items-center flex-wrap gap-2">
                <span class="text-sm text-gray-600">Ghế:</span>
                <div class="flex flex-wrap gap-1">
                    @foreach($booking->bookingDetails as $detail)
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                        {{ $detail->seat->SeatNumber }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('customer.booking.show', $booking->BookingID) }}" 
               class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm flex items-center">
                <i class="fas fa-eye mr-1"></i>Chi tiết
            </a>
            @if($booking->Status === 'Created')
            <form action="{{ route('customer.booking.cancel', $booking->BookingID) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200 transition-colors text-sm flex items-center"
                        onclick="return confirm('Bạn có chắc chắn muốn hủy đặt vé này?')">
                    <i class="fas fa-times mr-1"></i>Hủy
                </button>
            </form>
            @endif
        </div>
    </div>
</div>