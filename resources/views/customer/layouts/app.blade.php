<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cinema Booking')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Trong head tag -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        .nav-blur {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.8);
        }

        .footer-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .dropdown-menu {
            opacity: 0;
            transform: translateY(-10px);
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .dropdown:hover .dropdown-menu {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .container-padding {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        @media (min-width: 1536px) {
            .container-max {
                max-width: 1536px;
            }
        }
    </style>
    @yield('styles')
</head>

<body class="antialiased">
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Modern Navigation Bar - Fully Responsive -->
        <nav class="nav-blur border-b border-gray-200/50 sticky top-0 z-50">
            <div class="w-full container-padding">
                <div class="flex justify-between items-center h-16 lg:h-20 mx-16">
                    <!-- Logo - Responsive -->
                    <div class="flex items-center space-x-2 lg:space-x-3">
                        <a href="{{ route('customer.home') }}" class="flex items-center space-x-2 lg:space-x-3 group">
                            <div
                                class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg lg:rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-film text-white text-sm lg:text-lg"></i>
                            </div>
                            <div class="hidden sm:block">
                                <span
                                    class="text-xl lg:text-2xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">CINEMA</span>
                                <div class="text-xs text-gray-500 font-medium hidden lg:block">PREMIUM EXPERIENCE</div>
                            </div>
                        </a>
                    </div>

                    <!-- Center Navigation - Desktop -->
                    <div class="hidden xl:flex items-center space-x-1">
                        <a href="{{ route('customer.home') }}"
                            class="relative px-4 lg:px-6 py-2 lg:py-3 text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors duration-300 group">
                            <i class="fas fa-home mr-2"></i>
                            Trang Chủ
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 group-hover:w-full transition-all duration-300"></span>
                        </a>
                        <a href="{{ route('customer.booking.history') }}"
                            class="relative px-4 lg:px-6 py-2 lg:py-3 text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors duration-300 group">
                            <i class="fas fa-history mr-2"></i>
                            Lịch Sử
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 group-hover:w-full transition-all duration-300"></span>
                        </a>
                        <a href="{{ route('customer.voucher.list') }}"
                            class="relative px-4 lg:px-6 py-2 lg:py-3 text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors duration-300 group">
                            <i class="fas fa-gift mr-2"></i>
                            Khuyến Mãi
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 group-hover:w-full transition-all duration-300"></span>
                        </a>
                        <a href="{{ route('customer.contact') }}"
   class="relative px-4 lg:px-6 py-2 lg:py-3 text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors duration-300 group">
    <i class="fas fa-gift mr-2"></i>
    Liên hệ
    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-indigo-600 group-hover:w-full transition-all duration-300"></span>
</a>



                    </div>

                    <!-- Right Side - User Menu -->
                    <div class="flex items-center space-x-2 lg:space-x-4">
                        @auth
                                        <!-- Notification Dropdown -->
                                        <div class="relative" x-data="{ 
                                                                        open: false,
                                                                        markAsRead(notificationId) {
                                                                            fetch(`/customer/notifications/${notificationId}/mark-read`, {
                                                                                method: 'POST',
                                                                                headers: {
                                                                                    'Content-Type': 'application/json',
                                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                                    'Accept': 'application/json'
                                                                                }
                                                                            })
                                                                            .then(response => response.json())
                                                                            .then(data => {
                                                                                if (data.success) {
                                                                                    // Ẩn thông báo đã đọc khỏi dropdown
                                                                                    const notificationElement = document.querySelector(`[data-notification-id='${notificationId}']`);
                                                                                    if (notificationElement) {
                                                                                        notificationElement.style.display = 'none';
                                                                                    }

                                                                                    // Cập nhật badge count
                                                                                    this.updateBadgeCount();

                                                                                    // Hiển thị thông báo thành công
                                                                                    this.showToast('Đã đánh dấu đã đọc', 'success');
                                                                                } else {
                                                                                    this.showToast(data.message || 'Có lỗi xảy ra', 'error');
                                                                                }
                                                                            })
                                                                            .catch(error => {
                                                                                console.error('Error:', error);
                                                                                this.showToast('Có lỗi xảy ra khi đánh dấu đã đọc', 'error');
                                                                            });
                                                                        },
                                                                        updateBadgeCount() {
                                                                            // Có thể gọi API để cập nhật số lượng thông báo chưa đọc
                                                                            const badge = document.querySelector('.notification-badge');
                                                                            if (badge) {
                                                                                // Giảm số lượng hoặc ẩn badge nếu cần
                                                                                // Hoặc gọi API để lấy số lượng mới
                                                                            }
                                                                        },
                                                                        showToast(message, type = 'success') {
                                                                            // Tạo toast notification đơn giản
                                                                            const toast = document.createElement('div');
                                                                            toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${
                                                                                type === 'success' ? 'bg-green-500' : 'bg-red-500'
                                                                            }`;
                                                                            toast.textContent = message;
                                                                            document.body.appendChild(toast);

                                                                            setTimeout(() => {
                                                                                toast.remove();
                                                                            }, 3000);
                                                                        }
                                                                    }">
                                            <button
                                                class="relative p-2 lg:p-3 text-gray-600 hover:text-indigo-600 transition-colors duration-300 group"
                                                @click="open = !open">
                                                <i class="fas fa-bell text-base lg:text-lg"></i>
                                                @if(($unreadCount ?? 0) > 0)
                                                    <span
                                                        class="absolute top-1 right-1 lg:top-2 lg:right-2 w-2 h-2 bg-red-500 rounded-full notification-badge"></span>
                                                @endif
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div x-show="open" @click.away="open = false"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                                class="absolute right-0 mt-2 w-80 lg:w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50"
                                                style="display: none;">

                                                <!-- Header -->
                                                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                                                    <div>
                                                        <h3 class="font-semibold text-gray-900">Thông báo mới</h3>
                                                        @if(($unreadCount ?? 0) > 0)
                                                            <p class="text-xs text-red-600 font-medium mt-1">
                                                                <i class="fas fa-circle mr-1"></i>
                                                                Có {{ $unreadCount }} thông báo chưa đọc
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        @if(($unreadCount ?? 0) > 0)
                                                            <form action="{{ route('customer.notifications.markAllRead') }}" method="POST"
                                                                class="inline">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 font-medium transition-colors"
                                                                    @click="open = false">
                                                                    <i class="fas fa-check-double mr-1"></i>Đọc tất cả
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <a href="{{ route('customer.notifications.index') }}"
                                                            class="text-xs text-gray-600 hover:text-gray-800 font-medium"
                                                            @click="open = false">
                                                            Xem tất cả
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- Notifications List -->
                                                <div class="max-h-96 overflow-y-auto">
                                                    @php
                                                        $unreadNotifications = ($recentNotifications ?? collect())->where('Status', 'Unread');
                                                    @endphp

                                                    @if($unreadNotifications->count() > 0)
                                                        @foreach($unreadNotifications as $notification)
                                                            <div class="border-b border-gray-100 last:border-b-0"
                                                                data-notification-id="{{ $notification->NotificationID }}">
                                                                <div
                                                                    class="block p-4 transition-all duration-200 relative bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 shadow-sm hover:from-blue-100 hover:to-indigo-100">

                                                                    <!-- Dot indicator -->
                                                                    <div
                                                                        class="absolute top-4 left-2 w-2 h-2 bg-blue-500 rounded-full animate-pulse">
                                                                    </div>

                                                                    <div class="flex items-start space-x-3">
                                                                        <div class="flex-shrink-0">
                                                                            <div
                                                                                class="w-10 h-10 rounded-full flex items-center justify-center bg-blue-100 ring-2 ring-blue-300">
                                                                                <i class="fas fa-bell text-blue-600 text-sm"></i>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-1 min-w-0">
                                                                            <div class="flex items-start justify-between mb-1">
                                                                                <a href="{{ route('customer.notifications.show', $notification->NotificationID) }}"
                                                                                    class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors"
                                                                                    @click="open = false">
                                                                                    {{ Str::limit($notification->Title, 45) }}
                                                                                </a>
                                                                                <span
                                                                                    class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-medium animate-bounce ml-2">
                                                                                    MỚI
                                                                                </span>
                                                                            </div>
                                                                            <p class="text-sm text-gray-700 mb-2 leading-relaxed">
                                                                                {{ Str::limit($notification->Message, 70) }}
                                                                            </p>
                                                                            <div class="flex items-center justify-between">
                                                                                <span class="text-xs text-blue-600 font-medium">
                                                                                    <i class="fas fa-clock mr-1"></i>
                                                                                    {{ $notification->created_at->diffForHumans() }}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <!-- Empty State -->
                                                        <div class="text-center py-8">
                                                            <div
                                                                class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                                            </div>
                                                            <p class="text-gray-500 text-sm mb-2">Tuyệt vời!</p>
                                                            <p class="text-gray-400 text-xs">Bạn đã đọc tất cả thông báo</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Footer -->
                                                <div class="p-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                                                    <a href="{{ route('customer.notifications.index') }}"
                                                        class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
                                                        @click="open = false">
                                                        <i class="fas fa-list mr-2"></i>Xem tất cả thông báo
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- User Menu Dropdown -->
                                        <div class="dropdown relative">
                                            <div class="flex items-center space-x-2 lg:space-x-3 group cursor-pointer">
                                                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-indigo-500 to-purple-500 
                                                            rounded-full flex items-center justify-center shadow-lg 
                                                            group-hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                                                    @if(optional(Auth::user()->customer)->Avatar)
                                                        <img src="{{ Auth::user()->customer->Avatar }}"
                                                            class="w-full h-full object-cover" />
                                                    @else
                                                        <i class="fas fa-user text-white text-xs lg:text-sm"></i>
                                                    @endif
                                                </div>

                                                <div class="hidden md:block text-right">
                                                    <div
                                                        class="text-sm font-semibold text-gray-900 truncate max-w-[120px] lg:max-w-none">
                                                        {{ Auth::user()->customer->FullName ?? 'Khách hàng' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 hidden lg:block">Premium Member</div>
                                                </div>
                                                <i
                                                    class="fas fa-chevron-down text-gray-400 text-xs group-hover:text-indigo-600 transition-colors duration-300 hidden lg:block"></i>
                                            </div>

                                            <!-- Dropdown Menu -->
                                            <div
                                                class="dropdown-menu absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-100 py-2 z-50">
                                                <!-- User Info -->
                                                <div class="px-4 py-3 border-b border-gray-100">
                                                    <div class="text-sm font-semibold text-gray-900 truncate">
                                                        {{ Auth::user()->customer->FullName ?? 'Khách hàng' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">{{ Auth::user()->Email ?? 'user@example.com'
                                                        }}
                                                    </div>
                                                </div>

                                                <!-- Menu Items -->
                                                <a href="{{ route('customer.profile.index') }}"
                                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200">
                                                    <i class="fas fa-user-circle text-gray-400 mr-3 w-5"></i>
                                                    Hồ sơ cá nhân
                                                </a>

                                                <a href="{{ route('customer.booking.history') }}"
                                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200">
                                                    <i class="fas fa-history text-gray-400 mr-3 w-5"></i>
                                                    Lịch sử đặt vé
                                                </a>

                                                <a href="{{ route('customer.notifications.index') }}"
                                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200">
                                                    <i class="fas fa-bell text-gray-400 mr-3 w-5"></i>
                                                    Thông báo của tôi
                                                </a>

                                                <a href="{{ route('customer.voucher.list') }}"
                                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200">
                                                    <i class="fas fa-ticket-alt text-gray-400 mr-3 w-5"></i>
                                                    Voucher của tôi
                                                </a>

                                                <div class="border-t border-gray-100 my-1"></div>

                                                <!-- Logout -->
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                                        <i class="fas fa-sign-out-alt mr-3 w-5"></i>
                                                        Đăng xuất
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                        @else
                            <!-- Login & Register Buttons (Chỉ hiển thị khi chưa đăng nhập) -->
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('login') }}"
                                    class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors duration-300">
                                    Đăng nhập
                                </a>
                                <a href="{{ route('register') }}"
                                    class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                                    Đăng ký
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu - Enhanced for all mobile sizes -->
        <div class="xl:hidden bg-white/80 border-b border-gray-200 sticky top-16 z-40">
            <div class="w-full container-padding">
                <div class="flex justify-between space-x-1 lg:space-x-2 py-2 lg:py-3 overflow-x-auto scrollbar-hide">
                    <a href="{{ route('customer.home') }}"
                        class="flex flex-col items-center space-y-1 min-w-[60px] lg:min-w-[80px] px-2 py-2 text-gray-700 hover:text-indigo-600 transition-colors duration-300 group">
                        <i class="fas fa-home text-indigo-600 text-sm lg:text-base"></i>
                        <span class="text-xs lg:text-sm font-medium text-center">Trang Chủ</span>
                    </a>
                    <a href=""
                        class="flex flex-col items-center space-y-1 min-w-[60px] lg:min-w-[80px] px-2 py-2 text-gray-700 hover:text-indigo-600 transition-colors duration-300 group">
                        <i class="fas fa-ticket-alt text-indigo-600 text-sm lg:text-base"></i>
                        <span class="text-xs lg:text-sm font-medium text-center">Đặt Vé</span>
                    </a>
                    @auth
                    <a href=""
                        class="flex flex-col items-center space-y-1 min-w-[60px] lg:min-w-[80px] px-2 py-2 text-gray-700 hover:text-indigo-600 transition-colors duration-300 group">
                        <i class="fas fa-history text-indigo-600 text-sm lg:text-base"></i>
                        <span class="text-xs lg:text-sm font-medium text-center">Lịch Sử</span>
                    </a>
                    @endif
                    <a href=""
                        class="flex flex-col items-center space-y-1 min-w-[60px] lg:min-w-[80px] px-2 py-2 text-gray-700 hover:text-indigo-600 transition-colors duration-300 group">
                        <i class="fas fa-gift text-indigo-600 text-sm lg:text-base"></i>
                        <span class="text-xs lg:text-sm font-medium text-center">Khuyến Mãi</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Page Content - Fully Responsive -->
        <main class="flex-grow py-4 lg:py-8">
            <div class="w-full container-padding container-max mx-auto">


                @if(session('error'))
                    <div
                        class="mb-4 lg:mb-8 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 rounded-lg p-4 lg:p-6 shadow-lg">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 lg:w-12 lg:h-12 bg-red-100 rounded-full flex items-center justify-center mr-3 lg:mr-4">
                                <i class="fas fa-exclamation-circle text-red-600 text-base lg:text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-red-800 font-semibold text-sm lg:text-lg truncate">{{ session('error') }}
                                </div>
                                <div class="text-red-600 text-xs lg:text-sm mt-1">Đã xảy ra lỗi</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Content Section -->
                <div class="w-full">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Modern Footer - Fully Responsive -->
        <footer class="footer-gradient text-white mt-auto w-full">
            <div class="w-full">
                <!-- Main Footer Content -->
                <div class="mx-16">
                    <div class="w-full py-8 lg:py-12">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-12">
                            <!-- Brand -->
                            <div class="sm:col-span-2 lg:col-span-2">
                                <div class="flex items-center space-x-3 mb-4 lg:mb-6">
                                    <div
                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg lg:rounded-xl flex items-center justify-center">
                                        <i class="fas fa-film text-white text-base lg:text-lg"></i>
                                    </div>
                                    <span class="text-xl lg:text-2xl font-black text-white">CINEMA</span>
                                </div>
                                <p class="text-gray-300 leading-relaxed mb-4 lg:mb-6 text-sm lg:text-base">
                                    Trải nghiệm điện ảnh đẳng cấp thế giới. Công nghệ tiên tiến, dịch vụ hoàn hảo.
                                </p>
                                <div class="flex space-x-3 lg:space-x-4">
                                    <a href="#"
                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition-all duration-300 hover:scale-110">
                                        <i class="fab fa-facebook-f text-white text-sm lg:text-base"></i>
                                    </a>
                                    <a href="#"
                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition-all duration-300 hover:scale-110">
                                        <i class="fab fa-twitter text-white text-sm lg:text-base"></i>
                                    </a>
                                    <a href="#"
                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition-all duration-300 hover:scale-110">
                                        <i class="fab fa-instagram text-white text-sm lg:text-base"></i>
                                    </a>
                                    <a href="#"
                                        class="w-8 h-8 lg:w-10 lg:h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-white/20 transition-all duration-300 hover:scale-110">
                                        <i class="fab fa-tiktok text-white text-sm lg:text-base"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Quick Links -->
                            <div>
                                <h3 class="font-bold text-base lg:text-lg mb-4 lg:mb-6 text-white">Khám Phá</h3>
                                <ul class="space-y-2 lg:space-y-3">
                                    <li><a href=""
                                            class="text-gray-300 hover:text-white transition-colors duration-300 flex items-center space-x-2 text-sm lg:text-base">
                                            <i class="fas fa-chevron-right text-xs text-indigo-400"></i>
                                            <span>Phim Đang Chiếu</span>
                                        </a></li>
                                    <li><a href=""
                                            class="text-gray-300 hover:text-white transition-colors duration-300 flex items-center space-x-2 text-sm lg:text-base">
                                            <i class="fas fa-chevron-right text-xs text-indigo-400"></i>
                                            <span>Khuyến Mãi</span>
                                        </a></li>
                                    @auth
                                    <li><a href=""
                                            class="text-gray-300 hover:text-white transition-colors duration-300 flex items-center space-x-2 text-sm lg:text-base">
                                            <i class="fas fa-chevron-right text-xs text-indigo-400"></i>
                                            <span>Lịch Sử Đặt Vé</span>
                                        </a></li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Support -->
                            <div>
                                <h3 class="font-bold text-base lg:text-lg mb-4 lg:mb-6 text-white">Hỗ Trợ</h3>
                                <ul class="space-y-2 lg:space-y-3">
                                    <li><a href="#"
                                            class="text-gray-300 hover:text-white transition-colors duration-300 flex items-center space-x-2 text-sm lg:text-base">
                                            <i class="fas fa-phone text-indigo-400 text-sm"></i>
                                            <span>1900 1234</span>
                                        </a></li>
                                    <li><a href="#"
                                            class="text-gray-300 hover:text-white transition-colors duration-300 flex items-center space-x-2 text-sm lg:text-base">
                                            <i class="fas fa-envelope text-indigo-400 text-sm"></i>
                                            <span>support@cinema.com</span>
                                        </a></li>
                                    <li><a href="#"
                                            class="text-gray-300 hover:text-white transition-colors duration-300 flex items-center space-x-2 text-sm lg:text-base">
                                            <i class="fas fa-map-marker-alt text-indigo-400 text-sm"></i>
                                            <span>Hệ Thống Rạp</span>
                                        </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar - Full Width -->
                <div class="border-t border-gray-700 w-full">
                    <div class="mx-16">
                        <div
                            class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 text-center sm:text-left py-6 lg:py-8">
                            <div class="text-gray-400 text-xs lg:text-sm order-2 sm:order-1">
                                © 2025 Cinema Booking System. All rights reserved.
                            </div>
                            <div
                                class="flex space-x-4 lg:space-x-6 text-xs lg:text-sm text-gray-400 order-1 sm:order-2">
                                <a href="#" class="hover:text-white transition-colors duration-300">Privacy</a>
                                <a href="#" class="hover:text-white transition-colors duration-300">Terms</a>
                                <a href="#" class="hover:text-white transition-colors duration-300">Cookies</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Prevent horizontal scroll on mobile
        document.body.style.overflowX = 'hidden';

        // Dropdown functionality
        const dropdowns = document.querySelectorAll('.dropdown');

        dropdowns.forEach(dropdown => {
            const menu = dropdown.querySelector('.dropdown-menu');

            // Show on hover (desktop)
            dropdown.addEventListener('mouseenter', function () {
                if (window.innerWidth >= 1024) {
                    menu.style.opacity = '1';
                    menu.style.transform = 'translateY(0)';
                    menu.style.visibility = 'visible';
                }
            });

            // Hide on mouse leave (desktop)
            dropdown.addEventListener('mouseleave', function () {
                if (window.innerWidth >= 1024) {
                    menu.style.opacity = '0';
                    menu.style.transform = 'translateY(-10px)';
                    menu.style.visibility = 'hidden';
                }
            });

            // Click to toggle (mobile)
            dropdown.addEventListener('click', function (e) {
                if (window.innerWidth < 1024) {
                    e.preventDefault();
                    const isVisible = menu.style.visibility === 'visible';

                    // Close all other dropdowns
                    document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                        if (otherMenu !== menu) {
                            otherMenu.style.opacity = '0';
                            otherMenu.style.transform = 'translateY(-10px)';
                            otherMenu.style.visibility = 'hidden';
                        }
                    });

                    // Toggle current dropdown
                    if (isVisible) {
                        menu.style.opacity = '0';
                        menu.style.transform = 'translateY(-10px)';
                        menu.style.visibility = 'hidden';
                    } else {
                        menu.style.opacity = '1';
                        menu.style.transform = 'translateY(0)';
                        menu.style.visibility = 'visible';
                    }
                }
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.opacity = '0';
                    menu.style.transform = 'translateY(-10px)';
                    menu.style.visibility = 'hidden';
                });
            }
        });

        // Handle resize events
        window.addEventListener('resize', function () {
            // Close all dropdowns on resize
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.style.opacity = '0';
                menu.style.transform = 'translateY(-10px)';
                menu.style.visibility = 'hidden';
            });
        });
    </script>
</body>

</html>