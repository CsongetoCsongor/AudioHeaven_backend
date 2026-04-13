<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\QueueItemController;
use App\Http\Controllers\ListeningHistoryItemController;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\PasswordResetController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [VerifyEmailController::class, 'resendNotification'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');


Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

// WITH FRONTEND:
Route::get('/reset-password/{token}', function (string $token) {
    return redirect('http://localhost:8080/reset?token=' . $token);
})->name('password.reset');

// // WITHOUT FRONTEND:
// Route::get('/reset-password/{token}', function (string $token, Request $request) {
//     // Csak visszaadjuk a tokent és az emailt, hogy be tudd másolni a Postmanbe
//     return response()->json([
//         'message' => 'Copy this token to your password reset request',
//         'token' => $token,
//         'email' => $request->query('email')
//     ]);
// })->name('password.reset');


Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/play/{id}', [SongController::class, 'play']);

Route::get('/users/random', [UserController::class, 'random']);

Route::get('/users/{id}', [UserController::class, 'show']);

Route::get('/songs', [SongController::class, 'index']);

Route::get('/songs/random', [SongController::class, 'random']);

Route::get('/songs/new', [SongController::class, 'getNewSongs']);

Route::get('/songs/{id}', [SongController::class, 'show']);

Route::get('/albums', [AlbumController::class, 'index']);

Route::get('/albums/random', [AlbumController::class, 'random']);

Route::get('/users', [UserController::class, 'index']);

Route::get('/albums/{id}', [AlbumController::class, 'show']);

Route::get('/users/{id}/songs', [SongController::class, 'listByUser']);

Route::get('/users/{id}/albums', [AlbumController::class, 'listByUser']);

Route::middleware('auth:sanctum')->group(function () {

    Route::delete('/user', [UserController::class, 'destroy']);

    Route::post('/user', [UserController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/playlists', [PlaylistController::class, 'index']);

    Route::post('/playlists', [PlaylistController::class, 'store']);


    Route::get('/playlists/{id}', [PlaylistController::class, 'show']);


    Route::post('/playlists/{id}/songs/{songId}', [PlaylistController::class, 'addSong']);

    Route::delete('/playlists/{id}/songs/{songId}', [PlaylistController::class, 'removeSong']);

    Route::delete('/playlists/{id}', [PlaylistController::class, 'destroy']);

    Route::post('/songs/{id}/log-play', [SongController::class, 'logPlay']);

    Route::post('/songs', [SongController::class, 'store']);

    Route::delete('/songs/{id}', [SongController::class, 'destroy']);

    Route::post('/songs/{id}', [SongController::class, 'update']);

    Route::post('/albums', [AlbumController::class, 'store']);

    Route::post('/albums/{id}', [AlbumController::class, 'update']);

    Route::delete('/albums/{id}', [AlbumController::class, 'destroy']);

    Route::get('/queue', [QueueItemController::class, 'index']);

    Route::post('/queue/store-many', [QueueItemController::class, 'storeMany']);

    Route::post('/queue/{songId}', [QueueItemController::class, 'store']);

    Route::get('/queue/position/{position}', [QueueItemController::class, 'showByPosition']);

    Route::delete('/queue/position/{position}', [QueueItemController::class, 'destroyByPosition']);

    Route::patch('/queue/move/{oldPosition}/to/{newPosition}', [QueueItemController::class, 'updatePositionByPositions']);

    Route::delete('/queue/clear', [QueueItemController::class, 'clear']);

    Route::get('/history', [ListeningHistoryItemController::class, 'index']);

    Route::get('/me', [UserController::class, 'me']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::delete('/admin/users/{id}', [UserController::class, 'destroyById']);
});
