<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'main']);

Route::get('about', [PageController::class, 'about'])->name('about');

Route::get('services', [PageController::class, 'services'])->name('services');

Route::get('projeckt', [PageController::class, 'projeck'])->name('projeck');

Route::get('blog', [PageController::class, 'blog'])->name('blog');

Route::get('signal', [PageController::class, 'signal'])->name('signal');

Route::get('contact', [PageController::class, 'contact'])->name('contact');


// Route::get('posts', [PostController::class, 'index'])->name('posts.index');

// Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');

// Route::post('posts/create', [PostController::class, 'store'])->name('posts.store');

// Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');

// Route::put('posts/{post}/edit', [PostController::class, 'update'])->name('posts.update');

// Route::delete('posts/{post}/destroy', [PostController::class, 'destroy'])->name('posts.destroy');

Route::resource('posts', PostController::class);
