<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SongController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/play/{id}', [SongController::class, 'play']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/songs', [SongController::class, 'store']);

    Route::get('/users/{id}/songs', [SongController::class, 'listByUser']);

    // Route::get('/me', function (Request $request) {
    //     return $request->user();
    // });
});
