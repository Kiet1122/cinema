<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Seat';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'SeatID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'RoomID',
        'SeatNumber',
        'SeatType',
        'ManagerID',
        'Price',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    // =================================== RELATIONSHIPS ===================================

    /**
     * Get the room that the seat belongs to.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'RoomID', 'RoomID');
    }

    /**
     * Get the booking details for the seat.
     */
    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetail::class, 'SeatID', 'SeatID');
    }

    /**
     * Get the manager that manages the seat.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'ManagerID', 'ManagerID');
    }
}