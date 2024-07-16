<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'main']);
Route::get('about', [PageController::class, 'about'])->name('about');
Route::get('services', [PageController::class, 'services'])->name('services');
Route::get('projeckt', [PageController::class, 'projeck'])->name('projeck');
Route::get('signal', [PageController::class, 'signal'])->name('signal');
Route::get('contact', [PageController::class, 'contact'])->name('contact');


Route::resources([
    'posts' => PostController::class,
    'comments' => CommentController::class,
]);
