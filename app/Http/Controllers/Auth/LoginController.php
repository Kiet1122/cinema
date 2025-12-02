<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
class LoginController extends Controller
{
    /**
     * Khởi tạo controller với middleware guest.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Hiển thị form đăng nhập.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý yêu cầu đăng nhập.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // 1. Xác thực dữ liệu đầu vào
        $credentials = $request->validate([
            'Email' => 'required|email|max:255',
            'password' => 'required|min:6', // Tên trường mật khẩu phải là 'password' (chữ thường)
        ]);

        // 2. Cố gắng xác thực người dùng
        // Auth::attempt() sẽ tự động tìm trường 'password' và so sánh mật khẩu đã hash.
        // Dòng này đã được sửa lại cho đúng.
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            // 3. Kiểm tra trạng thái người dùng
            if ($user->Status !== 'Active') {
                Auth::logout();
                return back()->withInput()->with('status', 'Tài khoản của bạn không hoạt động hoặc đã bị cấm.');
            }

            // 4. Kiểm tra vai trò và chuyển hướng
            if ($user->Role === 'Manager') {
                return redirect()->intended('/manager/dashboard');
            } elseif ($user->Role === 'Customer') {
                return redirect()->intended('/customer/home');
            }

        }

        // 5. Nếu xác thực thất bại
        return back()->withInput()->with('status', 'Email hoặc mật khẩu không chính xác.');
    }

    /**
     * Xử lý yêu cầu đăng xuất.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}