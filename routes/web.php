<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;


Route::view('/admin', 'admin')->name('admin');

Route::get('/', [PostController::class, 'indexshow'])->name('page');

Route::resource('posts', PostController::class);
