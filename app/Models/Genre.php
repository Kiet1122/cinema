<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    use HasFactory;

    // Khai báo tên bảng và khóa chính
    protected $table = 'Genre';
    protected $primaryKey = 'GenreID';
    
    // Tắt timestamps vì bảng Genre không có created_at/updated_at
    public $timestamps = false; 

    protected $fillable = [
        'GenreName',
        'Description',
    ];

    /**
     * Get all movies for the genre (Many-to-Many relationship).
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(
            Movie::class,
            'MovieGenre', 
            'GenreID',     // Khóa ngoại của Genre trong bảng MovieGenre
            'MovieID'      // Khóa ngoại của Movie trong bảng MovieGenre
        );
    }
}