<?php

use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('chat_customer')->group(function () {
  Route::get('/{customerId}', [ChatBotController::class, 'getAllMessages']);
  Route::post('/send', [ChatBotController::class, 'sendMessage']);
  Route::post('/update/{id}', [ChatBotController::class, 'updateMessage']);
  Route::delete('/delete/{id}', action: [ChatBotController::class, 'deleteMessage']);
});
Route::get('/grouped', [AdminChatController::class, 'getGroupedMessages']);
Route::post('/send', [AdminChatController::class, 'sendMessage']);

Route::prefix('chat_admin')->group(function () {
    Route::get('/grouped', [AdminChatController::class, 'getGroupedMessages']);
    Route::post('/send', [AdminChatController::class, 'sendMessage']);
    Route::post('/update/{id}', [AdminChatController::class, 'updateMessage']);
    Route::delete('/delete/{id}', [AdminChatController::class, 'deleteMessage']);
    Route::post('/mark-read/{customerId}', [AdminChatController::class, 'markMessagesAsRead']);
});

Route::get('/employees', [TestController::class, 'index']);
Route::post('/employees', [TestController::class, 'store']);
Route::get('/employees/{id}', [TestController::class, 'show']);
Route::put('/employees/{id}', [TestController::class, 'update']);
Route::delete('/employees/{id}', [TestController::class, 'destroy']);

