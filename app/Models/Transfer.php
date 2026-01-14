<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'donation_id', 'manager_id'];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }
}
