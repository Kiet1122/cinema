<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDetail extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'BookingDetail';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'BookingDetailID';

    /**
     * Bảng không sử dụng các cột timestamps mặc định.
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
        'BookingID',
        'SeatID',
        'Price',
    ];

    /**
     * Các thuộc tính sẽ được tự động chuyển đổi kiểu dữ liệu.
     *
     * @var array
     */
    protected $casts = [
        'Price' => 'decimal:2',
    ];

    // ================= QUAN HỆ ================= //

    /**
     * Lấy booking liên quan đến chi tiết này.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'BookingID', 'BookingID');
    }

    /**
     * Lấy seat liên quan đến chi tiết này.
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class, 'SeatID', 'SeatID');
    }
}