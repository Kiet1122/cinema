<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Showtime extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'Showtime';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'ShowtimeID';

    /**
     * Tắt các cột timestamps mặc định của Laravel.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'MovieID',
        'RoomID',
        'StartTime',
        'EndTime',
        'Price',
        'Status',
        'ManagerID',
    ];
    
    /**
     * Các thuộc tính sẽ được tự động chuyển đổi kiểu dữ liệu.
     *
     * @var array
     */
    protected $casts = [
        'StartTime' => 'datetime',
        'EndTime' => 'datetime',
        'Price' => 'decimal:2',
    ];

    // ================= QUAN HỆ ================= //

    /**
     * Lấy movie liên quan đến suất chiếu này.
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'MovieID', 'MovieID');
    }

    /**
     * Lấy room liên quan đến suất chiếu này.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'RoomID', 'RoomID');
    }

    /**
     * Lấy manager liên quan đến suất chiếu này.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Lấy tất cả các bookings của suất chiếu này.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'ShowtimeID', 'ShowtimeID');
    }

    public function theater()
    {
        return $this->hasOneThrough(
            Theater::class, // Model đích
            Room::class,    // Model trung gian
            'RoomID',       // Khóa ngoại trong Room trỏ tới Showtime? (phải là RoomID ở showtime) 
            'TheaterID',    // Khóa chính trong Theater
            'RoomID',       // Khóa ngoại trong Showtime
            'TheaterID'     // Khóa ngoại trong Room
        );
    }
}