<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        if ($request->type === 'email') {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
        } elseif ($request->type === 'phone') {
            if ($request->otp !== '123456') {
                return response()->json(['message' => 'Invalid OTP'], 401);
            }
            $user = User::where('phone', $request->phone)->first();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            // Revoke the current token used in the request
            $user->currentAccessToken()->delete();

            return response()->json(['message' => 'Logout successful'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error during logout', 'error' => $e->getMessage()], 500);
        }
    }
}
