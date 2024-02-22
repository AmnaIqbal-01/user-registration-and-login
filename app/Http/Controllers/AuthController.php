<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{
  public function signup(Request $request){

    $request->validate([
        'name' => 'required|string',
        'email' => 'required|string|email|unique:users',
        'password' => 'required|string|confirmed'
    ]);
      
    $user =User::create([
     'name'=> $request->name,
     'email' => $request->email,
     'password'=>Hash::make($request->password)
    ]);

    event(new Registered($user));

    return response()->json(['message'=>'User successfully registered','user'=>$user]);

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

  public function resendVerificationEmail(Request $request)
{
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.']);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent.']);
}

  
}
