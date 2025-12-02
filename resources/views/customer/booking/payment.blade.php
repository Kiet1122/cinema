@extends('customer.layouts.app')

@section('title', 'Thanh Toán VNPay - ' . $booking->showtime->movie->Title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('customer.home') }}" class="hover:text-blue-600">Trang chủ</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li><a href="{{ route('customer.booking.history') }}" class="hover:text-blue-600">Lịch sử đặt vé</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-900 font-medium">Thanh toán VNPay</li>
            </ol>
        </nav>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <div class="text-center mb-8">
                    <div class="mb-4">
                        <img src="{{ asset('vendor/download.png') }}" alt="VNPay" class="h-12 mx-auto">
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Thanh Toán Qua VNPay</h1>
                    <p class="text-gray-600">Thanh toán an toàn và nhanh chóng qua cổng VNPay</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Booking Details -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Thông Tin Đặt Vé</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <img src="{{ $booking->showtime->movie->PosterURL ? asset('storage/movies/' . $booking->showtime->movie->PosterURL) : 'https://via.placeholder.com/80x100/cccccc/ffffff?text=No+Image' }}" 
                                     alt="{{ $booking->showtime->movie->Title }}"
                                     class="w-16 h-20 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $booking->showtime->movie->Title }}</h3>
                                    <p class="text-sm text-gray-600">{{ $booking->showtime->room->RoomType }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('H:i d/m/Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $booking->showtime->room->theater->Name }}</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Ghế Đã Chọn</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($booking->bookingDetails as $detail)
                                    <div class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm">
                                        {{ $detail->seat->SeatNumber }} ({{ $detail->seat->SeatType }})
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            @if($booking->voucher)
                            <div class="bg-green-50 rounded-lg p-3">
                                <h4 class="font-semibold text-green-900 mb-1">Mã Giảm Giá Đã Áp Dụng</h4>
                                <p class="text-green-700 text-sm">{{ $booking->voucher->Code }}</p>
                                @if($booking->voucher->DiscountType === 'Percent')
                                    <p class="text-green-700 text-sm">Giảm {{ $booking->voucher->Value }}%</p>
                                @else
                                    <p class="text-green-700 text-sm">Giảm {{ number_format($booking->voucher->Value) }}₫</p>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Thông Tin Thanh Toán</h2>
                        
                        <!-- Price Summary -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-6">
                            <h3 class="font-semibold text-gray-900 mb-4">Chi Tiết Giá</h3>
                            <div class="space-y-3">
                                @php
                                    $basePrice = $booking->showtime->Price * $booking->bookingDetails->count();
                                    $seatFees = $booking->bookingDetails->sum('Price');
                                    $subtotal = $basePrice + $seatFees;
                                    
                                    // Tính discount nếu có voucher
                                    $discount = 0;
                                    if ($booking->voucher) {
                                        if ($booking->voucher->DiscountType === 'Percent') {
                                            $discount = $subtotal * ($booking->voucher->Value / 100);
                                        } else {
                                            $discount = $booking->voucher->Value;
                                        }
                                        // Đảm bảo discount không vượt quá subtotal
                                        $discount = min($discount, $subtotal);
                                    }
                                    
                                    $total = $subtotal - $discount;
                                @endphp

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Vé phim ({{ $booking->bookingDetails->count() }} vé):</span>
                                    <span>{{ number_format($basePrice) }}₫</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phí ghế đặc biệt:</span>
                                    <span>+ {{ number_format($seatFees) }}₫</span>
                                </div>
                                
                                <div class="flex justify-between border-b border-gray-200 pb-3">
                                    <span class="text-gray-600">Tạm tính:</span>
                                    <span class="font-medium">{{ number_format($subtotal) }}₫</span>
                                </div>

                                @if($booking->voucher)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Giảm giá:</span>
                                    <span class="text-green-600 font-medium">-{{ number_format($discount) }}₫</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                                    <span class="text-gray-900">Tổng thanh toán:</span>
                                    <span class="text-blue-600">{{ number_format($total) }}₫</span>
                                </div>
                            </div>
                        </div>

                        <!-- VNPay Payment Form -->
                        <form action="{{ route('customer.vnpay.create') }}" method="POST" id="vnpayForm">
                            @csrf
                            <input type="hidden" name="booking_id" value="{{ $booking->BookingID }}">
                            <input type="hidden" name="amount" value="{{ $total }}">
                            
                            <div class="mb-6">
                                <h4 class="font-semibold text-gray-900 mb-3">Phương Thức Thanh Toán</h4>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-white p-2 rounded-lg">
                                            <img src="{{ asset('vendor/download.png') }}" alt="VNPay QR" class="h-12">
                                        </div>
                                        <div>
                                            <h5 class="font-bold text-gray-900">VNPay</h5>
                                            <p class="text-sm text-gray-600">Thanh toán qua Internet Banking, QR Code, Thẻ Nội địa, Thẻ Quốc tế</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full bg-[#005baa] text-white py-3 rounded-lg font-semibold hover:bg-[#004c8a] transition-colors flex items-center justify-center">
                                <i class="fas fa-lock mr-2"></i>
                                Thanh Toán {{ number_format($total) }}₫ Với VNPay
                            </button>
                        </form>

                        <!-- VNPay Security Info -->
                        <div class="mt-6 bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Bảo Mật VNPay</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-shield-alt text-green-500 mt-1"></i>
                                    <span>Giao dịch được mã hóa SSL 256-bit</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-check-circle text-green-500 mt-1"></i>
                                    <span>Được bảo hộ bởi Ngân hàng Nhà nước Việt Nam</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <i class="fas fa-clock text-blue-500 mt-1"></i>
                                    <span>Xác nhận thanh toán ngay lập tức</span>
                                </li>
                            </ul>
                        </div>

                        <p class="text-xs text-gray-500 text-center mt-6">
                            Bằng cách tiếp tục, bạn đồng ý với 
                            <a href="#" class="text-blue-600 hover:underline">Điều khoản dịch vụ</a> 
                            của VNPay và
                            <a href="#" class="text-blue-600 hover:underline">Chính sách bảo mật</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const vnpayForm = document.getElementById('vnpayForm');
    
    vnpayForm.addEventListener('submit', function(e) {
        // Show loading state
        const submitButton = vnpayForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang kết nối VNPay...';
        
        // Optional: Add slight delay to show loading state
        setTimeout(() => {
            vnpayForm.submit();
        }, 100);
    });
});
</script>
@endsection