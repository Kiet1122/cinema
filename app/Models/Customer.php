<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Customer';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'CustomerID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'UserID',
        'FullName',
        'Phone',
        'Gender',
        'DateOfBirth',
        'Address',
        'Avatar',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'DateOfBirth' => 'date',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    // =================================== RELATIONSHIPS ===================================

    /**
     * Get the user that owns the customer record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Get the reviews for the customer.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'CustomerID', 'CustomerID');
    }

    /**
     * Get the bookings for the customer.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'CustomerID', 'CustomerID');
    }

    /**
     * Get the customer vouchers for the customer.
     */
    public function customerVouchers(): HasMany
    {
        return $this->hasMany(CustomerVoucher::class, 'CustomerID', 'CustomerID');
    }

    /**
     * Get the notifications for the customer.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'CustomerID', 'CustomerID');
    }

    
}