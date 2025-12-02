<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\CustomerVoucher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class VoucherController extends Controller
{
    /**
     * Chỉ cho Manager truy cập
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:manager']);
    }

    /**
     * Hiển thị danh sách Voucher với trạng thái tự động cập nhật
     */
    public function index()
    {
        // Cập nhật trạng thái voucher dựa trên ngày hiện tại
        $this->updateVoucherStatuses();

        $vouchers = Voucher::orderBy('created_at', 'desc')
            ->paginate(15);

        // Thống kê
        $totalVouchers = Voucher::count();
        $activeVouchers = Voucher::where('Status', 'Active')->count();
        $expiringSoonVouchers = $this->getExpiringSoonVouchersCount();

        return view('manager.voucher.index', compact(
            'vouchers',
            'totalVouchers',
            'activeVouchers',
            'expiringSoonVouchers'
        ));
    }

    /**
     * Tự động cập nhật trạng thái voucher dựa trên ngày hiện tại
     */
    private function updateVoucherStatuses()
    {
        $now = Carbon::now();

        // Cập nhật voucher đã hết hạn
        Voucher::where('EndDate', '<', $now)
            ->where('Status', 'Active')
            ->update(['Status' => 'Inactive']);

        // Cập nhật voucher chưa đến ngày bắt đầu
        Voucher::where('StartDate', '>', $now)
            ->where('Status', 'Active')
            ->update(['Status' => 'Inactive']);

        // Cập nhật voucher đang trong thời gian hiệu lực
        Voucher::where('StartDate', '<=', $now)
            ->where('EndDate', '>=', $now)
            ->where('Status', 'Inactive')
            ->update(['Status' => 'Active']);
    }

    /**
     * Đếm số voucher sắp hết hạn (trong vòng 7 ngày tới)
     */
    private function getExpiringSoonVouchersCount()
    {
        $now = Carbon::now();
        $sevenDaysLater = $now->copy()->addDays(7);

        return Voucher::where('EndDate', '>=', $now)
            ->where('EndDate', '<=', $sevenDaysLater)
            ->where('Status', 'Active')
            ->count();
    }

    /**
     * Hiển thị form tạo Voucher mới
     */
    public function create()
    {
        return view('manager.voucher.create');
    }

    /**
     * Lưu Voucher mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'Code' => 'required|unique:Voucher,Code',
            'DiscountType' => 'required|in:Percent,FixedAmount',
            'Value' => 'required|numeric|min:0',
            'StartDate' => 'required|date',
            'EndDate' => 'required|date|after_or_equal:StartDate',
            'Status' => 'required|in:Active,Inactive',
            'UsageLimit' => 'nullable|integer|min:0',
            'PerUserLimit' => 'nullable|integer|min:0',
        ]);

        $voucher = new Voucher();
        $voucher->Code = $request->Code;
        $voucher->DiscountType = $request->DiscountType;
        $voucher->Value = $request->Value;
        $voucher->StartDate = $request->StartDate;
        $voucher->EndDate = $request->EndDate;
        $voucher->Status = $request->Status;
        $voucher->UsageLimit = $request->UsageLimit;
        $voucher->PerUserLimit = $request->PerUserLimit;
        $voucher->ManagerID = auth()->user()->manager->ManagerID ?? null;
        $voucher->save();

        return redirect()->route('manager.vouchers.index')->with('success', 'Voucher đã được tạo thành công.');
    }

    /**
     * Hiển thị form chỉnh sửa Voucher
     */
    public function edit($id)
    {
        $voucher = Voucher::findOrFail($id);
        return view('manager.voucher.edit', compact('voucher'));
    }

    /**
     * Cập nhật Voucher
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $request->validate([
            'Code' => 'required|unique:Voucher,Code,' . $voucher->VoucherID . ',VoucherID',
            'DiscountType' => 'required|in:Percent,FixedAmount',
            'Value' => 'required|numeric|min:0',
            'StartDate' => 'required|date',
            'EndDate' => 'required|date|after_or_equal:StartDate',
            'Status' => 'required|in:Active,Inactive',
            'UsageLimit' => 'nullable|integer|min:0',
            'PerUserLimit' => 'nullable|integer|min:0',
        ]);

        $voucher->Code = $request->Code;
        $voucher->DiscountType = $request->DiscountType;
        $voucher->Value = $request->Value;
        $voucher->StartDate = $request->StartDate;
        $voucher->EndDate = $request->EndDate;
        $voucher->Status = $request->Status;
        $voucher->UsageLimit = $request->UsageLimit;
        $voucher->PerUserLimit = $request->PerUserLimit;
        $voucher->save();

        return redirect()->route('manager.vouchers.index')->with('success', 'Voucher đã được cập nhật thành công.');
    }

    /**
     * Xóa Voucher
     */
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('manager.vouchers.index')->with('success', 'Voucher đã được xóa thành công.');
    }

    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);

        // Đếm số lượng thành viên đã được tặng Voucher này
        $assignedCount = CustomerVoucher::where('VoucherID', $id)->count();

        // Truyền Voucher và số lượng đã tặng sang view
        return view('manager.voucher.show', compact('voucher', 'assignedCount'));
    }

    /**
     * Tặng một Voucher đã chọn cho TẤT CẢ các thành viên hiện có.
     */
    public function assignAllVouchers(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|exists:voucher,VoucherID',
        ], [
            'voucher_id.required' => 'Vui lòng chọn một Voucher để tặng.',
            'voucher_id.exists' => 'Voucher được chọn không hợp lệ.',
        ]);

        $voucherId = $request->input('voucher_id');

        $customerIds = Customer::pluck('CustomerID');

        if ($customerIds->isEmpty()) {
            return redirect()->route('manager.vouchers.show', $voucherId)
                ->with('error', 'Hiện không có thành viên nào trong hệ thống để tặng Voucher.');
        }

        $countAssigned = 0;

        DB::beginTransaction();
        try {
            $voucher = Voucher::find($voucherId);
            $voucherName = $voucher->Code ?? 'Voucher được chọn';

            foreach ($customerIds as $customerId) {
                $exists = CustomerVoucher::where('CustomerID', $customerId)
                    ->where('VoucherID', $voucherId)
                    ->exists();

                if (!$exists) {
                    CustomerVoucher::create([
                        'CustomerID' => $customerId,
                        'VoucherID' => $voucherId,
                        'IsUsed' => 0,
                        'AssignedDate' => now(),
                    ]);
                    $countAssigned++;
                }
            }

            DB::commit();

            if ($countAssigned > 0) {
                return redirect()->route('manager.vouchers.show', $voucherId)
                    ->with('success', "Đã tặng Voucher '{$voucherName}' thành công cho **{$countAssigned}** thành viên.");
            } else {
                return redirect()->route('manager.vouchers.show', $voucherId)
                    ->with('error', "Tất cả thành viên đã sở hữu Voucher '{$voucherName}'. Không có Voucher nào được tặng thêm.");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Mass Voucher Assignment Failed: " . $e->getMessage());

            return redirect()->route('manager.vouchers.show', $voucherId)
                ->with('error', 'Đã xảy ra lỗi hệ thống trong quá trình tặng Voucher hàng loạt. Vui lòng kiểm tra log.');
        }
    }
}