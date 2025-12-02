<!-- Giả định file này được kế thừa từ layout chính của trang quản trị (manager.layouts.app) -->
@extends('manager.layouts.app')

@section('title', 'Quản lý Đánh giá Phim')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLMDJzLQQkX8I1p4yLw/1O1uC3+K8kY1K1/8y/q2u3s" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="p-4 sm:ml-64 min-h-screen bg-gray-100">
    <div class="p-4 mt-14 bg-white rounded-xl shadow-2xl">
        <h1 class="text-3xl font-extrabold mb-6 text-gray-800 border-b-4 border-yellow-500 pb-2 flex items-center">
            <i class="fas fa-comments text-yellow-500 mr-3"></i> Quản lý Đánh giá Phim
        </h1>

        <!-- Thanh Tìm Kiếm -->
        <div class="mb-6 flex flex-col md:flex-row justify-end items-center space-y-4 md:space-y-0">
            {{-- Đã loại bỏ hoàn toàn Form Lọc Trạng Thái --}}

            <!-- Form Tìm Kiếm -->
            {{-- Giả định $searchQuery được truyền từ Controller --}}
            @php $searchQuery = $searchQuery ?? ''; @endphp
            <form action="{{ route('manager.reviews.index') }}" method="GET" class="w-full md:w-80">
                <input type="hidden" name="status" value="all"> {{-- Giữ lại status mặc định 'all' để không làm hỏng cấu trúc URL nếu có --}}
                <div class="flex rounded-lg shadow-inner">
                    <input type="text" name="search" placeholder="Tìm kiếm nội dung/phim/người dùng..." 
                           value="{{ $searchQuery }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-r-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Bảng Danh Sách Đánh Giá -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-yellow-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Phim</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Người dùng</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Điểm/Nội dung</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Ngày tạo</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-yellow-50 transition duration-150 ease-in-out">
                            {{-- Sửa: Sử dụng ReviewID làm khóa chính --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $review->ReviewID }}</td>
                            
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $review->movie->title ?? 'N/A' }} 
                            </td>
                            
                            {{-- Sửa: Sử dụng quan hệ 'customer' thay vì 'user' --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $review->customer->name ?? 'Người dùng bị xóa' }}
                            </td>
                            
                            <td class="px-6 py-4 max-w-lg text-sm">
                                <div class="flex items-center">
                                    {{-- Sửa: Sử dụng cột Rating (hoặc accessor rating_score nếu có) --}}
                                    <span class="font-bold text-lg mr-2 text-yellow-600 flex items-center">
                                        {{ $review->Rating ?? 'N/A' }} 
                                        <i class="fas fa-star text-sm ml-1"></i>/5
                                    </span>
                                </div>
                                {{-- Sửa: Sử dụng cột Comment (hoặc accessor content nếu có) --}}
                                <p class="text-gray-600 mt-1 line-clamp-2 italic text-xs">"{{ Str::limit($review->Comment, 100) }}"</p>
                            </td>
                            
                            {{-- Đã loại bỏ cột Trạng thái --}}

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                {{ $review->created_at->format('d/m/Y') }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-2">
                                    {{-- Chỉ giữ lại Nút Xóa --}}
                                    {{-- Sửa: Sử dụng ReviewID cho route destroy --}}
                                    <form action="{{ route('manager.reviews.destroy', $review->ReviewID) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Xóa vĩnh viễn đánh giá này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Xóa vĩnh viễn" class="text-red-600 hover:text-red-800 transition duration-150 transform hover:scale-110 p-1 rounded-full hover:bg-red-50">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            {{-- Cập nhật colspan từ 7 thành 6 --}}
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 bg-gray-50 font-medium text-lg">
                                <i class="fas fa-info-circle mr-2"></i>Không tìm thấy đánh giá nào theo tiêu chí lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân Trang -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg shadow-inner">
            {{-- Loại bỏ 'status' khỏi appends --}}
            {{ $reviews->appends(['search' => $searchQuery])->links() }}
        </div>
    </div>
</div>
@endsection
