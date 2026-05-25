<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ModuleController;

Route::get('/dashboard', [WordController::class, 'index'])->name('dashboard');

// Module routes
Route::get('/library', [ModuleController::class, 'index'])->name('library');
Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');
Route::get('/modules/{module}/read', [ModuleController::class, 'show'])->name('modules.show');
Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

// Word definition + management
Route::post('/words/define', [WordController::class, 'defineAndStoreWord'])->name('words.define');
Route::delete('/words/{id}', [WordController::class, 'destroy'])->name('words.destroy');

// Quiz
Route::get('/quiz', [QuizController::class, 'index'])->name('quiz.index');
Route::post('/quiz/check', [QuizController::class, 'check'])->name('quiz.check');