<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theater;
use Illuminate\Support\Facades\Auth;

class TheaterController extends Controller
{
    /**
     * Hiển thị danh sách các rạp chiếu phim.
     */
    public function index(Request $request)
    {
        $query = Theater::query();

        // Tìm kiếm theo tên - SỬA: dùng 'Name' thay vì 'name'
        if ($request->has('search') && $request->search != '') {
            $query->where('Name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo thành phố - SỬA: dùng 'City' thay vì 'city'
        if ($request->has('city') && $request->city != '') {
            $query->where('City', $request->city);
        }

        // Sắp xếp
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('Name', 'desc');
                break;
            case 'city_asc':
                $query->orderBy('City', 'asc');
                break;
            case 'city_desc':
                $query->orderBy('City', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default: // name_asc
                $query->orderBy('Name', 'asc');
        }

        $theaters = $query->get(); // Không phân trang

        return view('manager.theater.index', compact('theaters'));
    }

    /**
     * Hiển thị form thêm rạp.
     */
    public function create()
    {
        return view('Manager.theater.create');
    }

    /**
     * Lưu rạp mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Address' => 'required|string',
            'Phone' => 'nullable|string|max:20',
            'City' => 'nullable|string|max:100',
        ]);

        Theater::create([
            'Name' => $request->Name,
            'Address' => $request->Address,
            'Phone' => $request->Phone,
            'City' => $request->City,
            'ManagerID' => Auth::user()->manager->ManagerID ?? null,
        ]);

        return redirect()->route('manager.theaters.index')->with('success', 'Thêm rạp thành công!');
    }

    /**
     * Hiển thị form sửa rạp.
     */
    public function edit($id)
    {
        $theater = Theater::findOrFail($id);
        return view('Manager.theater.edit', compact('theater'));
    }

    /**
     * Cập nhật rạp.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Name' => 'required|string|max:255',
            'Address' => 'required|string',
            'Phone' => 'nullable|string|max:20',
            'City' => 'nullable|string|max:100',
        ]);

        $theater = Theater::findOrFail($id);
        $theater->update($request->all());

        return redirect()->route('manager.theaters.index')->with('success', 'Cập nhật rạp thành công!');
    }

    /**
     * Xóa rạp.
     */
    public function destroy($id)
    {
        $theater = Theater::findOrFail($id);
        $theater->delete();

        return redirect()->route('manager.theaters.index')->with('success', 'Xóa rạp thành công!');
    }
}
