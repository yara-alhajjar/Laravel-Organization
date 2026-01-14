<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'completion_percentage',
        'challenges',
        'manager_id'
    ];

    
    protected $appends = ['media_with_urls'];

    
    public function manager()
    {
        return $this->belongsTo(Manager::class);
    }


    public function members()
    {
        return $this->belongsToMany(Member::class, 'task_member');
    }

    
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    
    public function getMediaWithUrlsAttribute()
    {
        return $this->media->map(function ($media) {
            return [
                'id' => $media->id,
                'file_name' => $media->file_name,
                'file_path' => $media->file_path,
                'file_type' => $media->file_type,
                'file_category' => $media->file_category,
                'file_url' => $media->file_url, 
                'file_size' => $media->file_size,
                'task_id' => $media->task_id,
                'created_at' => $media->created_at,
                'updated_at' => $media->updated_at
            ];
        });
    }
}
