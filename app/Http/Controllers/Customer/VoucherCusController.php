<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\CustomerVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherCusController extends Controller
{
    public function index()
    {
        $customer = Auth::user()->customer;
        
        $myVouchers = CustomerVoucher::with('voucher')
            ->where('CustomerID', $customer->CustomerID)
            ->where('IsUsed', 0)
            ->whereHas('voucher', function($query) {
                $query->where('Status', 'Active')
                      ->where('StartDate', '<=', now())
                      ->where('EndDate', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.voucher.list', compact('myVouchers'));
    }

    public function claim($voucherId)
    {
        $customer = Auth::user()->customer;
        
        // Kiểm tra voucher có tồn tại và hợp lệ không
        $voucher = Voucher::where('VoucherID', $voucherId)
            ->where('Status', 'Active')
            ->where('StartDate', '<=', now())
            ->where('EndDate', '>=', now())
            ->first();

        if (!$voucher) {
            return redirect()->back()->with('error', 'Voucher không khả dụng.');
        }

        // Kiểm tra xem customer đã claim voucher này chưa
        $existingClaim = CustomerVoucher::where('CustomerID', $customer->CustomerID)
            ->where('VoucherID', $voucherId)
            ->where('IsUsed', 0)
            ->first();

        if ($existingClaim) {
            return redirect()->back()->with('error', 'Bạn đã nhận voucher này rồi.');
        }

        // Tạo record CustomerVoucher
        CustomerVoucher::create([
            'CustomerID' => $customer->CustomerID,
            'VoucherID' => $voucherId,
            'IsUsed' => 0,
            'UsedAt' => null
        ]);

        return redirect()->back()->with('success', 'Nhận voucher thành công!');
    }
}