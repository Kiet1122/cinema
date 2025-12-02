@extends('customer.layouts.app')

@section('title', 'Chi Tiết Vé - #' . str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT))

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Breadcrumb -->
                <nav class="mb-6 no-print">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li><a href="{{ route('customer.home') }}" class="hover:text-blue-600">Trang chủ</a></li>
                        <li><i class="fas fa-chevron-right text-xs"></i></li>
                        <li><a href="{{ route('customer.booking.history') }}" class="hover:text-blue-600">Lịch sử đặt vé</a></li>
                        <li><i class="fas fa-chevron-right text-xs"></i></li>
                        <li class="text-gray-900 font-medium">Chi tiết vé</li>
                    </ol>
                </nav>

                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Chi Tiết Vé</h1>
                            <p class="text-gray-600">Mã đặt vé: #{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-sm text-gray-500 mt-1">Ngày đặt: {{ $booking->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold 
                                        {{ $booking->Status === 'Confirmed' ? 'bg-green-100 text-green-800 border border-green-200' :
                                            ($booking->Status === 'Created' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
                                            'bg-red-100 text-red-800 border border-red-200') }}">
                                {{ $booking->Status === 'Confirmed' ? '✅ Đã xác nhận' :
                                    ($booking->Status === 'Created' ? '⏳ Chờ thanh toán' : '❌ Đã hủy') }}
                            </span>
                            <p class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($booking->TotalAmount) }}₫</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Movie Information -->
                        <div class="lg:col-span-2">
                            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-film text-blue-500 mr-2"></i>Thông Tin Phim
                            </h2>
                            <div class="space-y-4">
                                <div class="flex items-start space-x-4 p-4 bg-blue-50 rounded-lg">
                                    <img src="{{ $booking->showtime->movie->PosterURL ? asset('storage/movies/' . $booking->showtime->movie->PosterURL) : 'https://via.placeholder.com/80x112/cccccc/ffffff?text=No+Image' }}"
                                        alt="{{ $booking->showtime->movie->Title }}"
                                        class="w-20 h-28 object-cover rounded-lg shadow">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $booking->showtime->movie->Title }}
                                        </h3>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                                                <i class="fas fa-clock mr-1"></i>{{ $booking->showtime->movie->Duration }} phút
                                            </span>
                                            @if($booking->showtime->movie->AgeRestriction)
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                                    <i class="fas fa-user-lock mr-1"></i>{{ $booking->showtime->movie->AgeRestriction }}+
                                                </span>
                                            @endif
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                                <i class="fas fa-closed-captioning mr-1"></i>{{ $booking->showtime->movie->Language ?? 'Tiếng Việt' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3 bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-building mr-2 text-blue-500"></i>
                                            <span>Rạp:</span>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $booking->showtime->room->theater->Name }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-door-open mr-2 text-green-500"></i>
                                            <span>Phòng chiếu:</span>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $booking->showtime->room->RoomName }}
                                            ({{ $booking->showtime->room->RoomType }})</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar mr-2 text-purple-500"></i>
                                            <span>Ngày chiếu:</span>
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-clock mr-2 text-orange-500"></i>
                                            <span>Giờ chiếu:</span>
                                        </div>
                                        <span class="font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($booking->showtime->EndTime)->format('H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="mt-6">
                                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-ticket-alt text-green-500 mr-2"></i>Thông Tin Đặt Vé
                                </h2>
                                <div class="space-y-4">
                                    <!-- Seats -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                            <i class="fas fa-chair mr-2 text-blue-500"></i>Ghế Đã Đặt
                                        </h3>
                                        <div class="grid grid-cols-2 gap-3">
                                            @foreach($booking->bookingDetails as $detail)
                                                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-3 text-center">
                                                    <div class="text-lg font-bold text-blue-800">{{ $detail->seat->SeatNumber }}</div>
                                                    <div class="text-xs text-blue-600 capitalize">{{ $detail->seat->SeatType }}</div>
                                                    <div class="text-sm font-semibold text-blue-900 mt-1">
                                                        {{ number_format($detail->Price) }}₫
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-3 text-sm text-gray-600 text-center">
                                            Tổng {{ $booking->bookingDetails->count() }} ghế
                                        </div>
                                    </div>

                                    <!-- Voucher -->
                                    @if($booking->voucher)
                                        <div class="bg-gradient-to-r from-green-50 to-emerald-100 border border-green-200 rounded-lg p-4">
                                            <h3 class="font-semibold text-green-900 mb-2 flex items-center">
                                                <i class="fas fa-tag mr-2 text-green-500"></i>Mã Giảm Giá
                                            </h3>
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-green-800 font-medium">{{ $booking->voucher->Code }}</p>
                                                    <p class="text-green-600 text-sm">
                                                        @if($booking->voucher->DiscountType === 'Percent')
                                                            Giảm {{ $booking->voucher->Value }}%
                                                        @else
                                                            Giảm {{ number_format($booking->voucher->Value) }}₫
                                                        @endif
                                                    </p>
                                                </div>
                                                <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs font-bold">
                                                    ĐÃ ÁP DỤNG
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Section -->
                        <div class="lg:col-span-1">
                            <div class="sticky top-6">
                                <!-- QR Code -->
                                <div class="bg-white border-2 border-gray-300 rounded-xl p-6 text-center mb-6">
                                    <h3 class="font-bold text-gray-900 mb-3 flex items-center justify-center">
                                        <i class="fas fa-qrcode text-blue-600 mr-2"></i>Mã QR Xác Nhận
                                    </h3>

                                    @if($booking->Status === 'Confirmed')
                                        @php
                                            $confirmationCode = strtoupper(substr(md5($booking->BookingID . $booking->created_at), 0, 8));
                                            $qrData = $confirmationCode;                                           
                                        @endphp

                                        <div id="qrcode" class="flex justify-center mb-3" data-qr-content="{{ htmlspecialchars($qrData) }}"></div>
                                        <p class="text-sm text-gray-600 mb-2">Quét mã để xác nhận vé</p>
                                        <div class="bg-blue-50 rounded-lg p-3">
                                            <p class="text-xs text-blue-800 font-medium">Mã xác nhận:</p>
                                            <p class="text-lg font-bold text-blue-900">{{ $confirmationCode }}</p>
                                        </div>
                                    @else
                                        <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-8 text-center">
                                            <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                                            <p class="text-yellow-800 font-semibold">Vé chưa được xác nhận</p>
                                            <p class="text-yellow-600 text-sm mt-1">Vui lòng thanh toán để kích hoạt mã QR</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Payment Information -->
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <i class="fas fa-credit-card mr-2 text-purple-500"></i>Thanh Toán
                                    </h3>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Phương thức:</span>
                                            <span class="font-semibold text-gray-900">
                                                @if($booking->payment)
                                                    @php
                                                        $paymentMethods = [
                                                            'credit_card' => ['text' => 'Thẻ tín dụng', 'icon' => 'fa-credit-card', 'color' => 'text-blue-600'],
                                                            'bank_transfer' => ['text' => 'Chuyển khoản', 'icon' => 'fa-university', 'color' => 'text-green-600'],
                                                            'momo' => ['text' => 'Ví MoMo', 'icon' => 'fa-mobile-alt', 'color' => 'text-pink-600'],
                                                            'zalopay' => ['text' => 'ZaloPay', 'icon' => 'fa-wallet', 'color' => 'text-blue-500'],
                                                            'cash' => ['text' => 'Tiền mặt', 'icon' => 'fa-money-bill-wave', 'color' => 'text-green-600']
                                                        ];
                                                        $method = $paymentMethods[$booking->payment->PaymentMethod] ?? ['text' => 'Không xác định', 'icon' => 'fa-question-circle', 'color' => 'text-gray-600'];
                                                    @endphp
                                                    <i class="fas {{ $method['icon'] }} {{ $method['color'] }} mr-1"></i>{{ $method['text'] }}
                                                @else
                                                    <i class="fas fa-clock text-yellow-600 mr-1"></i>Chưa thanh toán
                                                @endif
                                            </span>
                                        </div>
                                        @if($booking->payment && $booking->payment->PaymentDate)
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Ngày thanh toán:</span>
                                                <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->payment->PaymentDate)->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                            <span class="text-gray-600">Trạng thái:</span>
                                            <span class="font-semibold {{ $booking->PaymentStatus === 'Paid' ? 'text-green-600' : 'text-yellow-600' }}">
                                                @if($booking->PaymentStatus === 'Paid')
                                                    <i class="fas fa-check-circle mr-1"></i>Đã thanh toán
                                                @else
                                                    <i class="fas fa-clock mr-1"></i>Chờ thanh toán
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Important Notes -->
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                                    <h4 class="font-semibold text-yellow-800 mb-2 flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>Lưu Ý Quan Trọng
                                    </h4>
                                    <ul class="text-xs text-yellow-700 space-y-1">
                                        <li>• Đến rạp trước 15 phút để làm thủ tục</li>
                                        <li>• Mang theo mã QR hoặc mã đặt vé</li>
                                        <li>• Xuất trình vé khi nhân viên yêu cầu</li>
                                        <li>• Vé đã in không thể hoàn trả</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-end mt-8 pt-6 border-t border-gray-200 no-print">
                        <a href="{{ route('customer.booking.history') }}"
                            class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition-colors text-center flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>

                        @if($booking->Status === 'Created')
                            <form action="{{ route('customer.booking.cancel', $booking->BookingID) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors text-center flex items-center justify-center"
                                    onclick="return confirm('Bạn có chắc chắn muốn hủy đặt vé này?')">
                                    <i class="fas fa-times mr-2"></i>Hủy Đặt Vé
                                </button>
                            </form>

                            @if($booking->PaymentStatus === 'Pending')
                                <a href="{{ route('customer.booking.payment', $booking->BookingID) }}"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center flex items-center justify-center">
                                    <i class="fas fa-credit-card mr-2"></i>Thanh Toán Ngay
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                font-size: 12pt;
            }

            .bg-gray-50,
            .bg-white {
                background: white !important;
            }

            .shadow-sm,
            .shadow {
                box-shadow: none !important;
            }

            .rounded-2xl,
            .rounded-lg,
            .rounded {
                border-radius: 0 !important;
            }

            .border {
                border: 1px solid #000 !important;
            }

            .text-gray-600,
            .text-gray-500 {
                color: #000 !important;
            }

            .grid-cols-2 {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            .gap-8 {
                gap: 1rem !important;
            }

            .p-8 {
                padding: 1rem !important;
            }

            .mt-8,
            .mb-8 {
                margin: 1rem 0 !important;
            }

            /* Ensure QR code prints properly */
            #qrcode canvas {
                max-width: 100% !important;
                height: auto !important;
            }
        }

        /* Animation for status badges */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .bg-yellow-100 {
            animation: pulse 2s infinite;
        }

        /* QR Code styling */
        #qrcode {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #qrcode canvas {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px;
            background: white;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrContainer = document.getElementById('qrcode');
            if (qrContainer) {
                const qrContent = qrContainer.getAttribute('data-qr-content');
                
                if (qrContent) {
                    try {
                        // Clear container first
                        qrContainer.innerHTML = '';
                        
                        // Create QR code
                        new QRCode(qrContainer, {
                            text: qrContent,
                            width: 180,
                            height: 180,
                            colorDark: "#1f2937",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } catch (error) {
                        console.error('QR Code generation failed:', error);
                        qrContainer.innerHTML = `
                            <div class="bg-red-100 border border-red-300 rounded-lg p-4 text-center">
                                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                                <p class="text-red-800 text-sm">Không thể tạo mã QR</p>
                            </div>
                        `;
                    }
                }
            }

            // Add confirmation for cancellation
            const cancelForm = document.querySelector('form[action*="cancel"]');
            if (cancelForm) {
                cancelForm.addEventListener('submit', function(e) {
                    if (!confirm('Bạn có chắc chắn muốn hủy đặt vé này? Hành động này không thể hoàn tác.')) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
@endsection