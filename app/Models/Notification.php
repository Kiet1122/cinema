<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Notification';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'NotificationID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CustomerID',
        'Title',
        'Message',
        'Status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    // =================================== RELATIONSHIPS ===================================

    /**
     * Get the customer that owns the notification.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }

    /**
     * Get the manager who created the notification.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'ManagerID', 'ManagerID');
    }
}