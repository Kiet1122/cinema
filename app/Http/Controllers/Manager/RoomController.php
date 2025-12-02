<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Theater;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Hiển thị danh sách phòng chiếu.
     */
    public function index()
    {
        $rooms = Room::with('theater')->orderBy('RoomID', 'desc')->paginate(200);
        return view('Manager.room.index', compact('rooms'));
    }

    /**
     * Form thêm phòng chiếu.
     */
    public function create()
    {
        $theaters = Theater::all();
        return view('Manager.room.create', compact('theaters'));
    }

    /**
     * Lưu phòng chiếu mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'RoomName' => 'required|max:50',
            'Capacity' => 'required|integer|min:1',
            'RoomType' => 'required|in:2D,3D,IMAX',
            'TheaterID' => 'required|exists:Theater,TheaterID',
        ]);

        Room::create($request->all());

        return redirect()->route('manager.rooms.index')->with('success', 'Thêm phòng chiếu thành công!');
    }

    /**
     * Form chỉnh sửa phòng chiếu.
     */
    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $theaters = Theater::all();
        return view('Manager.room.edit', compact('room', 'theaters'));
    }

    /**
     * Cập nhật phòng chiếu.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'RoomName' => 'required|max:50',
            'Capacity' => 'required|integer|min:1',
            'RoomType' => 'required|in:2D,3D,IMAX',
            'TheaterID' => 'required|exists:Theater,TheaterID',
        ]);

        $room = Room::findOrFail($id);
        $room->update($request->all());

        return redirect()->route('manager.rooms.index')->with('success', 'Cập nhật phòng chiếu thành công!');
    }

    /**
     * Xóa phòng chiếu.
     */
    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('manager.rooms.index')->with('success', 'Xóa phòng chiếu thành công!');
    }
}
