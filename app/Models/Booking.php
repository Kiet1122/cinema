<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Booking';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'BookingID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CustomerID',
        'ShowtimeID',
        'TotalAmount',
        'Status',
        'PaymentStatus',
        'VoucherID',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'TotalAmount' => 'decimal:2',
    ];

    // =================================== RELATIONSHIPS ===================================

    /**
     * Get the customer that owns the booking.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    /**
     * Get the showtime for the booking.
     */
    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class, 'ShowtimeID', 'ShowtimeID');
    }

    /**
     * Get the voucher associated with the booking.
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'VoucherID', 'VoucherID');
    }
    
    /**
     * Get the booking details for the booking.
     */
    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetail::class, 'BookingID', 'BookingID');
    }
    
    /**
     * Get the payment associated with the booking.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'BookingID', 'BookingID');
    }
}