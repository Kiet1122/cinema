<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Review';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ReviewID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'MovieID',
        'CustomerID',
        'Rating',
        'Comment',
        'IsEdited',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'IsEdited' => 'boolean',
        'Rating' => 'integer',
    ];

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    // =================================== RELATIONSHIPS ===================================

    /**
     * Get the movie that the review belongs to.
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'MovieID', 'MovieID');
    }

    /**
     * Get the customer that wrote the review.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'CustomerID');
    }
}