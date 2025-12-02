<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review; // Giả định Model Review đã được cấu hình
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewsController extends Controller
{
    /**
     * Hiển thị danh sách tất cả các đánh giá (Reviews).
     * Loại bỏ logic lọc theo trạng thái vì cột 'status' không tồn tại.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Loại bỏ $filterStatus vì cột 'status' không tồn tại
        $searchQuery = $request->get('search');
        
        // Bắt đầu truy vấn, tải thông tin Customer (User) và Movie liên quan
        // Sử dụng 'customer' thay vì 'user' dựa trên cấu hình Model
        $reviews = Review::with(['customer', 'movie']) 
            ->latest('created_at'); // Sắp xếp theo đánh giá mới nhất trước

        // 1. Lọc theo tìm kiếm
        if ($searchQuery) {
            $reviews->where(function ($query) use ($searchQuery) {
                // Tìm kiếm trong nội dung review (sử dụng cột DB thực tế: Comment)
                // Hoặc dựa vào accessor 'content' nếu đã cấu hình trong Model
                $query->where('Comment', 'like', '%' . $searchQuery . '%') 
                      // Hoặc tìm kiếm theo tên phim (qua relationship 'movie')
                      ->orWhereHas('movie', function ($q) use ($searchQuery) {
                          $q->where('title', 'like', '%' . $searchQuery . '%');
                      })
                      // Hoặc tìm kiếm theo tên người dùng (qua relationship 'customer')
                      ->orWhereHas('customer', function ($q) use ($searchQuery) {
                          $q->where('name', 'like', '%' . $searchQuery . '%');
                      });
            });
        }

        $reviews = $reviews->paginate(15); // Phân trang 15 kết quả

        // Trả về view quản lý đánh giá
        return view('manager.reviews.index', [
            'reviews' => $reviews,
            // $filterStatus được truyền xuống là 'all' hoặc bị bỏ qua tùy vào cách dùng ở View
            'filterStatus' => 'all',
            'searchQuery' => $searchQuery,
        ]);
    }

    // Đã loại bỏ hoàn toàn phương thức updateStatus() vì nó phụ thuộc vào cột 'status'

    /**
     * Xóa vĩnh viễn một đánh giá khỏi hệ thống.
     *
     * @param  int  $id ID của Review (ReviewID)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);
            
            // Lấy Movie ID sử dụng tên cột DB thực tế: MovieID
            $movieId = $review->MovieID; 

            $review->delete();

            // Cập nhật lại rating trung bình của phim sau khi xóa
            $this->updateMovieRating($movieId);

            return back()->with('success', 'Đã xóa đánh giá thành công.');

        } catch (\Exception $e) {
            Log::error("Lỗi xóa Review ID: {$id}. Lỗi: " . $e->getMessage());
            return back()->with('error', 'Không thể xóa đánh giá. Vui lòng thử lại.');
        }
    }
    
    /**
     * [CHỨC NĂNG NGHIỆP VỤ NỘI BỘ]
     * Tính toán và cập nhật lại điểm Rating trung bình cho một bộ phim.
     * Tính toán dựa trên TẤT CẢ các review vì không có cột 'status'.
     * @param int $movieId ID của phim
     * @return void
     */
    protected function updateMovieRating($movieId)
    {
        // 1. Tính điểm trung bình của TẤT CẢ các review
        // Sử dụng tên cột DB thực tế: 'Rating'
        $averageRating = Review::where('MovieID', $movieId)
                                 // Loại bỏ where('status', 'active')
                                 ->avg('Rating'); 

        // 2. Cập nhật vào bảng movies
        // Giả định cột rating của phim là 'average_rating'
        DB::table('movies') 
            ->where('id', $movieId)
            ->update(['average_rating' => round($averageRating ?? 0, 1)]); // Làm tròn 1 chữ số thập phân
        
        Log::info("Đã cập nhật rating cho Movie ID: {$movieId} thành {$averageRating}");
    }
}
