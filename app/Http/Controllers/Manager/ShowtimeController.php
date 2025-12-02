<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Room;
use Carbon\Carbon;
use App\Models\Theater;

class ShowtimeController extends Controller
{
    // Danh sách suất chiếu
    public function index(Request $request)
    {
        $now = Carbon::now();

        // Lấy tất cả suất chiếu kèm phim, phòng, rạp
        $showtimes = Showtime::with(['movie', 'room.theater'])
            ->orderBy('StartTime', 'asc')
            ->get();

        // Cập nhật trạng thái động
        $showtimes->transform(function ($showtime) use ($now) {
            if ($showtime->Status !== 'Cancelled') {
                $start = Carbon::parse($showtime->StartTime);
                $end = Carbon::parse($showtime->EndTime);

                if ($now->lt($start)) {
                    $showtime->Status = 'Scheduled';
                } elseif ($now->between($start, $end)) {
                    $showtime->Status = 'Showing';
                } else {
                    $showtime->Status = 'Finished';
                }
            }
            return $showtime;
        });

        // Phân trang thủ công
        $perPage = 10;
        $page = $request->input('page', 1);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $showtimes->forPage($page, $perPage),
            $showtimes->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $theaters = Theater::all();

        return view('manager.showtime.index', [
            'showtimes' => $paginated,
            'theaters' => $theaters,
        ]);
    }






    // Form thêm suất chiếu
    public function create()
    {
        $movies = Movie::all();
        $rooms = Room::with('theater')->get();
        return view('manager.showtime.create', compact('movies', 'rooms'));
    }

    // Lưu suất chiếu mới
    public function store(Request $request)
    {
        $request->validate([
            'MovieID' => 'required|exists:movie,MovieID',
            'RoomID' => 'required|exists:room,RoomID',
            'StartTime' => 'required|date',
            'Price' => 'required|numeric|min:0',
        ]);

        $movie = Movie::findOrFail($request->MovieID);
        $startTime = Carbon::parse($request->StartTime);
        $endTime = $startTime->copy()->addMinutes($movie->Duration);

        // 👉 Kiểm tra phải tạo trước 1 ngày
        $today = Carbon::today();
        if ($startTime->lt($today->copy()->addDay())) {
            return back()->withErrors([
                'StartTime' => 'Suất chiếu phải được tạo trước ít nhất 1 ngày!'
            ])->withInput();
        }

        // 👉 Kiểm tra trùng phòng + thời gian
        $conflict = Showtime::where('RoomID', $request->RoomID)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('StartTime', '<', $endTime)
                    ->where('EndTime', '>', $startTime);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors([
                'RoomID' => 'Phòng này đã có suất chiếu trong khoảng thời gian đó!'
            ])->withInput();
        }

        // 👉 Lưu suất chiếu mới
        Showtime::create([
            'MovieID' => $request->MovieID,
            'RoomID' => $request->RoomID,
            'StartTime' => $startTime,
            'EndTime' => $endTime,
            'Price' => $request->Price,
        ]);

        return redirect()->route('manager.showtimes.index')
            ->with('success', 'Thêm suất chiếu thành công!');
    }


    // Form chỉnh sửa suất chiếu
    public function edit($id)
    {
        $showtime = Showtime::findOrFail($id);
        $movies = Movie::all();
        $rooms = Room::with('theater')->get();

        return view('manager.showtime.edit', compact('showtime', 'movies', 'rooms'));
    }

    // Cập nhật suất chiếu
    public function update(Request $request, $id)
    {
        $request->validate([
            'MovieID' => 'required|exists:movie,MovieID',
            'RoomID' => 'required|exists:room,RoomID',
            'StartTime' => 'required|date',
            'Price' => 'required|numeric|min:0',
            'Status' => 'required|in:Scheduled,Cancelled',
        ]);

        $showtime = Showtime::findOrFail($id);
        $movie = Movie::findOrFail($request->MovieID);
        $startTime = Carbon::parse($request->StartTime);
        $endTime = $startTime->copy()->addMinutes($movie->Duration);
        $now = Carbon::now();

        // Kiểm tra logic trạng thái
        if ($request->Status == 'Cancelled') {
            if ($startTime->diffInHours($now, false) > 1) {
                return back()->withErrors(['Status' => 'Không thể hủy suất chiếu trong vòng 1 ngày trước khi chiếu!'])->withInput();
            }
        } elseif ($request->Status == 'Scheduled' && $showtime->Status == 'Cancelled') {
            // Chỉ xử lý khi đang bật lại từ Cancelled sang Scheduled
            if ($startTime->diffInHours($now, false) < 24) {
                // Thông báo hoặc tự cộng 1 ngày nếu muốn
                $startTime->addDay();
                $endTime = $startTime->copy()->addMinutes($movie->Duration);
            }
        }

        // Kiểm tra trùng phòng (bỏ qua suất chiếu hiện tại)
        $conflict = Showtime::where('RoomID', $request->RoomID)
            ->where('ShowtimeID', '!=', $id)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('StartTime', '<', $endTime)
                    ->where('EndTime', '>', $startTime);
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors([
                'RoomID' => 'Phòng này đã có suất chiếu trong khoảng thời gian đó!'
            ])->withInput();
        }

        $showtime->update([
            'MovieID' => $request->MovieID,
            'RoomID' => $request->RoomID,
            'StartTime' => $startTime,
            'EndTime' => $endTime,
            'Price' => $request->Price,
            'Status' => $request->Status,
        ]);

        return redirect()->route('manager.showtimes.index')
            ->with('success', 'Cập nhật suất chiếu thành công!');
    }


    // Xóa suất chiếu
    public function destroy($id)
    {
        $showtime = Showtime::findOrFail($id);
        $showtime->delete();

        return redirect()->route('manager.showtimes.index')
            ->with('success', 'Xóa suất chiếu thành công!');
    }
}
