<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Manager\{
    DashboardController,
    TheaterController,
    RoomController,
    MovieController,
    ShowtimeController,
    SeatController,
    MemberController,
    VoucherController,
    GenreController,
    ProfileController,
    ReportsController,
    ReviewsController,
    BookingsController,
    NotificationController,
};
use App\Http\Controllers\Customer\{
    HomeController,
    ProfileController as CustomerProfileController,
    DetailsMovieController,
    ReviewController,
    BookingController,
    VoucherCusController,
    ProfileCusController,
    NotificationCustomerController,
    VNPayController,
    ContactController
};

// ========== KHÁCH HÀNG ==========

// Trang chủ khách hàng (hiển thị danh sách phim đang chiếu)
Route::get('/', [HomeController::class, 'home'])->name('customer.home');

// Đăng nhập / Đăng xuất / Đăng ký
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/check-email', [RegisterController::class, 'checkEmail'])->name('check-email');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Nhóm route cho customer
Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::get('/movie/details/{id}', [DetailsMovieController::class, 'details'])->name('movie.details');
    Route::post('/movie/{id}/review', [ReviewController::class, 'store'])->name('movie.review.store');

    // Booking Routes - Sử dụng name prefix 'booking.' 
    Route::prefix('booking')->name('booking.')->group(function () {
        // Main booking routes
        Route::get('/create/{showtime_id}', [BookingController::class, 'create'])->name('create');
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/payment/{id}', [BookingController::class, 'payment'])->name('payment');
        Route::post('/payment/{id}/process', [BookingController::class, 'processPayment'])->name('process.payment');
        Route::get('/success/{id}', [BookingController::class, 'success'])->name('success');
        Route::get('/history', [BookingController::class, 'history'])->name('history');
        Route::get('/{id}', [BookingController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');

        // Additional features
        Route::get('/{id}/download', [BookingController::class, 'downloadTicket'])->name('download');
        Route::post('/{id}/send-confirmation', [BookingController::class, 'sendConfirmation'])->name('send.confirmation');

        // API routes - có thể để trong group riêng nếu cần
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/seats/{showtime_id}', [BookingController::class, 'getSeats'])->name('seats');
            Route::post('/validate-voucher', [BookingController::class, 'validateVoucher'])->name('validate.voucher');
            Route::post('/calculate-price', [BookingController::class, 'calculatePrice'])->name('calculate.price');
        });

        

    });
    Route::post('/payment/create', [VNPayController::class, 'createPayment'])->name('payment.create');

    // URL nhận về kết quả
    Route::get('/payment/vnpay/return', [VNPayController::class, 'return'])->name('payment.return');

    Route::post('/vnpay/create', [VNPayController::class, 'createPayment'])->name('vnpay.create');
    Route::get('/vnpay/return', [VNPayController::class, 'return'])->name('vnpay.return');

    Route::prefix('voucher')->name('voucher.')->group(function () {
        Route::get('/list', [VoucherCusController::class, 'index'])->name('list');
        Route::get('/my-vouchers', [VoucherCusController::class, 'myVouchers'])->name('my-vouchers');
        Route::post('/claim/{voucherId}', [VoucherCusController::class, 'claim'])->name('claim');
    });


    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileCusController::class, 'index'])->name('index');
        Route::put('/update', [ProfileCusController::class, 'updateProfile'])->name('update'); // PUT
        Route::post('/update-password', [ProfileCusController::class, 'updatePassword'])->name('update.password');
        Route::post('/update-email', [ProfileCusController::class, 'updateEmail'])->name('update.email');
        Route::delete('/remove-avatar', [ProfileCusController::class, 'removeAvatar'])->name('remove.avatar'); // DELETE
    });

    Route::get('/notifications', [NotificationCustomerController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationCustomerController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{id}/mark-read', [NotificationCustomerController::class, 'markAsRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [NotificationCustomerController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [NotificationCustomerController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/clear/read', [NotificationCustomerController::class, 'clearRead'])->name('notifications.clearRead');
    Route::get('/notifications/unread/count', [NotificationCustomerController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::get('/notifications/recent', [NotificationCustomerController::class, 'getRecentNotifications'])->name('notifications.recent');
    Route::get('/notifications/unread/count', [NotificationCustomerController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::post('/notifications/{id}/mark-read', [NotificationCustomerController::class, 'markAsRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [NotificationCustomerController::class, 'markAllAsRead'])->name('notifications.markAllRead');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
});


// ========== QUẢN LÝ (MANAGER) ==========

Route::prefix('manager')->name('manager.')->middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rạp chiếu, phòng, phim, lịch chiếu
    Route::resources([
        'theaters' => TheaterController::class,
        'rooms' => RoomController::class,
        'movies' => MovieController::class,
        'showtimes' => ShowtimeController::class,
        'seats' => SeatController::class,
        'genre' => GenreController::class,
        'member' => MemberController::class,
        'vouchers' => VoucherController::class,
        'reviews' => ReviewsController::class,
        'bookings' => BookingsController::class,
    ]);

    // Toggle trạng thái phim
    Route::patch('movies/{id}/toggle', [MovieController::class, 'toggleStatus'])->name('movies.toggle');

    // Voucher cho thành viên
    Route::prefix('member')->name('member.')->group(function () {
        Route::post('/{customer}/assign-voucher', [MemberController::class, 'assignVoucher'])->name('assignVoucher');
        Route::delete('/{CustomerID}/revoke-voucher/{customerVoucherId}', [MemberController::class, 'revokeVoucher'])->name('revoke_voucher');
    });

    // Gán tất cả voucher
    Route::post('/vouchers/assign-all', [VoucherController::class, 'assignAllVouchers'])->name('vouchers.assignAll');

    // Báo cáo
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportsController::class, 'generateRevenueReport'])->name('revenue');
        Route::get('/performance', [ReportsController::class, 'generateMoviePerformanceReport'])->name('performance');
        // Export routes - sửa lại
        Route::get('/export/revenue/{year?}', [ReportsController::class, 'exportRevenueExcel'])->name('export.revenue');
        Route::get('/export/performance', [ReportsController::class, 'exportPerformanceExcel'])->name('export.performance');
    });



    // Gửi thông báo
    Route::get('/notification', [NotificationController::class, 'index'])->name('notification.index');
    Route::post('/notification/send', [NotificationController::class, 'sendNotification'])->name('notification.send');

    // Hồ sơ quản lý
    Route::prefix('profile')->as('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/', 'update')->name('update');
    });




    Route::get('/bookings', [BookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [BookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [BookingsController::class, 'update'])->name('bookings.update');
    Route::put('/bookings/{id}/status', [BookingsController::class, 'updateStatus'])->name('bookings.updateStatus');
    Route::put('/bookings/{id}/payment-status', [BookingsController::class, 'updatePaymentStatus'])->name('bookings.updatePaymentStatus');
    Route::delete('/bookings/{id}', [BookingsController::class, 'destroy'])->name('bookings.destroy');
});
