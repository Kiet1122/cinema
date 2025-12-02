<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use Symfony\Component\HttpFoundation\Response;

class ShareNotificationData
{
    public function handle(Request $request, Closure $next): Response
    {
        $unreadCount = 0;
        $recentNotifications = collect();

        if (Auth::check() && Auth::user()->customer) {
            try {
                $customer = Auth::user()->customer;
                
                // Chỉ đếm thông báo chưa đọc
                $unreadCount = Notification::where('CustomerID', $customer->CustomerID)
                    ->where('Status', 'Unread')
                    ->count();

                // Chỉ lấy thông báo chưa đọc (tối đa 5 cái)
                $recentNotifications = Notification::where('CustomerID', $customer->CustomerID)
                    ->where('Status', 'Unread')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();

            } catch (\Exception $e) {
                \Log::error('Error sharing notification data: ' . $e->getMessage());
            }
        }

        // Share với tất cả views
        view()->share('unreadCount', $unreadCount);
        view()->share('recentNotifications', $recentNotifications);

        return $next($request);
    }
}