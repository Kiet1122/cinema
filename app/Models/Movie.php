<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Cần thêm

class Movie extends Model
{
    use HasFactory;

    protected $table = 'Movie';
    protected $primaryKey = 'MovieID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Title',
        // 'Genre', // Đã xóa vì đã chuyển sang bảng riêng
        'Duration',
        'Description',
        'ReleaseDate',
        'Language',
        'Rating',
        'PosterURL',
        'TrailerURL',
        'AgeRestriction',
        'IsActive',
        'ManagerID'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'ReleaseDate' => 'date',
        'Rating' => 'decimal:1',
        'IsActive' => 'boolean',
        'created_at' => 'datetime',
    ];

    // =================================== RELATIONSHIPS ===================================

    /**
     * Get the manager that manages the movie.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'ManagerID', 'ManagerID');
    }

    /**
     * Get the showtimes for the movie.
     */
    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class, 'MovieID', 'MovieID');
    }

    /**
     * Get the reviews for the movie.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'MovieID', 'MovieID');
    }

    // --- QUAN HỆ MỚI CHO THỂ LOẠI (GENRE) ---

    /**
     * Get all genres for the movie (Many-to-Many relationship).
     * Bảng trung gian (pivot) là MovieGenre.
     */
    public function genres(): BelongsToMany
    {
        // Tham số: Model đích, tên bảng pivot, khóa ngoại của model hiện tại, khóa ngoại của model đích
        return $this->belongsToMany(
            Genre::class,
            'MovieGenre', // Tên bảng trung gian
            'MovieID',    // Khóa ngoại của Movie trong bảng MovieGenre
            'GenreID'     // Khóa ngoại của Genre trong bảng MovieGenre
        );
    }

    /**
     * Get the pivot records in the MovieGenre table (HasMany relationship to the pivot model).
     * Tùy chọn, nếu bạn muốn truy cập trực tiếp vào bảng MovieGenre.
     */
    public function movieGenres(): HasMany
    {
        return $this->hasMany(MovieGenre::class, 'MovieID', 'MovieID');
    }
}