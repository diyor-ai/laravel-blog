<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('main');
// });


Route::get('/', [PageController::class, 'main']);

Route::get('about', [PageController::class, 'about'])->name('about');
