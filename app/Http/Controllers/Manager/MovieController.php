<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre; // Thêm Model Genre để lấy danh sách thể loại
use Illuminate\Support\Facades\Auth; // Để lấy ManagerID
use Illuminate\Support\Facades\DB; // Dùng cho transaction
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    /**
     * 1. Hiển thị danh sách phim.
     * GET /manager/movies
     */
    public function index()
    {
        $genres = Genre::all();
        // Lấy tất cả phim kèm thông tin manager và thể loại
        $movies = Movie::with(['manager', 'genres'])->orderBy('created_at', 'desc')->get();

        return view('Manager.movie.index', compact('movies', 'genres'));
    }

    /**
     * 2. Form thêm phim
     * GET /manager/movies/create
     */
    public function create()
    {
        // Lấy tất cả thể loại để hiển thị trong form dropdown/checkbox
        $genres = Genre::all();

        return view('Manager.movie.create', compact('genres'));
    }

    /**
     * 3. Lưu phim mới
     * POST /manager/movies
     */
    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'language' => 'nullable|string|max:100',
            'rating' => 'nullable|numeric|min:0|max:10',
            // genres là mảng các tên thể loại, không phải IDs
            'genres' => 'nullable|array',
            'genres.*' => 'string|max:50', // Từng phần tử là string tên thể loại
            'age_restriction' => ['nullable', Rule::in(['0', '13', '16', '18'])],
            'is_active' => 'nullable|boolean',

            // Xử lý Poster: cần đảm bảo chỉ 1 trong 2 được gửi
            'poster_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Nếu upload file
            'poster_url' => 'nullable|url|max:2048', // Nếu dùng URL
            'trailer_url' => 'nullable|url|max:2048',
        ]);

        // Lấy ManagerID của người dùng đang đăng nhập
        // KHẮC PHỤC LỖI: Lỗi "Incorrect integer value: 'email'" xảy ra do Auth::id() trả về email.
        // Ta sử dụng Auth::user()->id để đảm bảo lấy được ID số nguyên chuẩn của người dùng.
        $managerId = Auth::user()->id; // LẤY ID SỐ NGUYÊN

        // Xử lý logic poster (giả định)
        $posterUrl = $validated['poster_url'] ?? null;
        if ($request->hasFile('poster_file')) {
            // Logic upload file và lấy URL ở đây
            // Ví dụ: $posterUrl = $request->file('poster_file')->store('posters', 'public');
            // HIỆN TẠI GIỮ NGUYÊN POSTER URL/FILE HANDLING ĐƠN GIẢN
        }


        DB::beginTransaction(); // Bắt đầu Transaction để đảm bảo tính toàn vẹn
        try {
            // 2. Tạo Movie
            $movie = Movie::create([
                'Title' => $validated['title'],
                'Duration' => $validated['duration'],
                'Description' => $validated['description'] ?? null,
                'ReleaseDate' => $validated['release_date'] ?? null,
                'Language' => $validated['language'] ?? null,
                'Rating' => $validated['rating'] ?? null,
                'PosterURL' => $posterUrl, // Sử dụng URL đã xử lý
                'TrailerURL' => $validated['trailer_url'] ?? null,
                'AgeRestriction' => $validated['age_restriction'] ?? 0,
                'IsActive' => $validated['is_active'] ?? true,
                'ManagerID' => $managerId, // Gán ManagerID
            ]);

            // 3. Đồng bộ (Sync) thể loại vào bảng trung gian (movie_genre)
            if (!empty($validated['genres'])) {
                // Chuyển mảng tên thể loại ('Action', 'Comedy', ...) thành mảng IDs
                // KHẮC PHỤC LỖI: Column not found: 'Name'. Đã đổi thành 'GenreName'.
                $genreNames = $validated['genres'];

                $genreIDs = Genre::whereIn('GenreName', $genreNames)
                    ->pluck('GenreID') // Giả định ID của Genre là GenreID
                    ->toArray();

                // BƯỚC GỠ LỖI: Kiểm tra xem có tìm thấy ID nào không
                if (empty($genreIDs)) {
                    Log::warning("Genre sync warning: No Genre IDs found for names: " . implode(', ', $genreNames));
                }

                // Sử dụng IDs để sync vào bảng trung gian
                $movie->genres()->sync($genreIDs);
            } else {
                // Nếu không chọn thể loại nào, đảm bảo không có liên kết nào được tạo
                $movie->genres()->sync([]);
            }

            DB::commit(); // Hoàn tất Transaction
            return redirect()->route('manager.movies.index')->with('success', 'Thêm phim thành công!');

        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn lại các thay đổi nếu có lỗi (kể cả lỗi Foreign Key)

            // Log lỗi để debug chi tiết hơn
            Log::error("Movie creation failed: " . $e->getMessage() . " - ManagerID: " . $managerId);

            // Bắt lỗi cụ thể 1452 để hướng dẫn người dùng kiểm tra DB (Lỗi Foreign Key)
            if (str_contains($e->getMessage(), 'SQLSTATE[23000]') && str_contains($e->getMessage(), '1452')) {
                return back()->withInput()->withErrors(['db_error' => 'Lỗi ràng buộc dữ liệu: ID người quản lý không hợp lệ (ID ' . $managerId . '). Vui lòng kiểm tra xem ManagerID này có tồn tại trong bảng `manager` hay không.']);
            }

            // Hiển thị lỗi chi tiết hơn nếu không phải lỗi 1452
            // Việc này giúp bạn gỡ lỗi các vấn đề khác ngoài Foreign Key.
            $errorDetails = "Lỗi máy chủ: [" . $e->getCode() . "] " . $e->getMessage();
            return back()->withInput()->withErrors(['server_error' => 'Có lỗi xảy ra khi tạo phim. Chi tiết lỗi: ' . $errorDetails]);
        }
    }

    /**
     * 4. Form sửa phim
     * GET /manager/movies/{id}/edit
     */
    public function edit(Movie $movie)
    {
        // Bắt buộc phải load tất cả các thể loại để hiển thị trong <select multiple>
        $genres = Genre::all();

        // Bạn có thể cần load eager loading genres cho movie nếu chưa có
        // $movie->load('genres'); 

        return view('Manager.movie.edit', compact('movie', 'genres'));
    }

    /**
     * 5. Cập nhật phim
     * PUT/PATCH /manager/movies/{id}
     */
    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        // 1. Validation: SỬA để chấp nhận mảng 'genres'
        $validated = $request->validate([
            'Title' => 'required|string|max:255',
            'Duration' => 'required|integer|min:1',
            
            // FIX: Thay thế 'Genre' (string) bằng 'genres' (array)
            'genres' => 'nullable|array',
            'genres.*' => 'string|max:255', // Kiểm tra mỗi phần tử trong mảng là chuỗi
            
            'Description' => 'nullable|string',
            'ReleaseDate' => 'nullable|date',
            'Language' => 'nullable|string|max:50',
            'Rating' => 'nullable|numeric|min:0|max:10',
            
            'PosterFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'PosterURL' => 'nullable|url',
            
            'TrailerURL' => 'nullable|url',
            'AgeRestriction' => 'nullable|integer',
            'IsActive' => 'nullable|boolean',
        ]);

        // Xử lý Poster (logic upload file giả định, cần được thay thế bằng logic thực tế)
        $posterUrl = $validated['PosterURL'] ?? $movie->PosterURL;
        if ($request->hasFile('PosterFile')) { 
            // *Thêm logic upload file thực tế ở đây và gán URL cho $posterUrl*
            // $posterUrl = $request->file('PosterFile')->store('posters', 'public');
            // Log::info("New file uploaded for poster, URL: " . $posterUrl);
        }

        // Xử lý trạng thái IsActive
        $isActive = $request->has('IsActive') ? true : false;

        // FIX: Lấy mảng GenreNames đã được validate trực tiếp từ request
        $genreNames = $validated['genres'] ?? []; 

        DB::beginTransaction();
        try {
            // 2. Cập nhật Movie
            $movie->update([
                'Title' => $validated['Title'],
                'Duration' => $validated['Duration'],
                'Description' => $validated['Description'] ?? null,
                'ReleaseDate' => $validated['ReleaseDate'] ?? null,
                'Language' => $validated['Language'] ?? null,
                'Rating' => $validated['Rating'] ?? null,
                'PosterURL' => $posterUrl, 
                'TrailerURL' => $validated['TrailerURL'] ?? null,
                'AgeRestriction' => $validated['AgeRestriction'] ?? 0,
                'IsActive' => $isActive, 
            ]);

            // 3. Đồng bộ (Sync) thể loại mới (Tên -> ID)
            if (!empty($genreNames)) {
                // Lấy GenreIDs từ GenreNames đã được xử lý
                $genreIDs = Genre::whereIn('GenreName', $genreNames)
                    ->pluck('GenreID')
                    ->toArray();
                
                // Đồng bộ các GenreID tìm được
                $movie->genres()->sync($genreIDs);
                
                // BƯỚC GỠ LỖI: Kiểm tra nếu số lượng ID tìm được ít hơn tên gửi lên
                if (count($genreIDs) !== count($genreNames)) {
                     Log::warning("Genre sync warning: Found " . count($genreIDs) . " IDs for " . count($genreNames) . " names. Check if all submitted genre names exist.");
                }

            } else {
                // Nếu không chọn thể loại nào, xóa tất cả các liên kết thể loại
                $movie->genres()->sync([]);
            }

            DB::commit();
            return redirect()->route('manager.movies.index')->with('success', 'Cập nhật phim thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Movie update failed: " . $e->getMessage() . " - MovieID: " . $id);

            if (str_contains($e->getMessage(), 'SQLSTATE[23000]') && str_contains($e->getMessage(), '1452')) {
                return back()->withInput()->withErrors(['db_error' => 'Lỗi ràng buộc dữ liệu: Lỗi khóa ngoại khi cập nhật.']);
            }
            return back()->withInput()->withErrors(['server_error' => 'Có lỗi xảy ra khi cập nhật phim. Vui lòng thử lại.']);
        }
    }

    /**
     * 6. Xóa phim
     * DELETE /manager/movies/{id}
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return redirect()->route('manager.movies.index')->with('success', 'Xóa phim thành công!');
    }

    /**
     * 7. Chuyển đổi trạng thái (Toggle Status)
     * POST /manager/movies/{id}/toggle-status
     */
    public function toggleStatus($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->IsActive = !$movie->IsActive; // đổi trạng thái
        $movie->save();

        return redirect()->route('manager.movies.index')
            ->with('success', 'Trạng thái phim đã được cập nhật.');
    }
}
