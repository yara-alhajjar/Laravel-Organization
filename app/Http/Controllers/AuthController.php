<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function registerAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6|confirmed'
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'admin' => $admin,
            'token' => $token
        ], 201);
    }

    
    public function registerManager(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:managers',
            'password' => 'required|min:6|confirmed',
            'number' => 'required|string',
            'location' => 'required|string',
        ]);

        $admin = auth()->user();

        $manager = Manager::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'number' => $request->number,
            'location' => $request->location,
            'admin_id' => $admin->id
        ]);

        $token = $manager->createToken('auth_token')->plainTextToken;

        return response()->json([
            'manager' => $manager,
            'token' => $token
        ], 201);
    }

    
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'admin' => $admin,
            'token' => $token
        ]);
    }

    
    public function loginManager(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $manager = Manager::where('email', $request->email)->first();

        if (!$manager || !Hash::check($request->password, $manager->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $manager->createToken('auth_token')->plainTextToken;

        return response()->json([
            'manager' => $manager,
            'token' => $token
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
