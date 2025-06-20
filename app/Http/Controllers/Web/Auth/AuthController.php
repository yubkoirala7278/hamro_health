<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index()
    {
        try {
            return view("auth.login");
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }


    // web login
    public function login(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Attempt login
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                // Login successful
                $request->session()->regenerate();
                return redirect()->route('admin.home')->with('success', 'Login successful!');
            }

            // Failed login
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->withInput();
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    // web logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success',"logout success!");
    }
}
