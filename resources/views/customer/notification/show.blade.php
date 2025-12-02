@extends('customer.layouts.app')

@section('title', $notification->Title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li><a href="{{ route('customer.home') }}" class="hover:text-blue-600">Trang chủ</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li><a href="{{ route('customer.notifications.index') }}" class="hover:text-blue-600">Thông báo</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-gray-900 font-medium">Chi tiết</li>
                </ol>
            </nav>

            <!-- Thông báo chi tiết -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <!-- Header -->
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $notification->Title }}</h1>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span>
                                <i class="fas fa-clock mr-1"></i>
                                {{ $notification->created_at->format('H:i d/m/Y') }}
                            </span>
                            <span class="flex items-center">
                                <i class="fas fa-circle text-xs mr-1 {{ $notification->Status === 'Unread' ? 'text-red-500' : 'text-green-500' }}"></i>
                                {{ $notification->Status === 'Unread' ? 'Chưa đọc' : 'Đã đọc' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('customer.notifications.index') }}" 
                           class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Quay lại
                        </a>
                        <form action="{{ route('customer.notifications.destroy', $notification->NotificationID) }}" 
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?')">
                                <i class="fas fa-trash mr-2"></i>Xóa
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Nội dung -->
                <div class="prose max-w-none mb-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $notification->Message }}</p>
                    </div>
                </div>

                <!-- Thông tin hệ thống -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Thông tin hệ thống</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Mã thông báo:</span>
                            <span>#{{ $notification->NotificationID }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Ngày gửi:</span>
                            <span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Trạng thái:</span>
                            <span class="capitalize">{{ $notification->Status === 'Unread' ? 'Chưa đọc' : 'Đã đọc' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection