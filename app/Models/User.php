<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'email', 'password', 'role', 'area_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';

    public function isAdmin() {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager() {
        return $this->role === self::ROLE_MANAGER;
    }
    public function area()
{
    return $this->belongsTo(\App\Models\Area::class);
}
}   
