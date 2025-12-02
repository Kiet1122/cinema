<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Room';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'RoomID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'TheaterID',
        'RoomName',
        'Capacity',
        'RoomType',
        'ManagerID'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    // =================================== RELATIONSHIPS ===================================

    /**
     * Get the theater that the room belongs to.
     */
    public function theater(): BelongsTo
    {
        return $this->belongsTo(Theater::class, 'TheaterID', 'TheaterID');
    }

    /**
     * Get the seats for the room.
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class, 'RoomID', 'RoomID');
    }

    /**
     * Get the showtimes for the room.
     */
    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class, 'RoomID', 'RoomID');
    }

    /**
     * Get the manager that manages the room.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'ManagerID', 'ManagerID');
    }
}