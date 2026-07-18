<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JukirApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/v1/jukirs', [JukirApiController::class, 'index']);
Route::get('/v1/jukirs/public-stats', [JukirApiController::class, 'publicStats']);
Route::get('/v1/jukirs/{id}', [JukirApiController::class, 'show']);
Route::post('/v1/complaints/sync', [JukirApiController::class, 'syncComplaint']);
Route::put('/v1/complaints/sync/{report_code}', [JukirApiController::class, 'updateComplaintSync']);
