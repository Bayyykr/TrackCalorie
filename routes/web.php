<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\AnswerForumController;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Forum Routes
Route::prefix('forum')->group(function () {
    Route::get('/', [ForumController::class, 'forum'])->name('forum.forum');
    Route::get('/filter', [ForumController::class, 'filterByTime'])->name('forum.filterByTime');
    Route::post('/', [ForumController::class, 'store'])->name('forum.store');
    // Route::post('/toggle-like', [ForumController::class, 'toggleLike'])->name('forum.toggleLike');
    Route::post('/forum/toggle-like', [ForumController::class, 'toggleLike'])->name('forum.toggleLike');
    Route::post('/{user}/follow', [ForumController::class, 'follow'])->name('forum.follow');
    Route::post('/{user}/unfollow', [ForumController::class, 'unfollow'])->name('forum.unfollow');
    // Route untuk AJAX create post
    Route::post('/store-ajax', [ForumController::class, 'storeAjax'])->name('forum.store.ajax');

    // Answer Forum Routes - dipindah ke dalam grup forum
    Route::get('/answer/{post}', [AnswerForumController::class, 'answer'])->name('forum.answer');
    Route::post('/answer/store', [AnswerForumController::class, 'storeAnswer'])->name('forum.answer.store');
    Route::post('/forum/posts/{post}/increment-views', [ForumController::class, 'incrementViews'])->name('forum.increment-views');
});

// Dashboard Route
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
