<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_category', 
        'file_size',
        'task_id'
    ];

    protected $appends = ['file_url']; 

    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    
    public function getFileCategoryAttribute()
    {
        $mimeType = $this->attributes['file_type'];

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } else {
            return 'document';
        }
    }
}
