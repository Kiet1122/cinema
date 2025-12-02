<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Manager extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'Manager';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'ManagerID';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'UserID',
        'FullName',
        'Phone',
        'Avatar',
    ];

    /**
     * Bảng không sử dụng các cột timestamps mặc định.
     *
     * @var bool
     */
    public $timestamps = false;

    // =================================== QUAN HỆ ===================================

    /**
     * Lấy user liên quan đến người quản lý này.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Lấy tất cả các rạp chiếu phim được quản lý bởi người này.
     */
    public function theaters(): HasMany
    {
        return $this->hasMany(Theater::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Lấy tất cả các phòng được quản lý bởi người này.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Lấy tất cả các ghế được quản lý bởi người này.
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Lấy tất cả các suất chiếu được quản lý bởi người này.
     */
    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Lấy tất cả các voucher được tạo bởi người này.
     */
    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Lấy tất cả các bộ phim được quản lý bởi người này.
     */
    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Lấy tất cả các thông báo được tạo bởi người quản lý này.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'ManagerID', 'ManagerID');
    }
}