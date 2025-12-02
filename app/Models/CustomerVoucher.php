<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerVoucher extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'CustomerVoucher';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'CustomerVoucherID';

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
        'CustomerID',
        'VoucherID',
        'UsedAt',
        'IsUsed',
    ];

    /**
     * Các thuộc tính sẽ được tự động chuyển đổi kiểu dữ liệu.
     *
     * @var array
     */
    protected $casts = [
        // Đã loại bỏ 'created_at' vì cột này không tồn tại và public $timestamps = false.
        'UsedAt' => 'datetime',
        'IsUsed' => 'boolean',
    ];

    // ================= QUAN HỆ ================= //

    /**
     * Lấy customer liên quan đến voucher này.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    /**
     * Lấy voucher liên quan đến bản ghi này.
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'VoucherID', 'VoucherID');
    }
}
