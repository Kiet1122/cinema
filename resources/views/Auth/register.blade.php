@extends('customer.layouts.app')

@section('title', 'Đăng Ký')

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-indigo-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full">
            <!-- Card Container -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
                <!-- Left Side - Graphic/Image -->
                <div
                    class="md:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 p-12 flex flex-col justify-center text-white relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute -top-24 -right-24 w-48 h-48 bg-white rounded-full"></div>
                        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-white rounded-full"></div>
                    </div>

                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-ticket-alt text-white text-3xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-center mb-4">Cinema Booking</h1>
                        <p class="text-blue-100 text-lg text-center leading-relaxed">
                            Đặt vé xem phim dễ dàng và nhanh chóng. Trải nghiệm điện ảnh tuyệt vời cùng chúng tôi.
                        </p>

                        <!-- Features List -->
                        <div class="mt-8 space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-300 mr-3 text-lg"></i>
                                <span class="text-blue-100">Đặt vé nhanh chóng</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-300 mr-3 text-lg"></i>
                                <span class="text-blue-100">Ghế ngồi thoải mái</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-300 mr-3 text-lg"></i>
                                <span class="text-blue-100">Ưu đãi đặc biệt</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Registration Form -->
                <div class="md:w-1/2 p-12 flex flex-col justify-center">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">Tạo Tài Khoản Mới</h2>
                        <p class="text-gray-600 mt-2">Tham gia cùng chúng tôi để trải nghiệm dịch vụ tốt nhất</p>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                                <span class="font-medium">{{ $errors->first() }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-3 text-lg"></i>
                                <span class="font-medium">{{ session('status') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Registration Form -->
                    <form class="space-y-6" method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Full Name Field -->
                        <div>
                            <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-user text-blue-500 mr-2"></i>Họ và tên
                            </label>
                            <div class="relative">
                                <input id="full_name" name="full_name" type="text" required
                                    class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 @error('name') border-red-500 @enderror"
                                    placeholder="Nguyễn Văn A" value="{{ old('full_name') }}" autocomplete="full_name" autofocus>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-id-card text-gray-400"></i>
                                </div>
                            </div>
                            @error('full_name')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-envelope text-blue-500 mr-2"></i>Địa chỉ Email
                            </label>
                            <div class="relative">
                                <input id="email" name="email" type="email" required
                                    class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 @error('email') border-red-500 @enderror"
                                    placeholder="nguyenvana@example.com" value="{{ old('email') }}" autocomplete="email">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-at text-gray-400"></i>
                                </div>
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-lock text-blue-500 mr-2"></i>Mật khẩu
                            </label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required
                                    class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 pr-12 @error('password') border-red-500 @enderror"
                                    placeholder="Nhập mật khẩu" autocomplete="new-password">
                                <!-- ĐÃ BỎ minlength="8" -->
                                <button type="button"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition duration-300"
                                    onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-toggle-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Confirm Password Field -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-lock text-blue-500 mr-2"></i>Xác nhận mật khẩu
                            </label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 pr-12 @error('password_confirmation') border-red-500 @enderror"
                                    placeholder="Nhập lại mật khẩu" autocomplete="new-password">
                                <button type="button"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 transition duration-300"
                                    onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="confirm-password-toggle-icon"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Phone Field -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-phone text-blue-500 mr-2"></i>Số điện thoại
                            </label>
                            <div class="relative">
                                <input id="phone" name="phone" type="tel"
                                    class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-300 @error('phone') border-red-500 @enderror"
                                    placeholder="0123 456 789" value="{{ old('phone') }}" autocomplete="tel">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-mobile-alt text-gray-400"></i>
                                </div>
                            </div>
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" required
                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition duration-300">
                            </div>
                            <label for="terms" class="ml-3 text-sm text-gray-700">
                                Tôi đồng ý với
                                <a href="#"
                                    class="text-blue-600 hover:text-blue-500 font-semibold transition duration-300 hover:underline">
                                    Điều khoản dịch vụ
                                </a> và
                                <a href="#"
                                    class="text-blue-600 hover:text-blue-500 font-semibold transition duration-300 hover:underline">
                                    Chính sách bảo mật
                                </a>
                            </label>
                        </div>
                        @error('terms')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                            </p>
                        @enderror

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 px-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-offset-2 transition duration-300 transform hover:scale-[1.02] font-bold text-lg shadow-lg">
                            <i class="fas fa-user-plus mr-3"></i>Đăng Ký Tài Khoản
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <div class="text-center">
                            <span class="text-gray-600">Đã có tài khoản?</span>
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}"
                                    class="text-blue-600 hover:text-blue-500 font-bold ml-2 transition duration-300 hover:underline text-lg">
                                    Đăng nhập ngay
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Social Registration -->
                    <div class="mt-8">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 bg-white text-gray-500">Hoặc đăng ký với</span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <button type="button"
                                class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-300 hover:shadow-md">
                                <i class="fab fa-google text-red-500 text-lg mr-2"></i>
                                <span>Google</span>
                            </button>

                            <button type="button"
                                class="w-full inline-flex justify-center items-center py-3 px-4 border border-gray-300 rounded-xl shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-300 hover:shadow-md">
                                <i class="fab fa-facebook text-blue-600 text-lg mr-2"></i>
                                <span>Facebook</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId === 'password' ? 'password-toggle-icon' : 'confirm-password-toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Auto hide alerts
        document.addEventListener('DOMContentLoaded', function () {
            const alerts = document.querySelectorAll('.bg-red-50, .bg-green-50');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'all 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(100%)';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });

            // Add floating animation to left side elements
            const leftSide = document.querySelector('.bg-gradient-to-br');
            if (leftSide) {
                leftSide.style.animation = 'float 6s ease-in-out infinite';
            }
        });
    </script>

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
@endsection