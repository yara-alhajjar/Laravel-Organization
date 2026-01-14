<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manager;

class ManagerController extends Controller
{
    public function index()
    {
        
        return Manager::withCount(['members', 'tasks'])->get();
    }

    public function show($id)
    {
        
        $manager = Manager::withCount(['members', 'tasks'])->findOrFail($id);
        return response()->json($manager);
    }

    public function update(Request $request, $id)
    {
        
        $manager = Manager::findOrFail($id);

        $manager->update($request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:managers,email,'.$id,
            'number' => 'sometimes|string',
            'location' => 'sometimes|string'
        ]));

        return response()->json($manager);
    }

    public function destroy($id)
    {

        return response()->json([
            'message' => 'Delete operation is not allowed for managers. You can only update their information.'
        ], 403);
    }
}
