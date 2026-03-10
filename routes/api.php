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

Route::get('/users/random', [UserController::class, 'random']);

Route::get('/users/{id}', [UserController::class, 'show']);

Route::get('/songs', [SongController::class, 'index']);

Route::get('/albums', [AlbumController::class, 'index']);

Route::get('/users', [UserController::class, 'index']);

Route::get('/songs/random', [SongController::class, 'random']);

Route::get('/albums/random', [AlbumController::class, 'random']);


Route::middleware('auth:sanctum')->group(function () {

    Route::delete('/user', [UserController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/playlists', [PlaylistController::class, 'index']);

    Route::post('/playlists', [PlaylistController::class, 'store']);

    Route::post('/playlists/{id}/songs/{songId}', [PlaylistController::class, 'addSong']);

    Route::delete('/playlists/{id}/songs/{songId}', [PlaylistController::class, 'removeSong']);

    Route::post('/songs/{id}/log-play', [SongController::class, 'logPlay']);

    Route::post('/songs', [SongController::class, 'store']);

    Route::post('/songs/{id}', [SongController::class, 'update']);

    Route::post('/albums', [AlbumController::class, 'store']);

    Route::get('/users/{id}/songs', [SongController::class, 'listByUser']);


    Route::get('/me', [UserController::class, 'me']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::delete('/admin/users/{id}', [UserController::class, 'destroyById']);
});