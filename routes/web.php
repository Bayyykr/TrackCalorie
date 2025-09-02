<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AnswerForumController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Auth\CalculatorController;
use App\Http\Controllers\Auth\RecomendationsController;
use App\Http\Controllers\Auth\HomePageController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\CalorieController;
use App\Http\Controllers\Controller;

// Route::get('/', function () {
//     return view('auth.login');
// });
Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/recomend', [RecomendationsController::class, 'showRecommendations'])
    ->name('recomend')
    ->middleware('auth');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::delete('/recomend/{menu}', [RecomendationsController::class, 'destroyMenu'])
    ->middleware('auth');
Route::get('/homepage', [HomePageController::class, 'showHomepage'])
    ->name('homepage')
    ->middleware('auth');
Route::get('/calculator', [CalculatorController::class, 'showFormCalculator'])->name('calculator');
Route::post('/calculator', [CalculatorController::class, 'calculate'])->name('calculator.calculate');
Route::post('/save-calc', [CalculatorController::class, 'save'])->name('calc.save');
Route::get('/calculate-monitor', [CalculatorController::class, 'monitor'])->name('calculate.monitor');
Route::get('/calorie-tracker', [CalorieController::class, 'index'])->name('calorie.tracker');
Route::post('/api/search-menu', [CalorieController::class, 'searchMenu'])->name('api.search.menu');
Route::post('/api/add-calories', [CalorieController::class, 'addCalories'])->name('api.add.calories');
Route::get('/calorie-history', [CalorieController::class, 'getCalorieHistory'])->name('calorie.history');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::prefix('forum')->group(function () {
    Route::get('/', [ForumController::class, 'forum'])->name('forum.forum');
    Route::get('/filter', [ForumController::class, 'filterByTime'])->name('forum.filterByTime');
    Route::post('/', [ForumController::class, 'store'])->name('forum.store');
    // Route::post('/toggle-like', [ForumController::class, 'toggleLike'])->name('forum.toggleLike');
    Route::post('/toggle-like', [ForumController::class, 'toggleLike'])->name('forum.toggleLike');
    Route::post('/{user}/follow', [ForumController::class, 'follow'])->name('forum.follow');
    Route::post('/{user}/unfollow', [ForumController::class, 'unfollow'])->name('forum.unfollow');
    // Route untuk AJAX create post
    Route::post('/store-ajax', [ForumController::class, 'storeAjax'])->name('forum.store.ajax');

    // Answer Forum Routes - dipindah ke dalam grup forum
    Route::get('/answer/{post}', [AnswerForumController::class, 'answer'])->name('forum.answer');
    Route::post('/answer/store', [AnswerForumController::class, 'storeAnswer'])->name('forum.answer.store');
    Route::post('/forum/posts/{post}/increment-views', [ForumController::class, 'incrementViews'])->name('forum.increment-views');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload');
});
