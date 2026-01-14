<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'donor', 'admin_id'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }
}
