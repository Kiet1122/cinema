@extends('customer.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-800 mb-3">Voucher Của Tôi</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">Danh sách các mã giảm giá bạn đã nhận và có thể sử dụng</p>
        </div>
        
        @if($myVouchers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach($myVouchers as $customerVoucher)
            <div class="group relative">
                <!-- Hiệu ứng background -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                
                <!-- Card content -->
                <div class="relative bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 transform hover:-translate-y-2">
                    <!-- Ribbon badge -->
                    <div class="absolute -top-3 -right-3">
                        <span class="bg-gradient-to-r from-green-400 to-green-500 text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg">
                            {{ $customerVoucher->voucher->DiscountType == 'Percent' ? $customerVoucher->voucher->Value . '%' : number_format($customerVoucher->voucher->Value) . 'đ' }}
                        </span>
                    </div>
                    
                    <!-- Voucher code -->
                    <div class="text-center mb-6">
                        <div class="inline-block bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-2xl font-bold py-3 px-8 rounded-lg shadow-md">
                            {{ $customerVoucher->voucher->Code }}
                        </div>
                        <p class="text-gray-500 text-sm mt-2">Mã giảm giá</p>
                    </div>
                    
                    <!-- Info grid -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-day text-indigo-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700">Bắt đầu</span>
                            </div>
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($customerVoucher->voucher->StartDate)->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-times text-red-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700">Hết hạn</span>
                            </div>
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($customerVoucher->voucher->EndDate)->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-gift text-purple-500 mr-3"></i>
                                <span class="text-sm font-medium text-gray-700">Nhận ngày</span>
                            </div>
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($customerVoucher->created_at)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-4 text-center">
                        <div class="flex items-center justify-center space-x-3">
                            <i class="fas fa-check-circle text-green-500 text-lg"></i>
                            <div>
                                <p class="text-green-800 font-bold text-sm">SẴN SÀNG SỬ DỤNG</p>
                                <p class="text-green-600 text-xs mt-1">Áp dụng khi đặt vé</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty state với gradient -->
        <div class="max-w-2xl mx-auto text-center">
            <div class="bg-white rounded-3xl shadow-xl p-12 border border-gray-100">
                <div class="text-gray-300 text-9xl mb-8">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-500 mb-4">Chưa có voucher nào</h3>
                <p class="text-gray-400 text-lg mb-8 leading-relaxed">
                    Bạn chưa nhận được voucher nào từ hệ thống.<br>
                    Hãy tham gia các chương trình khuyến mãi để nhận ưu đãi hấp dẫn!
                </p>
                <a href="{{ route('customer.home') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold rounded-2xl shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <i class="fas fa-home mr-3"></i>
                    Khám phá ưu đãi ngay
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection