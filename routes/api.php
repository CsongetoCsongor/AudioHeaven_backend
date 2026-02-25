<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\PlaylistController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/play/{id}', [SongController::class, 'play']);

Route::get('/users/{id}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/playlists', [PlaylistController::class, 'store']);

    Route::post('/songs/{id}/log-play', [SongController::class, 'logPlay']);
    
    Route::post('/songs', [SongController::class, 'store']);

    Route::post('/albums', [AlbumController::class, 'store']);

    Route::get('/users/{id}/songs', [SongController::class, 'listByUser']);


    // Route::get('/me', function (Request $request) {
    //     return $request->user();
    // });
});
