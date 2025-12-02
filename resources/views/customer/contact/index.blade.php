@extends('customer.layouts.app')

@section('title', 'Liên Hệ')

@section('styles')
<style>
    .contact-container {
        min-height: calc(100vh - 200px);
        padding: 60px 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    .contact-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .contact-header h1 {
        font-size: 36px;
        font-weight: 700;
        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 15px;
    }
    
    .contact-header p {
        font-size: 16px;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.5;
    }
    
    /* Phần lọc rạp */
    .search-section {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .search-title {
        text-align: center;
        margin-bottom: 25px;
    }
    
    .search-title h2 {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }
    
    .search-title p {
        color: #666;
        font-size: 14px;
    }
    
    /* Form tìm kiếm */
    .search-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .form-group {
        margin-bottom: 0;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e1e5eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: #8f94fb;
        box-shadow: 0 0 0 3px rgba(143, 148, 251, 0.1);
        outline: none;
    }
    
    .btn-search {
        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
        width: 100%;
    }
    
    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78, 84, 200, 0.3);
    }
    
    /* Hiển thị rạp khi tìm thấy */
    .theater-result-section {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
        display: none; /* Ẩn ban đầu */
    }
    
    .theater-result-section.show {
        display: block; /* Hiện khi có kết quả */
    }
    
    .result-title {
        text-align: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .result-title h3 {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .result-title p {
        color: #666;
        font-size: 14px;
    }
    
    /* Card hiển thị rạp */
    .theater-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .theater-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        border-color: #8f94fb;
    }
    
    .theater-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .theater-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
    }
    
    .theater-icon i {
        font-size: 24px;
        color: white;
    }
    
    .theater-info-main {
        flex: 1;
    }
    
    .theater-name {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .theater-city {
        display: inline-block;
        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .theater-details {
        margin-top: 20px;
    }
    
    .theater-detail {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
        color: #555;
    }
    
    .theater-detail i {
        color: #4e54c8;
        margin-right: 12px;
        margin-top: 3px;
        font-size: 16px;
        min-width: 20px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 2px;
        display: block;
    }
    
    .detail-value {
        line-height: 1.4;
    }
    
    /* Trạng thái không tìm thấy */
    .no-result {
        text-align: center;
        padding: 40px;
        color: #666;
    }
    
    .no-result i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #ddd;
    }
    
    .no-result h4 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #333;
    }
    
    /* Thông tin liên hệ */
    .contact-info {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-top: 30px;
    }
    
    .contact-info h3 {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .contact-detail {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .contact-detail:hover {
        background: #f8f9fa;
    }
    
    .contact-detail i {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .contact-text {
        flex: 1;
    }
    
    .contact-text strong {
        display: block;
        color: #333;
        margin-bottom: 3px;
    }
    
    .contact-text a {
        color: #4e54c8;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .contact-text a:hover {
        color: #8f94fb;
        text-decoration: underline;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .contact-container {
            padding: 30px 15px;
        }
        
        .contact-header h1 {
            font-size: 28px;
        }
        
        .search-section,
        .theater-result-section,
        .contact-info {
            padding: 20px;
        }
        
        .search-title h2 {
            font-size: 24px;
        }
        
        .theater-header {
            flex-direction: column;
            text-align: center;
        }
        
        .theater-icon {
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        .contact-detail {
            flex-direction: column;
            text-align: center;
            padding: 15px;
        }
        
        .contact-detail i {
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="contact-container">
    <div class="container">
        <!-- Header -->
        <div class="contact-header">
            <h1>Tìm Kiếm Rạp Chiếu Phim</h1>
            <p>Nhập tên thành phố để tìm rạp chiếu phim trong khu vực của bạn</p>
        </div>

        <!-- Form tìm kiếm rạp -->
        <div class="search-section">
            <div class="search-title">
                <h2>Tìm Rạp Theo Thành Phố</h2>
                <p>Chỉ hiển thị rạp khi bạn tìm kiếm</p>
            </div>
            
            <form method="GET" action="{{ route('customer.contact') }}" class="search-form" id="searchForm">
                <div class="form-group">
                    <label for="city">
                        <i class="fas fa-city mr-2"></i>Chọn thành phố
                    </label>
                    <select name="city" id="city" class="form-control" required>
                        <option value="">-- Chọn thành phố --</option>
                        @if(isset($cities))
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <button type="submit" class="btn-search">
                    <i class="fas fa-search mr-2"></i> Tìm Rạp Chiếu Phim
                </button>
                
                @if(request()->has('city'))
                    <a href="{{ route('customer.contact') }}" class="btn-search" style="background: #6c757d; margin-top: 10px;">
                        <i class="fas fa-times mr-2"></i> Xóa Tìm Kiếm
                    </a>
                @endif
            </form>
        </div>

        <!-- Kết quả tìm kiếm - CHỈ HIỆN KHI CÓ KẾT QUẢ -->
        @if(request()->has('city') && isset($randomTheater))
            <div class="theater-result-section show" id="theaterResult">
                <div class="result-title">
                    <h3>Rạp Chiếu Phim Tại {{ request('city') }}</h3>
                    <p>Kết quả tìm kiếm của bạn</p>
                </div>
                
                <div class="theater-card">
                    <div class="theater-header">
                        <div class="theater-icon">
                            <i class="fas fa-film"></i>
                        </div>
                        <div class="theater-info-main">
                            <div class="theater-name">{{ $randomTheater->Name }}</div>
                            <span class="theater-city">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $randomTheater->City }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="theater-details">
                        <div class="theater-detail">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <span class="detail-label">Địa chỉ:</span>
                                <span class="detail-value">{{ $randomTheater->Address }}</span>
                            </div>
                        </div>
                        
                        <div class="theater-detail">
                            <i class="fas fa-phone"></i>
                            <div>
                                <span class="detail-label">Số điện thoại:</span>
                                <span class="detail-value">
                                    <a href="tel:{{ $randomTheater->Phone }}" style="color: #4e54c8; text-decoration: none;">
                                        {{ $randomTheater->Phone }}
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted">
                        <i class="fas fa-info-circle mr-2"></i>
                        Đây là một trong những rạp chiếu phim tại {{ request('city') }}
                    </p>
                </div>
            </div>
        @elseif(request()->has('city'))
            <!-- Không tìm thấy kết quả -->
            <div class="theater-result-section show" id="noResult">
                <div class="no-result">
                    <i class="fas fa-search"></i>
                    <h4>Không tìm thấy rạp chiếu phim</h4>
                    <p>Không có rạp chiếu phim nào tại {{ request('city') }}</p>
                    <p class="text-muted mt-3">
                        <i class="fas fa-lightbulb mr-2"></i>
                        Hãy thử tìm kiếm với thành phố khác
                    </p>
                    <a href="{{ route('customer.contact') }}" class="btn-search mt-3" style="max-width: 200px; margin: 20px auto 0;">
                        <i class="fas fa-undo mr-2"></i> Tìm lại
                    </a>
                </div>
            </div>
        @endif

        <!-- Thông tin liên hệ -->
        <div class="contact-info">
            <h3>Thông Tin Liên Hệ</h3>
            
            <div class="contact-detail">
                <i class="fas fa-phone-alt"></i>
                <div class="contact-text">
                    <strong>Điện thoại hỗ trợ</strong>
                    <a href="tel:19001234">1900 1234</a> (24/7)
                </div>
            </div>
            
            <div class="contact-detail">
                <i class="fas fa-envelope"></i>
                <div class="contact-text">
                    <strong>Email hỗ trợ</strong>
                    <a href="mailto:support@cinemabooking.vn">support@cinemabooking.vn</a>
                </div>
            </div>
            
            <div class="contact-detail">
                <i class="fas fa-map-marker-alt"></i>
                <div class="contact-text">
                    <strong>Trụ sở chính</strong>
                    123 Đường ABC, Quận 1, TP. Hồ Chí Minh
                </div>
            </div>
            
            <div class="contact-detail">
                <i class="fas fa-clock"></i>
                <div class="contact-text">
                    <strong>Giờ làm việc</strong>
                    08:00 - 22:00 hàng ngày
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const citySelect = document.getElementById('city');
    const searchForm = document.getElementById('searchForm');
    
    // Auto submit khi chọn thành phố
    if (citySelect) {
        citySelect.addEventListener('change', function() {
            if (this.value) {
                searchForm.submit();
            }
        });
    }
    
    // Scroll đến kết quả nếu có
    @if(request()->has('city') && isset($randomTheater))
        setTimeout(() => {
            const resultSection = document.getElementById('theaterResult');
            if (resultSection) {
                resultSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        }, 300);
    @elseif(request()->has('city'))
        setTimeout(() => {
            const noResultSection = document.getElementById('noResult');
            if (noResultSection) {
                noResultSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        }, 300);
    @endif
});
</script>
@endsection