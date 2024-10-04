<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;

// User registration and login routes
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');

// Authenticated user route
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Post and related actions routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);

    Route::apiResource('posts', PostController::class);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::post('/posts/{post}/toggle-like', [LikeController::class, 'toggleLike']);


});