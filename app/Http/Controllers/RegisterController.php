<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\ChildrenInfo;
use App\Models\ParentInfo;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        // Create the user (parent) record
        $user = User::create([
            'name' => $request->parent_full_name,
            'email' => $request->parent_email,
            'phone' => $request->parent_phone,
            'school_id' => $request->child_school_id, // Use the same school as the child
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
        ]);

        // Create the parent record
        ParentInfo::create([
            'user_id' => $user->id,
            'full_name' => $request->parent_full_name,
            'home_address' => $request->parent_home_address,
        ]);

        // Create the child record
        $child = ChildrenInfo::create([
            'user_id' => $user->id,
            'school_id' => $request->child_school_id,
            'full_name' => $request->child_full_name,
            'dob' => $request->child_dob,
            'emergency_contact_number' => $request->child_emergency_contact_number,
        ]);

        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user,
            'child' => $child,
        ], 201);
    }
}