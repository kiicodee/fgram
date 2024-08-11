<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\FollowController;
use App\Http\Controllers\api\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix("v1")->group(function () { 
    Route::prefix("auth")->group(function () {
        Route::post('register', [AuthController::class,'register']);
        Route::post('login', [AuthController::class,'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class,'logout']);
        });
    });
});

Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('posts', [PostController::class,'post']);
        Route::delete('posts/{id}', [PostController::class,'delete']);
        Route::get('posts', [PostController::class,'show']);

        Route::post('users/{username}/follow', [FollowController::class,'follow']);
        Route::delete('users/{username}/unfollow', [FollowController::class,'unfollow']);
        Route::get('users/{username}/following', [FollowController::class,'following']);
        Route::get('users/{username}/followers', [FollowController::class,'follower']);
        Route::put('users/{username}/accept', [FollowController::class,'accept']);


        Route::get('users', [AuthController::class,'user']);
        Route::get('users/{username}', [AuthController::class,'detail']);







        // Route::get('users', [UserController::class,'index']);
    });
});



