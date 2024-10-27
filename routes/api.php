<?php

use App\Http\Controllers\MessageController;
use App\Http\Middleware\AuthenticateWithToken;
use Illuminate\Support\Facades\Route;

Route::middleware(AuthenticateWithToken::class)->group(function () {
    Route::post('/messages/{user_id}/send', [MessageController::class, 'create']);
    Route::get('/messages/{user_id}/list', [MessageController::class, 'index']);
    Route::post('/messages/{message_id}/mark-read', [MessageController::class, 'markAsRead']);
    Route::get('/messages/{user_id}/sync', [MessageController::class, 'sync']);
});
