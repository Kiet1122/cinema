<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Manager;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Lấy đối tượng User và đối tượng Manager đang đăng nhập.
     * @return array|null ['user' => User, 'manager' => Manager]
     */
    private function getAuthEntities()
    {
        // Sử dụng auth()->user() để lấy đối tượng User đang đăng nhập
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        // Lấy thông tin chi tiết của Manager từ bảng managers
        $manager = Manager::where('UserID', $user->UserID)->first();

        if (!$manager) {
            // Trường hợp Manager không tồn tại trong bảng Managers (lỗi dữ liệu)
            return null;
        }

        return compact('user', 'manager');
    }

    /**
     * Hiển thị trang hồ sơ của Manager
     */
    public function index()
    {
        $entities = $this->getAuthEntities();

        if (!$entities) {
            // Nếu không có user hoặc manager, chuyển hướng hoặc trả về lỗi
            return redirect()->route('manager.login')->with('error', 'Vui lòng đăng nhập lại.');
        }

        extract($entities); // Giải nén $user và $manager

        return view('manager.profile.index', compact('user', 'manager'));
    }

    /**
     * Cập nhật thông tin cá nhân (CHỈ sử dụng LINK cho Avatar)
     */
    public function update(Request $request)
    {
        $entities = $this->getAuthEntities();
        if (!$entities) {
            return redirect()->route('manager.login');
        }
        extract($entities); // $user, $manager

        // --- 1. VALIDATION Đã Đơn Giản Hóa ---
        $validator = Validator::make($request->all(), [
            'FullName' => 'required|string|max:255',
            'Phone' => 'nullable|string|max:20',
            'Avatar_Link' => 'nullable|url|max:1000', // Tăng max length cho URL dài
        ], [
            'FullName.required' => 'Họ và tên là bắt buộc.',
            'FullName.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'Phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'Avatar_Link.url' => 'Link ảnh đại diện phải là một URL hợp lệ.',
            'Avatar_Link.max' => 'Link ảnh đại diện quá dài.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // --- 2. CẬP NHẬT THÔNG TIN CƠ BẢN ---
        $manager->FullName = $request->FullName;
        $manager->Phone = $request->Phone;

        // --- 3. XỬ LÝ AVATAR (CHỈ LƯU URL) ---

        // Kiểm tra xem có URL avatar mới không
        if ($request->filled('Avatar_Link')) {
            $avatarUrl = $request->Avatar_Link;

            // Kiểm tra URL hợp lệ và hỗ trợ các định dạng ảnh phổ biến
            if ($this->isValidImageUrl($avatarUrl)) {
                $manager->Avatar = $avatarUrl;
            } else {
                return redirect()->back()
                    ->withErrors(['Avatar_Link' => 'Link ảnh không hợp lệ hoặc không hỗ trợ định dạng ảnh.'])
                    ->withInput();
            }
        } else {
            // Nếu không có link mới, giữ nguyên avatar hiện tại
            // Hoặc có thể set về null nếu muốn xóa ảnh
            // $manager->Avatar = null;
        }

        // --- 4. LƯU VÀ CHUYỂN HƯỚNG ---
        $manager->save();

        return redirect()->back()->with('success', 'Cập nhật hồ sơ thành công!');
    }

    /**
     * Kiểm tra URL ảnh có hợp lệ không
     */
    private function isValidImageUrl($url)
    {
        // Kiểm tra định dạng URL cơ bản
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Kiểm tra phần mở rộng file ảnh phổ biến
        $path = parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];

        if (!in_array($extension, $allowedExtensions)) {
            // Nếu không có extension, vẫn chấp nhận (có thể là dynamic URL)
            // Hoặc có thể kiểm tra content-type nếu muốn chặt chẽ hơn
            return true; // Tạm thời chấp nhận để linh hoạt với các URL không có extension
        }

        return true;
    }

    /**
     * Kiểm tra URL ảnh có tồn tại và là ảnh thật không (tùy chọn)
     */
    private function verifyImageUrl($url)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->head($url, [
                'timeout' => 5,
                'verify' => false // Tắt SSL verify cho development
            ]);

            $contentType = $response->getHeaderLine('Content-Type');

            // Kiểm tra content-type có phải là ảnh không
            return strpos($contentType, 'image/') === 0;

        } catch (\Exception $e) {
            // Nếu không thể kiểm tra, vẫn chấp nhận URL
            return true;
        }
    }
}