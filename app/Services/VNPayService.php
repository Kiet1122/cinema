<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VNPayService
{
    private $tmnCode;
    private $hashSecret;
    private $url;
    private $returnUrl;
    
    public function __construct()
    {
        $this->tmnCode = config('vnpay.tmn_code', 'DEMO');
        $this->hashSecret = config('vnpay.hash_secret', 'DEMO');
        $this->url = config('vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->returnUrl = config('vnpay.return_url', url('/payment/vnpay/return'));
    }
    
    /**
     * Tạo URL thanh toán VNPAY
     */
    public function createPaymentUrl($bookingId, $amount, $orderInfo = '', $bankCode = '')
    {
        // Lấy thông tin booking
        $booking = Booking::find($bookingId);
        if (!$booking) {
            throw new \Exception('Booking not found');
        }
        
        // Tạo mã giao dịch
        $txnRef = 'BOOK-' . $bookingId . '-' . time();
        
        // Lưu thông tin vào session
        Session::put('vnpay_pending', [
            'booking_id' => $bookingId,
            'amount' => $amount,
            'txn_ref' => $txnRef,
            'order_info' => $orderInfo,
            'created_at' => now()
        ]);
        
        // Tạo các tham số VNPAY
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $this->tmnCode,
            "vnp_Amount" => $amount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $orderInfo ?: "Thanh toán đặt vé #{$bookingId}",
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $this->returnUrl,
            "vnp_TxnRef" => $txnRef,
            "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes'))
        ];
        
        if (!empty($bankCode)) {
            $inputData['vnp_BankCode'] = $bankCode;
        }
        
        // Sắp xếp và tạo chữ ký
        ksort($inputData);
        $hashData = $this->buildHashData($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        
        // Tạo URL
        $query = http_build_query($inputData);
        $paymentUrl = $this->url . "?" . $query . "&vnp_SecureHash=" . $secureHash;
        
        Log::info('VNPay URL created', [
            'booking_id' => $bookingId,
            'amount' => $amount,
            'txn_ref' => $txnRef
        ]);
        
        return $paymentUrl;
    }
    
    /**
     * Xác thực kết quả thanh toán
     */
    public function validatePayment($requestData)
    {
        // Lấy chữ ký từ request
        $vnpSecureHash = $requestData['vnp_SecureHash'] ?? '';
        unset($requestData['vnp_SecureHash']);
        
        // Sắp xếp lại dữ liệu
        ksort($requestData);
        $hashData = $this->buildHashData($requestData);
        
        // Tạo chữ ký để so sánh
        $secureHash = hash_hmac('sha512', $hashData, $this->hashSecret);
        
        return $secureHash === $vnpSecureHash;
    }
    
    /**
     * Xử lý kết quả thanh toán
     */
    public function processPaymentResult($requestData)
    {
        if (!$this->validatePayment($requestData)) {
            return [
                'success' => false,
                'message' => 'Invalid signature',
                'code' => '97'
            ];
        }
        
        $responseCode = $requestData['vnp_ResponseCode'] ?? '';
        $transactionStatus = $requestData['vnp_TransactionStatus'] ?? '';
        $txnRef = $requestData['vnp_TxnRef'] ?? '';
        
        // Lấy booking_id từ txnRef (format: BOOK-{booking_id}-{timestamp})
        preg_match('/BOOK-(\d+)-/', $txnRef, $matches);
        $bookingId = $matches[1] ?? null;
        
        if (!$bookingId) {
            return [
                'success' => false,
                'message' => 'Invalid transaction reference',
                'code' => '99'
            ];
        }
        
        // Kiểm tra mã phản hồi
        if ($responseCode == '00' && $transactionStatus == '00') {
            return [
                'success' => true,
                'message' => 'Payment successful',
                'code' => '00',
                'booking_id' => $bookingId,
                'transaction_no' => $requestData['vnp_TransactionNo'] ?? '',
                'amount' => ($requestData['vnp_Amount'] ?? 0) / 100,
                'bank_code' => $requestData['vnp_BankCode'] ?? '',
                'pay_date' => $requestData['vnp_PayDate'] ?? '',
                'raw_data' => $requestData
            ];
        } else {
            return [
                'success' => false,
                'message' => $this->getResponseMessage($responseCode),
                'code' => $responseCode,
                'booking_id' => $bookingId,
                'raw_data' => $requestData
            ];
        }
    }
    
    /**
     * Build hash data string
     */
    private function buildHashData($data)
    {
        $hashData = "";
        $i = 0;
        
        foreach ($data as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        return $hashData;
    }
    
    /**
     * Get response message by code
     */
    private function getResponseMessage($code)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công nhưng giao dịch bị nghi ngờ',
            '09' => 'Thẻ/Tài khoản chưa đăng ký Internet Banking',
            '10' => 'Xác thực thông tin không đúng quá 3 lần',
            '11' => 'Hết hạn chờ thanh toán',
            '12' => 'Thẻ/Tài khoản bị khóa',
            '13' => 'Sai mật khẩu xác thực (OTP)',
            '24' => 'Khách hàng hủy giao dịch',
            '51' => 'Tài khoản không đủ số dư',
            '65' => 'Tài khoản vượt hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng đang bảo trì',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định',
            '99' => 'Lỗi không xác định'
        ];
        
        return $messages[$code] ?? "Mã lỗi không xác định: {$code}";
    }
}