<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'user_type'
        // 'user_type', // nếu bạn có cột này thì bỏ comment và thêm vào
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Tự hash khi gán mật khẩu (dùng cho register/seed)
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] =
            Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    // === JWTSubject ===
    public function getJWTIdentifier() { return $this->getKey(); }
    public function getJWTCustomClaims() { return []; }
}
