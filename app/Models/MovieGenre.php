<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

// Sử dụng Illuminate\Database\Eloquent\Relations\Pivot thay vì Model
class MovieGenre extends Pivot 
{
    use HasFactory;

    // Khai báo tên bảng
    protected $table = 'MovieGenre';
    
    // Tắt timestamps 
    public $timestamps = false;
    
    // Khai báo các cột là khóa chính (Composite Primary Key)
    protected $primaryKey = ['MovieID', 'GenreID'];
    public $incrementing = false; // Tắt tự tăng cho khóa chính kép
    
    protected $fillable = [
        'MovieID',
        'GenreID',
    ];
}