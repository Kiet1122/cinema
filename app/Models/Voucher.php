<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
class Voucher extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'Voucher';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'VoucherID';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'Code',
        'DiscountType',
        'Value',
        'StartDate',
        'EndDate',
        'Status',
        'UsageLimit',
        'UsedCount',
        'PerUserLimit',
        'ManagerID',
    ];

    /**
     * Các thuộc tính sẽ được tự động chuyển đổi kiểu dữ liệu.
     *
     * @var array
     */
    protected $casts = [
        'StartDate' => 'datetime',
        'EndDate' => 'datetime',
        'created_at' => 'datetime',
        'Value' => 'decimal:2',
    ];

    /**
     * Tắt các cột timestamps mặc định của Laravel.
     *
     * @var bool
     */
    public $timestamps = false;
    
    // =================================== QUAN HỆ ===================================

    /**
     * Lấy manager đã tạo voucher này.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Lấy tất cả các booking đã sử dụng voucher này.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'VoucherID', 'VoucherID');
    }

    /**
     * Lấy tất cả các customer vouchers sử dụng voucher này.
     */
    public function customerVouchers(): HasMany
    {
        return $this->hasMany(CustomerVoucher::class, 'VoucherID', 'VoucherID');
    }

    /**
     * Kiểm tra xem voucher có đang hoạt động không
     */
    public function getIsActiveAttribute()
    {
        $now = Carbon::now();
        return $this->Status === 'Active' && 
               Carbon::parse($this->StartDate) <= $now && 
               Carbon::parse($this->EndDate) >= $now;
    }

    /**
     * Kiểm tra xem voucher có sắp hết hạn không (trong 7 ngày tới)
     */
    public function getIsExpiringSoonAttribute()
    {
        $now = Carbon::now();
        $sevenDaysLater = $now->copy()->addDays(7);
        
        return $this->Status === 'Active' && 
               Carbon::parse($this->EndDate) >= $now && 
               Carbon::parse($this->EndDate) <= $sevenDaysLater;
    }
}