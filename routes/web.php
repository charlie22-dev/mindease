<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\GuestChatController;
use App\Http\Controllers\MoodController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\GoogleController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::post('/guest-chat', [GuestChatController::class, 'send'])
    ->middleware('throttle:30,1')
    ->name('guest.chat');

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat',        [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send',  [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/clear', [ChatController::class, 'clear'])->name('chat.clear');

    Route::get('/mood',        [MoodController::class, 'index'])->name('mood.index');
    Route::post('/mood',       [MoodController::class, 'store'])->name('mood.store');
});

// Add this to routes/web.php inside the auth middleware group:
Route::get('/dashboard', function () {
    return redirect()->route('chat.index');
})->name('dashboard');

require __DIR__.'/auth.php';