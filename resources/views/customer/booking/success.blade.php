@extends('customer.layouts.app')

@section('title', 'Đặt Vé Thành Công - ' . $booking->showtime->movie->Title)

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <!-- Success Card -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <!-- Success Header -->
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-green-500 text-3xl"></i>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Đặt Vé Thành Công!</h1>
                        <p class="text-gray-600">Cảm ơn bạn đã đặt vé. Thông tin vé đã được gửi đến email của bạn.</p>
                    </div>

                    <!-- Movie & Showtime Info -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6">
                        <div class="flex items-start space-x-4">
                            <!-- Movie Poster -->
                            <div class="w-16 h-24 flex-shrink-0 rounded-lg overflow-hidden shadow-md">
                                @if($booking->showtime->movie->PosterURL && Storage::exists('public/movies/' . $booking->showtime->movie->PosterURL))
                                    <img src="{{ Storage::url('movies/' . $booking->showtime->movie->PosterURL) }}"
                                        alt="{{ $booking->showtime->movie->Title }}" class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                        <i class="fas fa-film text-white"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Movie Details -->
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $booking->showtime->movie->Title }}</h2>
                                <div class="flex flex-wrap gap-3 text-sm text-gray-600 mb-2">
                                    <span class="flex items-center">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('H:i') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="far fa-calendar mr-1"></i>
                                        {{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('d/m/Y') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-door-open mr-1"></i>
                                        Phòng {{ $booking->showtime->room->RoomName }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700">
                                    <i class="fas fa-video mr-1"></i>
                                    {{ $booking->showtime->room->theater->Name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Seats Section -->
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-chair text-blue-500 mr-2"></i>
                                Ghế Đã Đặt
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($booking->bookingDetails as $detail)
                                    <div class="bg-blue-50 rounded-lg px-3 py-2 text-center min-w-[60px]">
                                        <div class="text-lg font-bold text-blue-700">{{ $detail->seat->SeatNumber }}</div>
                                        <div class="text-xs text-blue-600 mt-1">
                                            @if($detail->seat->SeatType == 'Standard')
                                                Thường
                                            @elseif($detail->seat->SeatType == 'VIP')
                                                <span class="font-semibold">VIP</span>
                                            @elseif($detail->seat->SeatType == 'Couple')
                                                Đôi
                                            @else
                                                {{ $detail->seat->SeatType }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Payment & Codes Section -->
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-receipt text-green-500 mr-2"></i>
                                Thanh Toán & Mã Vé
                            </h3>

                            <!-- Total Amount -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Tổng thanh toán:</span>
                                    <span
                                        class="text-xl font-bold text-green-600">{{ number_format($booking->TotalAmount) }}₫</span>
                                </div>
                            </div>

                            <!-- Booking Code -->
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 mb-1">Mã đặt vé:</p>
                                <div class="bg-blue-50 rounded-lg p-3">
                                    <p class="text-lg font-bold text-blue-700 text-center booking-code" data-code="#{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}">
                                        #{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Confirmation Code -->
                            @php
                                $confirmationCode = strtoupper(substr(md5($booking->BookingID . $booking->created_at), 0, 8));
                            @endphp
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Mã xác nhận:</p>
                                <div class="bg-emerald-50 rounded-lg p-3">
                                    <p class="text-lg font-bold text-emerald-700 text-center font-mono confirmation-code" data-code="{{ $confirmationCode }}">
                                        {{ $confirmationCode }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 mb-8 text-center">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center justify-center">
                            <i class="fas fa-qrcode text-gray-400 mr-2"></i>
                            Mã QR Nhận Vé
                        </h3>

                        <!-- QR Code Container -->
                        <div class="inline-block">
                            <div class="bg-white p-4 rounded-lg border border-gray-300">
                                @if($booking->Status === 'Confirmed')
                                    @php
                                        $confirmationCode = strtoupper(substr(md5($booking->BookingID . $booking->created_at), 0, 8));
                                        $qrData = $confirmationCode;
                                    @endphp

                                    <div id="qrcode-container" class="flex justify-center mb-3">
                                        <div id="qrcode" class="w-48 h-48"></div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <div class="bg-blue-50 rounded-lg p-3">
                                            <p class="text-xs text-blue-800 font-medium">Mã xác nhận:</p>
                                            <p class="text-lg font-bold text-blue-900">{{ $confirmationCode }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-8 text-center">
                                        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                                        <p class="text-yellow-800 font-semibold">Vé chưa được xác nhận</p>
                                        <p class="text-yellow-600 text-sm mt-1">Vui lòng thanh toán để kích hoạt mã QR</p>
                                    </div>
                                @endif
                            </div>

                            <!-- QR Status -->
                            @if($booking->Status === 'Confirmed')
                            <div class="mt-4">
                                <span
                                    class="inline-flex items-center bg-green-50 text-green-700 px-3 py-1 rounded-full text-sm">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Mã QR hợp lệ đến {{ \Carbon\Carbon::parse($booking->showtime->StartTime)->format('H:i d/m') }}
                                </span>
                            </div>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mt-4">
                            <i class="fas fa-camera mr-1"></i>
                            Quét mã này tại quầy vé để nhận vé
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="{{ route('customer.booking.show', $booking->BookingID) }}"
                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            Xem Chi Tiết Vé
                        </a>
                        <a href="{{ route('customer.home') }}"
                            class="flex-1 border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center">
                            <i class="fas fa-home mr-2"></i>
                            Về Trang Chủ
                        </a>
                        @if($booking->Status === 'Confirmed')
                        <button onclick="window.print()" 
                                class="flex-1 border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center">
                            <i class="fas fa-print mr-2"></i>
                            In Vé
                        </button>
                        @endif
                    </div>

                    <!-- Important Notes -->
                    <div class="bg-yellow-50 rounded-xl p-5 border border-yellow-200">
                        <h3 class="font-semibold text-yellow-800 mb-3 flex items-center">
                            <i class="fas fa-exclamation-circle text-yellow-500 mr-2"></i>
                            Lưu Ý Quan Trọng
                        </h3>
                        <ul class="space-y-2 text-sm text-yellow-700">
                            <li class="flex items-start">
                                <i class="fas fa-clock text-yellow-500 mr-2 mt-0.5"></i>
                                <span>Vui lòng đến rạp trước <strong class="text-yellow-800">15 phút</strong> để làm thủ
                                    tục</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-qrcode text-yellow-500 mr-2 mt-0.5"></i>
                                <span>Mang theo <strong class="text-yellow-800">mã QR</strong> hoặc <strong
                                        class="text-yellow-800">mã đặt vé</strong> khi đến rạp</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-ban text-yellow-500 mr-2 mt-0.5"></i>
                                <span>Vé không thể hoàn trả hoặc đổi sau khi mua</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-phone text-yellow-500 mr-2 mt-0.5"></i>
                                <span>Liên hệ hotline <strong
                                        class="text-yellow-800">{{ $booking->showtime->room->theater->Phone ?? '1900 0000' }}</strong>
                                    nếu có thắc mắc</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Email Confirmation -->
                    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-600">
                            <i class="far fa-envelope text-gray-400 mr-1"></i>
                            Thông tin chi tiết đã được gửi đến <strong>{{ Auth::user()->email }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Animation for success check */
        .fa-check {
            animation: scaleCheck 0.5s ease-in-out;
        }

        @keyframes scaleCheck {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            70% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .bg-white.rounded-2xl,
            .bg-white.rounded-2xl * {
                visibility: visible;
            }
            .bg-white.rounded-2xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                border: none;
            }
            .no-print {
                display: none !important;
            }
            .bg-yellow-50,
            .bg-gray-50 {
                background-color: white !important;
                border: 1px solid #ddd !important;
            }
        }

        /* QR Code style */
        #qrcode canvas {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Clickable code style */
        .booking-code,
        .confirmation-code {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .booking-code:hover,
        .confirmation-code:hover {
            opacity: 0.8;
            transform: scale(1.02);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to show notification
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300 opacity-0 translate-y-2`;
                notification.textContent = message;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.classList.remove('opacity-0', 'translate-y-2');
                    notification.classList.add('opacity-100', 'translate-y-0');
                }, 10);

                setTimeout(() => {
                    notification.classList.remove('opacity-100', 'translate-y-0');
                    notification.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => notification.remove(), 300);
                }, 2000);
            }

            // Copy to clipboard functionality
            function setupCopyToClipboard(element, message) {
                if (element) {
                    element.addEventListener('click', function () {
                        const text = this.getAttribute('data-code') || this.textContent.trim();
                        navigator.clipboard.writeText(text).then(() => {
                            showNotification(message);
                        }).catch(err => {
                            console.error('Copy failed:', err);
                            showNotification('Sao chép thất bại!', 'error');
                        });
                    });
                    
                    element.title = 'Click để sao chép';
                }
            }

            // Set up copy functionality for codes
            setupCopyToClipboard(document.querySelector('.booking-code'), 'Đã sao chép mã đặt vé!');
            setupCopyToClipboard(document.querySelector('.confirmation-code'), 'Đã sao chép mã xác nhận!');

            // Generate QR Code if container exists
            const qrContainer = document.getElementById('qrcode');
            if (qrContainer && typeof QRCode !== 'undefined') {
                @if($booking->Status === 'Confirmed')
                    const qrData = @json($qrData);
                    
                    try {
                        // Clear container first
                        qrContainer.innerHTML = '';
                        
                        // Generate QR Code using the library
                        const qrcode = new QRCode(qrContainer, {
                            text: JSON.stringify(qrData),
                            width: 180,
                            height: 180,
                            colorDark: "#1e40af", // blue-800
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });

                        // Add download QR Code button after a short delay (wait for QR to render)
                        setTimeout(() => {
                            const downloadBtn = document.createElement('button');
                            downloadBtn.type = 'button';
                            downloadBtn.className = 'mt-3 text-blue-600 hover:text-blue-800 text-sm flex items-center justify-center mx-auto no-print';
                            downloadBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Tải mã QR';
                            downloadBtn.addEventListener('click', function() {
                                const canvas = qrContainer.querySelector('canvas');
                                if (canvas) {
                                    const link = document.createElement('a');
                                    link.download = `QR-Ve-{{ str_pad($booking->BookingID, 6, '0', STR_PAD_LEFT) }}.png`;
                                    link.href = canvas.toDataURL('image/png');
                                    link.click();
                                    showNotification('Đang tải mã QR...');
                                }
                            });
                            
                            const container = document.getElementById('qrcode-container');
                            if (container && !container.querySelector('.no-print')) {
                                container.appendChild(downloadBtn);
                            }
                        }, 500);

                    } catch (error) {
                        console.error('QR Code generation failed:', error);
                        qrContainer.innerHTML = `
                            <div class="bg-red-100 border border-red-300 rounded-lg p-4 text-center">
                                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
                                <p class="text-red-800 text-sm">Lỗi tạo mã QR: ${error.message}</p>
                                <p class="text-sm text-gray-600 mt-2">Mã xác nhận: <strong>{{ $confirmationCode }}</strong></p>
                            </div>
                        `;
                    }
                @endif
            } else if (qrContainer && typeof QRCode === 'undefined') {
                // QRCode library not loaded
                qrContainer.innerHTML = `
                    <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4 text-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                        <p class="text-yellow-800 text-sm">Không thể tải thư viện QR Code</p>
                        <p class="text-sm text-gray-600 mt-2">Vui lòng sử dụng mã xác nhận: <strong>{{ $confirmationCode }}</strong></p>
                    </div>
                `;
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endsection