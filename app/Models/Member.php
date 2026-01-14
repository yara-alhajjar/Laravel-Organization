<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'number', 'manager_id'];

    
    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }

    
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_member');
    }
}
