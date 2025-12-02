<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileCusController extends Controller
{
    public function updateProfile(Request $request)
    {
        $customer = Auth::user()->customer;

        $validator = Validator::make($request->all(), [
            'FullName' => 'required|string|max:255',
            'Phone' => 'nullable|string|max:20',
            'Gender' => 'nullable|in:Male,Female,Other',
            'DateOfBirth' => 'nullable|date',
            'Address' => 'nullable|string|max:500',
            'AvatarURL' => 'nullable|url',
            'AvatarFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'FullName' => $request->FullName,
            'Phone' => $request->Phone,
            'Gender' => $request->Gender,
            'Address' => $request->Address,
        ];

        // Xử lý ngày sinh
        if ($request->filled('DateOfBirth')) {
            $data['DateOfBirth'] = \Carbon\Carbon::parse($request->DateOfBirth)->format('Y-m-d');
        } else {
            $data['DateOfBirth'] = null;
        }

        // Xử lý Avatar: Ưu tiên URL trước, sau đó mới đến file upload
        if ($request->filled('AvatarURL')) {
            // Sử dụng URL ảnh
            $data['Avatar'] = $request->AvatarURL;
        } elseif ($request->hasFile('AvatarFile')) {
            // Upload file ảnh
            if ($customer->Avatar && Storage::exists('public/avatars/' . $customer->Avatar)) {
                Storage::delete('public/avatars/' . $customer->Avatar);
            }

            $avatarFile = $request->file('AvatarFile');
            $avatarName = time() . '_' . $avatarFile->getClientOriginalName();
            $avatarFile->storeAs('public/avatars', $avatarName);
            $data['Avatar'] = $avatarName;
        }
        // Nếu không có cả URL và File, giữ nguyên avatar hiện tại

        $customer->update($data);

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function index()
    {
        $customer = Auth::user()->customer;

        // Debug để xem dữ liệu thực tế
        \Log::info('Customer data:', [
            'DateOfBirth' => $customer->DateOfBirth,
            'DateOfBirth type' => gettype($customer->DateOfBirth),
            'Formatted DateOfBirth' => $customer->DateOfBirth ? \Carbon\Carbon::parse($customer->DateOfBirth)->format('Y-m-d') : 'null'
        ]);

        return view('customer.profile.index', compact('customer'));
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->Password)) {
            return redirect()->back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'Password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:User,Email,' . Auth::id() . ',UserID',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Xác thực mật khẩu
        if (!Hash::check($request->password, $user->Password)) {
            return redirect()->back()->with('error', 'Mật khẩu không đúng.');
        }

        $user->update([
            'Email' => $request->email
        ]);

        return redirect()->back()->with('success', 'Cập nhật email thành công!');
    }

    public function removeAvatar()
    {
        $customer = Auth::user()->customer;

        if ($customer->Avatar && Storage::exists('public/avatars/' . $customer->Avatar)) {
            Storage::delete('public/avatars/' . $customer->Avatar);
        }

        $customer->update([
            'Avatar' => null
        ]);

        return redirect()->back()->with('success', 'Xóa ảnh đại diện thành công!');
    }
}