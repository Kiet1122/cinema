<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Customer;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Hiển thị form để Manager soạn và gửi thông báo
     */
    public function index()
    {
        // Lấy danh sách khách hàng từ bảng customer
        $customers = Customer::with('user')->get();
        
        // Đếm tổng số khách hàng
        $totalCustomers = $customers->count();

        return view('manager.notification.index', compact('customers', 'totalCustomers'));
    }

    /**
     * Xử lý việc gửi thông báo đến người dùng
     */
    public function sendNotification(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'customer_id' => 'required'
        ]);

        try {
            $title = $request->input('title');
            $content = $request->input('content');
            $customerId = $request->input('customer_id');

            // Xác định đối tượng nhận
            if ($customerId === 'all') {
                // Gửi đến tất cả khách hàng
                $customers = Customer::all();
                $sentCount = 0;

                foreach ($customers as $customer) {
                    // Lưu thông báo vào database
                    Notification::create([
                        'CustomerID' => $customer->CustomerID,
                        'Title' => $title,
                        'Message' => $content,
                        'Status' => 'Unread',
                        'created_at' => now()
                    ]);
                    $sentCount++;
                }

                $message = "Đã gửi thông báo đến {$sentCount} khách hàng";
                
            } else {
                // Gửi đến khách hàng cụ thể
                $customer = Customer::find($customerId);
                
                if (!$customer) {
                    return redirect()->back()->with('error', 'Không tìm thấy khách hàng được chọn.');
                }

                // Lưu thông báo vào database
                Notification::create([
                    'CustomerID' => $customerId,
                    'Title' => $title,
                    'Message' => $content,
                    'Status' => 'Unread',
                    'created_at' => now()
                ]);

                $message = "Đã gửi thông báo đến {$customer->FullName}";
            }

            // Ghi log
            Log::info('Notification sent by Manager: ' . auth()->id(), [
                'title' => $title,
                'recipients' => $customerId === 'all' ? 'all_customers' : 'specific_customer',
                'sent_count' => $customerId === 'all' ? $sentCount : 1
            ]);

            // Chuyển hướng về trang index với thông báo thành công
            return redirect()->route('manager.notification.index')->with('success', $message);

        } catch (\Exception $e) {
            // Xử lý lỗi
            Log::error('Error sending notification: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi thông báo: ' . $e->getMessage());
        }
    }

    /**
     * API để gửi thông báo (nếu cần dùng AJAX)
     */
    public function sendNotificationApi(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'customer_id' => 'required'
        ]);

        try {
            $title = $request->input('title');
            $content = $request->input('content');
            $customerId = $request->input('customer_id');

            if ($customerId === 'all') {
                $customers = Customer::all();
                $sentCount = 0;

                foreach ($customers as $customer) {
                    Notification::create([
                        'CustomerID' => $customer->CustomerID,
                        'Title' => $title,
                        'Message' => $content,
                        'Status' => 'Unread',
                        'created_at' => now()
                    ]);
                    $sentCount++;
                }

                return response()->json([
                    'success' => true,
                    'message' => "Đã gửi thông báo đến {$sentCount} khách hàng"
                ]);
                
            } else {
                $customer = Customer::find($customerId);
                
                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy khách hàng được chọn.'
                    ], 404);
                }

                Notification::create([
                    'CustomerID' => $customerId,
                    'Title' => $title,
                    'Message' => $content,
                    'Status' => 'Unread',
                    'created_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Đã gửi thông báo đến {$customer->FullName}"
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi gửi thông báo: ' . $e->getMessage()
            ], 500);
        }
    }
}