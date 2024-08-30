<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/users/{id}/post', [PostController::class, 'getUserPostbyId']);
Route::get('/post', [PostController::class, 'getPost']);
Route::delete('/users/{id}', [UserController::class, 'deleteUser']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/addpost', [PostController::class, 'addPost']);
    Route::get('/postbyauth', [PostController::class, 'getUserPostbyAuth']);
});
