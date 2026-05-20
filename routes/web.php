<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\DashboardController;

// Welcome Redirect
Route::get('/', function () {
    return redirect()->route('modules.index');
});

// Library / Modules Routes
Route::get('/library', [ModuleController::class, 'index'])->name('modules.index');
Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
Route::get('/modules/{module}', [ModuleController::class, 'show'])->name('modules.show');

// Word Bank / Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Word Definition Operations (AJAX Lookup & Destruction)
Route::post('/words/define', [WordController::class, 'defineAndStoreWord'])->name('words.define');
Route::delete('/words/{id}', [WordController::class, 'destroy'])->name('words.destroy');

// Quiz Generation Engine Evaluation Routes
Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
Route::post('/quiz/check', [QuizController::class, 'checkAnswer'])->name('quiz.check');