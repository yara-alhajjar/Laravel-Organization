<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Manager extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['name', 'email', 'password', 'number', 'location', 'admin_id'];

    
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    
    public function members() {
        return $this->hasMany(Member::class);
    }


    public function tasks() {
        return $this->hasMany(Task::class);
    }
}
