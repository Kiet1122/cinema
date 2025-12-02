<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theater extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Theater';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'TheaterID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Name',
        'Address',
        'Phone',
        'City',
        'ManagerID'
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
     * Get the rooms for the theater.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'TheaterID', 'TheaterID');
    }

    /**
     * Get the manager that manages the theater.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'ManagerID', 'ManagerID');
    }
}