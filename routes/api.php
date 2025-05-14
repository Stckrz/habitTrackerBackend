<?php

use App\Http\Controllers\HabitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('habits', HabitController::class);

    Route::post(
        'habits/{habit}/mark-done',
        [HabitController::class, 'markDone']
    )->name('habits.markDone');

    Route::get(
        'habits/{habit}/status',
        [HabitController::class, 'status']
    )->name('habits.status');

    Route::get(
        'habits/{habit}/logs',
        [HabitController::class, 'logsInPeriod']
    )->name('habits.logsInPeriod');
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

//load auth routes
require __DIR__.'/auth.php';
