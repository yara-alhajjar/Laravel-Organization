<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
    
        if (auth()->user() instanceof \App\Models\Admin) {
            return Member::with('manager')->get();
        }

    
        $managerId = auth()->id();
        return Member::where('manager_id', $managerId)->get();
    }


    public function store(Request $request)
    {
        $managerId = auth()->id();

        $request->validate([
            'name' => 'required|string',
            'number' => 'required|string|unique:members'
        ]);


        $member = Member::create([
            'name' => $request->name,
            'number' => $request->number,
            'manager_id' => $managerId
        ]);

        return response()->json($member, 201);
    }

    public function update(Request $request, $id)
    {
        $managerId = auth()->id();
        $member = Member::where('manager_id', $managerId)->findOrFail($id);

        $member->update($request->validate([
            'name' => 'sometimes|string',
            'number' => 'sometimes|string'
        ]));

        return response()->json($member);
    }

    public function destroy($id)
    {
        $managerId = auth()->id();
        $member = Member::where('manager_id', $managerId)->findOrFail($id);

        $member->delete();

        return response()->json(['message' => 'Member deleted successfully']);
    }

    public function getManagerMembers()
    {
        $managerId = auth()->id();
        return Member::where('manager_id', $managerId)->get();
    }
}
