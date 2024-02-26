<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Mail\MailNotify;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::post('/login', [AuthController::class, 'login'])->name('login');
// Protected routes
Route::get('/test', function () {
       
    Mail::to('annie.sws18@gmail.com')->send(new MailNotify());
    return response()->json(['message' => 'Email sent successfully.']);

});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
   
});