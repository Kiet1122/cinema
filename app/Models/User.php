<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'User';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'UserID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Email',
        'Password',
        'Role',
        'Status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'Password',
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
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * Chỉ định trường dùng để xác thực thay vì 'email'.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'Email';
    }

    /**
     * Chỉ định trường mật khẩu dùng để xác thực thay vì 'password'.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->Password;
    }
    // =================================== QUAN HỆ ===================================

    /**
     * Lấy bản ghi customer liên quan đến user này.
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'UserID', 'UserID');
    }

    /**
     * Lấy bản ghi manager liên quan đến user này.
     */
    public function manager(): HasOne
    {
        return $this->hasOne(Manager::class, 'UserID', 'UserID');
    }
}