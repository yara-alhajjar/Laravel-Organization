<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function store(Request $request, Task $task)
{
    $request->validate([
        'file' => 'sometimes|file|mimes:jpg,jpeg,png,mp4,mov,avi,pdf|max:20480',
    ]);

    if (!$request->hasFile('file')) {
        return response()->json(['message' => 'تمت العملية بدون إضافة ملف'], 200);
    }

    $file = $request->file('file');
    $fileName = time() . '_' . $file->getClientOriginalName();
    $filePath = $file->store('media');

    
    $fileCategory = 'document';
    if (str_starts_with($file->getClientMimeType(), 'image/')) {
        $fileCategory = 'image';
    } elseif (str_starts_with($file->getClientMimeType(), 'video/')) {
        $fileCategory = 'video';
    }

    $media = Media::create([
        'file_name' => $fileName,
        'file_path' => $filePath,
        'file_type' => $file->getClientMimeType(),
        'file_category' => $fileCategory, 
        'file_size' => $file->getSize(),
        'task_id' => $task->id
    ]);

    return response()->json($media, 201);
}


private function getFileCategory($mimeType)
{
    if (strpos($mimeType, 'image/') === 0) {
        return 'image';
    } elseif (strpos($mimeType, 'video/') === 0) {
        return 'video';
    } else {
        return 'document';
    }
}
}
