<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationCustomerController extends Controller
{
    /**
     * Hiển thị danh sách tất cả thông báo
     */
    public function index()
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return redirect()->route('customer.home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        // Lấy tất cả thông báo, phân trang
        $notifications = Notification::where('CustomerID', $customer->CustomerID)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Đếm số thông báo chưa đọc
        $unreadCount = Notification::where('CustomerID', $customer->CustomerID)
            ->where('Status', 'Unread')
            ->count();

        return view('customer.notification.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Hiển thị chi tiết một thông báo
     */
    public function show($id)
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return redirect()->route('customer.home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        // Tìm thông báo thuộc về khách hàng này
        $notification = Notification::where('NotificationID', $id)
            ->where('CustomerID', $customer->CustomerID)
            ->firstOrFail();

        // Đánh dấu là đã đọc nếu chưa đọc
        if ($notification->Status === 'Unread') {
            $notification->update([
                'Status' => 'Read'
            ]);
        }

        return view('customer.notification.show', compact('notification'));
    }

    /**
     * Đánh dấu một thông báo là đã đọc
     */
    public function markAsRead($id)
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin khách hàng.'
                ], 404);
            }
            return redirect()->route('customer.home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        try {
            $notification = Notification::where('NotificationID', $id)
                ->where('CustomerID', $customer->CustomerID)
                ->first();

            if (!$notification) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy thông báo.'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Không tìm thấy thông báo.');
            }

            $notification->update([
                'Status' => 'Read'
            ]);

            // Nếu là AJAX request, trả về JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã đánh dấu thông báo là đã đọc.',
                    'notification_id' => $notification->NotificationID
                ]);
            }

            // Nếu là normal request, redirect back
            return redirect()->back()->with('success', 'Đã đánh dấu thông báo là đã đọc.');

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function markAllAsRead()
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return redirect()->route('customer.home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        try {
            $updatedCount = Notification::where('CustomerID', $customer->CustomerID)
                ->where('Status', 'Unread')
                ->update(['Status' => 'Read']);

            return redirect()->back()->with('success', "Đã đánh dấu {$updatedCount} thông báo là đã đọc.");

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa một thông báo
     */
    public function destroy($id)
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return redirect()->route('customer.home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        try {
            $notification = Notification::where('NotificationID', $id)
                ->where('CustomerID', $customer->CustomerID)
                ->firstOrFail();

            $notification->delete();

            return redirect()->route('customer.notifications.index')
                ->with('success', 'Đã xóa thông báo thành công.');

        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa thông báo: ' . $e->getMessage());
        }
    }

    /**
     * Xóa tất cả thông báo đã đọc
     */
    public function clearRead()
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return redirect()->route('customer.home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        try {
            $deletedCount = Notification::where('CustomerID', $customer->CustomerID)
                ->where('Status', 'Read')
                ->delete();

            return redirect()->route('customer.notifications.index')
                ->with('success', "Đã xóa {$deletedCount} thông báo đã đọc.");

        } catch (\Exception $e) {
            Log::error('Error clearing read notifications: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa thông báo: ' . $e->getMessage());
        }
    }

    /**
     * Lấy số lượng thông báo chưa đọc (API)
     */
    public function getUnreadCount()
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }

        try {
            $unreadCount = Notification::where('CustomerID', $customer->CustomerID)
                ->where('Status', 'Unread')
                ->count();

            return response()->json([
                'success' => true,
                'count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Lấy thông báo gần đây (API)
     */
    public function getRecentNotifications()
    {
        $customer = Auth::user()->customer;
        
        if (!$customer) {
            return response()->json([
                'success' => false,
                'notifications' => []
            ]);
        }

        try {
            $notifications = Notification::where('CustomerID', $customer->CustomerID)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['NotificationID', 'Title', 'Message', 'Status', 'created_at']);

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recent notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'notifications' => []
            ]);
        }
    }
}