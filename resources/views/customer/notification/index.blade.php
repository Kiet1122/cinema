@extends('customer.layouts.app')

@section('title', 'Thông Báo Của Tôi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Thông Báo</h1>
                    <p class="text-gray-600 mt-2">Quản lý thông báo của bạn</p>
                </div>
                <div class="flex gap-3">
                    @if($unreadCount > 0)
                    <form action="{{ route('customer.notifications.markAllRead') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                            <i class="fas fa-check-double mr-2"></i>Đánh dấu tất cả đã đọc
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('customer.notifications.clearRead') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa tất cả thông báo đã đọc?')">
                            <i class="fas fa-trash mr-2"></i>Xóa đã đọc
                        </button>
                    </form>
                </div>
            </div>

            <!-- Thông báo Flash -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- Badge thống kê -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $notifications->total() }}</div>
                            <div class="text-sm text-gray-600">Tổng số</div>
                        </div>
                        @if($unreadCount > 0)
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $unreadCount }}</div>
                            <div class="text-sm text-gray-600">Chưa đọc</div>
                        </div>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">
                        Cập nhật: {{ now()->format('H:i d/m/Y') }}
                    </div>
                </div>
            </div>

            <!-- Danh sách thông báo -->
            <div class="bg-white rounded-lg shadow-sm">
                @if($notifications->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($notifications as $notification)
                        <div class="p-6 hover:bg-gray-50 transition-colors {{ $notification->Status === 'Unread' ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="font-semibold text-gray-900 {{ $notification->Status === 'Unread' ? 'text-blue-900' : '' }}">
                                            {{ $notification->Title }}
                                        </h3>
                                        @if($notification->Status === 'Unread')
                                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">Mới</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-600 mb-3">{{ Str::limit($notification->Message, 200) }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $notification->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('customer.notifications.show', $notification->NotificationID) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Xem chi tiết
                                            </a>
                                            <form action="{{ route('customer.notifications.destroy', $notification->NotificationID) }}" 
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium ml-3"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?')">
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Phân trang -->
                    @if($notifications->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $notifications->links() }}
                    </div>
                    @endif

                @else
                    <!-- Empty state -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bell text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Không có thông báo</h3>
                        <p class="text-gray-600 mb-6">Bạn chưa có thông báo nào. Thông báo mới sẽ xuất hiện ở đây.</p>
                        <a href="{{ route('customer.home') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Quay về trang chủ
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Auto refresh unread count every 30 seconds
setInterval(function() {
    fetch('{{ route("customer.notifications.unreadCount") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update badge in navbar if exists
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            }
        });
}, 30000);
// Real-time notification updates
function updateNotificationBadge() {
    fetch('/customer/notifications/unread/count')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.querySelector('.notification-badge');
                if (data.count > 0) {
                    if (!badge) {
                        // Create badge if it doesn't exist
                        const button = document.querySelector('[x-data] button.relative');
                        if (button) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'absolute top-1 right-1 lg:top-2 lg:right-2 w-2 h-2 bg-red-500 rounded-full notification-badge';
                            button.appendChild(newBadge);
                        }
                    }
                    // Update count if you want to show number
                    // badge.textContent = data.count;
                } else {
                    // Remove badge if no unread notifications
                    if (badge) {
                        badge.remove();
                    }
                }
            }
        })
        .catch(error => console.error('Error updating notification badge:', error));
}

// Update every 30 seconds
setInterval(updateNotificationBadge, 30000);

// Also update when page becomes visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        updateNotificationBadge();
    }
});

// Update on page load
document.addEventListener('DOMContentLoaded', updateNotificationBadge);
</script>
@endsection