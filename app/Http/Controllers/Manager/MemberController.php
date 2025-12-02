<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use App\Models\Voucher;
use App\Models\CustomerVoucher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /**
     * Chỉ cho Manager truy cập
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:manager']);
    }

    // --- CRUD METHODS ---

    /**
     * Hiển thị danh sách Customer
     */
    public function index()
    {
        $vouchers = Voucher::where('Status', 'Active')->get();
        $customers = Customer::with('user')->orderBy('CustomerID', 'asc')->get();
        return view('manager.member.index', compact('customers', 'vouchers'));
    }

    /**
     * Hiển thị form tạo Customer mới
     */
    public function create()
    {
        return view('manager.member.create');
    }

    /**
     * Lưu Customer mới vào CSDL
     */
    public function store(Request $request)
    {
        $request->validate([
            'Email' => 'required|email|unique:User,Email',
            'Password' => 'required|min:6',
            'FullName' => 'required|string|max:255',
            'Phone' => 'nullable|string|max:20',
            'DateOfBirth' => 'nullable|date',
            'Gender' => 'nullable|in:Male,Female,Other',
            'Address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // 1. Tạo bản ghi User (Tài khoản)
            $user = User::create([
                'Email' => $request->Email,
                'Password' => Hash::make($request->Password),
                'Role' => 'Customer',
                'Status' => 'Active',
            ]);

            // 2. Tạo bản ghi Customer (Thông tin cá nhân)
            Customer::create([
                'UserID' => $user->UserID,
                'FullName' => $request->FullName,
                'Phone' => $request->Phone,
                'Gender' => $request->Gender,
                'DateOfBirth' => $request->DateOfBirth,
                'Address' => $request->Address,
            ]);

            DB::commit();

            return redirect()->route('manager.member.index')
                ->with('success', 'Thành viên mới đã được tạo thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Lỗi khi tạo thành viên: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form chỉnh sửa thông tin Customer
     */
    public function edit($id)
    {
        // Chỉ fetch Customer và User (Không fetch Voucher để tối ưu sau khi xóa khỏi view edit)
        $customer = Customer::with('user')->findOrFail($id);

        return view('manager.member.edit', compact('customer'));
    }

    /**
     * Cập nhật thông tin Customer đã chọn
     * Lưu ý: Đã loại bỏ khả năng cập nhật Email.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $user = $customer->user;

        $request->validate([
            'FullName' => 'required|string|max:255',
            'Phone' => 'nullable|string|max:20',
            'DateOfBirth' => 'nullable|date',
            'Gender' => 'nullable|in:Male,Female,Other',
            'Address' => 'nullable|string|max:500',
            'Password' => 'nullable|min:6',
            'Status' => [
                // 'required_with:UserID', // Bỏ vì UserID luôn có trong trường hợp này nếu customer->user tồn tại
                Rule::in(['Active', 'Inactive', 'Banned'])
            ],
        ]);

        DB::beginTransaction();

        try {
            // 1. Cập nhật bản ghi User (chỉ khi user tồn tại)
            if ($user) {
                // Status
                $user->Status = $request->Status;

                // Password (chỉ cập nhật nếu có nhập)
                if ($request->filled('Password')) {
                    $user->Password = Hash::make($request->Password);
                }
                // Email được giữ nguyên/bảo vệ, không update ở đây
                $user->save();
            }

            // 2. Cập nhật bản ghi Customer
            $customer->update([
                'FullName' => $request->FullName,
                'Phone' => $request->Phone,
                'Gender' => $request->Gender,
                'DateOfBirth' => $request->DateOfBirth,
                'Address' => $request->Address,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Thông tin thành viên đã được cập nhật.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Lỗi khi cập nhật thành viên: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa Customer đã chọn
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        DB::beginTransaction();

        try {
            // Lấy UserID trước khi xóa Customer
            $userId = $customer->UserID;

            // 1. Xóa bản ghi Customer
            $customer->delete();

            // 2. Xóa bản ghi User liên quan
            User::where('UserID', $userId)->delete();

            DB::commit();

            return redirect()->route('manager.member.index')
                ->with('success', 'Thành viên đã được xóa thành công.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Lỗi khi xóa thành viên: ' . $e->getMessage()]);
        }
    }

    /**
     * Xem chi tiết 1 Customer (tùy chọn) - CÓ THÊM DANH SÁCH VOUCHER KHẢ DỤNG
     */
    /**
     * Hiển thị chi tiết khách hàng và danh sách voucher khả dụng
     */
    public function show($id)
    {
        // Tải thông tin liên quan: User, Voucher của khách hàng (cùng thông tin voucher)
        $customer = Customer::with('user', 'customerVouchers.voucher')->findOrFail($id);

        // Lấy danh sách voucher đang hoạt động để có thể tặng
        $availableVouchers = Voucher::where('Status', 'Active')->get();

        return view('manager.member.show', compact('customer', 'availableVouchers'));
    }

    // --- VOUCHER MANAGEMENT METHODS ---

    /**
     * Đồng bộ Voucher cho Customer (Tặng/Gỡ nhiều voucher chưa dùng)
     * Route: manager.member.assign_voucher
     */
    public function assignVoucher(Request $request, $customerId)
    {
        $request->validate([
            // Cho phép mảng ID voucher hoặc null nếu không tích gì
            'voucher_ids' => 'nullable|array',
            // Đảm bảo mỗi ID voucher tồn tại
            'voucher_ids.*' => 'exists:Voucher,VoucherID',
        ]);

        $customer = Customer::findOrFail($customerId);

        // 1. Lấy danh sách Voucher ID được yêu cầu (tích trong form)
        $requestedVoucherIds = $request->input('voucher_ids', []);

        // 2. Lấy danh sách ID các voucher khách hàng đang sở hữu và CHƯA DÙNG
        $currentUnusedVoucherIds = $customer->customerVouchers()
            ->where('IsUsed', 0)
            ->pluck('VoucherID')
            ->toArray();

        // 3. Vouchers cần ADD (Có trong yêu cầu, nhưng chưa có trong danh sách chưa dùng hiện tại)
        $vouchersToAdd = array_diff($requestedVoucherIds, $currentUnusedVoucherIds);

        // 4. Vouchers cần REMOVE (Có trong danh sách chưa dùng hiện tại, nhưng KHÔNG CÓ trong yêu cầu)
        $vouchersToRemove = array_diff($currentUnusedVoucherIds, $requestedVoucherIds);

        $addedCount = 0;
        $removedCount = 0;

        DB::transaction(function () use ($customerId, $vouchersToAdd, $vouchersToRemove, &$addedCount, &$removedCount) {

            // Thêm mới (Insert)
            if (!empty($vouchersToAdd)) {
                $data = [];
                foreach ($vouchersToAdd as $voucherId) {
                    $data[] = [
                        'CustomerID' => $customerId,
                        'VoucherID' => $voucherId,
                        'IsUsed' => 0,

                    ];
                }
                CustomerVoucher::insert($data);
                $addedCount = count($vouchersToAdd);
            }

            // Thu hồi/Gỡ bỏ (Delete) - Chỉ xóa những voucher CHƯA DÙNG
            if (!empty($vouchersToRemove)) {
                $removedCount = CustomerVoucher::where('CustomerID', $customerId)
                    ->whereIn('VoucherID', $vouchersToRemove)
                    ->where('IsUsed', 0) // Rất quan trọng: Chỉ xóa những voucher chưa dùng
                    ->delete();
            }
        });

        // Chuẩn bị thông báo dựa trên kết quả
        $message = "Đã cập nhật trạng thái voucher thành công.";
        if ($addedCount === 0 && $removedCount === 0) {
            $message = "Không có thay đổi nào về voucher được áp dụng.";
        } elseif ($addedCount > 0 && $removedCount === 0) {
            $message = "Đã tặng thành công " . $addedCount . " voucher mới.";
        } elseif ($addedCount === 0 && $removedCount > 0) {
            $message = "Đã gỡ thành công " . $removedCount . " voucher chưa dùng.";
        } else {
            $message = "Đã tặng " . $addedCount . " voucher và gỡ " . $removedCount . " voucher chưa dùng.";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Thu hồi/Xóa Voucher khỏi Customer (Route: manager.member.revoke_voucher)
     * Phương thức này vẫn giữ lại để xử lý nút "Thu hồi" riêng lẻ.
     */
    public function revokeVoucher($customerId, $customerVoucherId)
    {
        // Tìm bản ghi CustomerVoucher theo cả CustomerID và CustomerVoucherID để đảm bảo bảo mật
        $customerVoucher = CustomerVoucher::with('voucher', 'customer')
            ->where('CustomerVoucherID', $customerVoucherId)
            ->where('CustomerID', $customerId)
            ->firstOrFail();

        if ($customerVoucher->IsUsed) {
            return redirect()->back()->withErrors(['error' => 'Không thể thu hồi voucher đã được sử dụng.']);
        }

        $voucherCode = $customerVoucher->voucher->Code ?? 'này';
        $customerName = $customerVoucher->customer->FullName ?? 'Thành viên';

        $customerVoucher->delete();

        return redirect()->back()->with('success', "Đã thu hồi thành công voucher {$voucherCode} khỏi {$customerName}.");
    }
}
