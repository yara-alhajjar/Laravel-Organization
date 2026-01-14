<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Member;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        if (auth()->user() instanceof \App\Models\Admin) {
            $tasks = Task::with(['manager', 'members', 'media'])->get();
        } else {
            $managerId = auth()->id();
            $tasks = Task::with(['members', 'media'])->where('manager_id', $managerId)->get();
        }

        
        return $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'name' => $task->name,
                'start_date' => $task->start_date,
                'end_date' => $task->end_date,
                'completion_percentage' => $task->completion_percentage,
                'challenges' => $task->challenges,
                'manager_id' => $task->manager_id,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
                'members' => $task->members,
                'media' => $task->media_with_urls, 
                'manager' => $task->manager
            ];
        });
    }

    public function store(Request $request)
    {
        $managerId = auth()->id();

        $request->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'challenges' => 'nullable|string',
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:members,id',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480'
        ]);

        $task = Task::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'completion_percentage' => $request->completion_percentage,
            'challenges' => $request->challenges,
            'manager_id' => $managerId
        ]);

        $task->members()->attach($request->member_ids);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('media', $fileName);

                Media::create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'task_id' => $task->id
                ]);
            }
        }

        return response()->json($task->load(['members', 'media']), 201);
    }

    public function update(Request $request, $id)
    {
        $managerId = auth()->id();
        $task = Task::where('manager_id', $managerId)->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'completion_percentage' => 'sometimes|integer|min:0|max:100',
            'challenges' => 'nullable|string',
            'member_ids' => 'sometimes|array',
            'member_ids.*' => 'exists:members,id',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4,mov,avi|max:20480'
        ]);

        $task->update($request->except(['member_ids', 'media']));

        if ($request->has('member_ids')) {
            $task->members()->sync($request->member_ids);
        }

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->store('media');

                Media::create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'task_id' => $task->id
                ]);
            }
        }

        return response()->json($task->load(['members', 'media']));
    }

    public function destroy($id)
    {
        $managerId = auth()->id();
        $task = Task::where('manager_id', $managerId)->findOrFail($id);

        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }

    public function show($id)
    {
        $task = Task::with(['members', 'media'])->findOrFail($id);

        if (auth()->user() instanceof \App\Models\Manager && $task->manager_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json($task);
    }
}

