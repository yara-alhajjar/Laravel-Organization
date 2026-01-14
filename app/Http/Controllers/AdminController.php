<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Models\Member;
use App\Models\Task;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $managers = Manager::withCount(['members', 'tasks'])->get();

        return response()->json([
            'total_managers' => Manager::count(),
            'total_members' => Member::count(),
            'total_tasks' => Task::count(),
            'managers' => $managers
        ]);
    }

    public function getAllMembers()
    {
        $members = Member::with('manager')->get();
        return response()->json($members);
    }

    public function getAllTasks()
    {
        $tasks = Task::with(['manager', 'members'])->get();
        return response()->json($tasks);
    }
}
