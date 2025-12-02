@extends('customer.layouts.app')

@section('title', 'Lỗi Thanh Toán')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <div class="mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Lỗi Thanh Toán</h1>
                    <p class="text-gray-600 mb-6">{{ $message ?? 'Đã xảy ra lỗi trong quá trình thanh toán.' }}</p>
                </div>

                <div class="space-y-4">
                    <a href="{{ route('customer.booking.history') }}" 
                       class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-history mr-2"></i>Xem Lịch Sử Đặt Vé
                    </a>
                    
                    <a href="{{ route('customer.home') }}" 
                       class="block w-full bg-gray-100 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-200 transition-colors">
                        <i class="fas fa-home mr-2"></i>Về Trang Chủ
                    </a>
                    
                    @if(isset($bookingId))
                    <a href="{{ route('customer.booking.payment', $bookingId) }}" 
                       class="block w-full bg-yellow-100 text-yellow-700 py-3 rounded-lg font-semibold hover:bg-yellow-200 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Thử Lại Thanh Toán
                    </a>
                    @endif
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        Cần hỗ trợ? 
                        <a href="mailto:support@cinema.com" class="text-blue-600 hover:underline">Liên hệ chúng tôi</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection