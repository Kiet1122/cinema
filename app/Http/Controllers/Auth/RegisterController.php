<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Hiển thị form đăng ký
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký user
     */
    public function register(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Xử lý upload avatar nếu có
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarFile = $request->file('avatar');
                $avatarName = time() . '_' . $avatarFile->getClientOriginalName();
                $avatarPath = $avatarFile->storeAs('avatars', $avatarName, 'public');
            }

            // Tạo user trong bảng user
            $user = User::create([
                'Email' => $request->email,
                'Password' => Hash::make($request->password),
                'Role' => 'Customer',
                'Status' => 'Active',
            ]);

            // Tạo customer trong bảng customer
            $customer = Customer::create([
                'UserID' => $user->UserID,
                'FullName' => $request->full_name,
                'Phone' => $request->phone,
                'Gender' => $request->gender,
                'DateOfBirth' => $request->date_of_birth,
                'Address' => $request->address,
                'Avatar' => $avatarPath,
            ]);

            DB::commit();

            // Tự động đăng nhập sau khi đăng ký
            Auth::login($user);

            // Chuyển hướng về trang chủ với thông báo thành công
            return redirect()->route('customer.home')
                ->with('success', 'Đăng ký tài khoản thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại.')
                ->withInput();
        }
    }

    /**
     * API đăng ký (cho ứng dụng mobile)
     */
    public function apiRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:Male,Female,Other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Tạo user
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'Customer',
                'status' => 'Active',
            ]);

            // Tạo customer
            $customer = Customer::create([
                'UserID' => $user->UserID,
                'FullName' => $request->full_name,
                'Phone' => $request->phone,
                'Gender' => $request->gender,
                'DateOfBirth' => $request->date_of_birth,
                'Address' => $request->address,
            ]);

            DB::commit();

            // Tạo token cho API
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => [
                    'user' => [
                        'id' => $user->UserID,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                    'customer' => [
                        'id' => $customer->CustomerID,
                        'full_name' => $customer->FullName,
                        'phone' => $customer->Phone,
                        'gender' => $customer->Gender,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình đăng ký',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra email đã tồn tại chưa (dùng cho AJAX)
     */
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'message' => 'Email không hợp lệ'
            ]);
        }

        $emailExists = User::where('email', $request->email)->exists();

        return response()->json([
            'valid' => !$emailExists,
            'message' => $emailExists ? 'Email đã được sử dụng' : 'Email có thể sử dụng'
        ]);
    }
}