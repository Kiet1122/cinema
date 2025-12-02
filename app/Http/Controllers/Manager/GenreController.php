<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Genre; // Import Model Genre
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GenreController extends Controller
{
    /**
     * 1. Hiển thị danh sách tất cả các thể loại.
     * GET /manager/genres
     */
    public function index()
    {
        // Lấy tất cả thể loại, sắp xếp theo tên
        $genres = Genre::orderBy('GenreName')->get();
        
        // Trả về view để hiển thị danh sách
        return view('manager.genre.index', compact('genres'));
    }

    /**
     * 2. Hiển thị form tạo thể loại mới.
     * GET /manager/genres/create
     */
    public function create()
    {
        return view('manager.genre.create');
    }

    /**
     * 3. Lưu thể loại mới vào cơ sở dữ liệu.
     * POST /manager/genres
     */
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $validated = $request->validate([
            // Tên thể loại là bắt buộc, tối đa 100 ký tự và phải là duy nhất trong bảng 'Genre'
            'GenreName' => 'required|string|max:100|unique:Genre,GenreName',
            'Description' => 'nullable|string',
        ]);

        // 2. Tạo thể loại mới
        Genre::create($validated);

        // 3. Chuyển hướng về trang danh sách với thông báo thành công
        return redirect()->route('manager.genre.index')
                         ->with('success', 'Thể loại "' . $validated['GenreName'] . '" đã được tạo thành công.');
    }

    /**
     * 4. Hiển thị form chỉnh sửa thể loại.
     * GET /manager/genres/{genre}
     */
    public function edit(Genre $genre)
    {
        // Tên tham số {genre} phải khớp với tham số trong route và Laravel tự động inject Model (Route Model Binding)
        return view('manager.genre.edit', compact('genre'));
    }

    /**
     * 5. Cập nhật thể loại trong cơ sở dữ liệu.
     * PUT/PATCH /manager/genres/{genre}
     */
    public function update(Request $request, Genre $genre)
    {
        // 1. Validate dữ liệu
        $validated = $request->validate([
            // Đảm bảo tên thể loại là duy nhất, ngoại trừ bản ghi hiện tại
            'GenreName' => [
                'required',
                'string',
                'max:100',
                Rule::unique('Genre', 'GenreName')->ignore($genre->GenreID, 'GenreID'),
            ],
            'Description' => 'nullable|string',
        ]);
        
        // 2. Cập nhật
        $genre->update($validated);

        // 3. Chuyển hướng
        return redirect()->route('manager.genre.index')
                         ->with('success', 'Thể loại "' . $validated['GenreName'] . '" đã được cập nhật thành công.');
    }

    /**
     * 6. Xóa một thể loại khỏi cơ sở dữ liệu.
     * DELETE /manager/genres/{genre}
     */
    public function destroy(Genre $genre)
    {
        $genreName = $genre->GenreName;
        
        // Xóa thể loại. Nhờ các ràng buộc FOREIGN KEY ON DELETE CASCADE
        // trong CSDL (bảng MovieGenre), các liên kết với phim sẽ tự động bị xóa.
        $genre->delete();

        // Chuyển hướng
        return redirect()->route('manager.genre.index')
                         ->with('success', 'Thể loại "' . $genreName . '" và tất cả liên kết liên quan đã được xóa.');
    }
}