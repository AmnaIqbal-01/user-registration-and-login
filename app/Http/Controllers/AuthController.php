<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Notifiable;

class AuthController extends Controller
{
    public function signup(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|string|email|unique:users',
        'password' => 'required|string|confirmed'
    ]);

    try {
        $user = User::create([
            'name'=> $request->name,
            'email' => $request->email,
            'password'=> Hash::make($request->password)
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'User successfully registered', 'user' => $user]);
    }
     catch (\Exception $e) {
        return response()->json(['message' => 'Failed to register user', 'error' => $e->getMessage()], 500);
    }
}


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Check if a user with the specified email exists
        $user = User::where('email', $request->email)->first();

        // If user exists and passwords match
        if ($user && Hash::check($request->password, $user->password)) {
            // Create a new token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User successfully logged in',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }

        // Authentication failed
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

   
}
