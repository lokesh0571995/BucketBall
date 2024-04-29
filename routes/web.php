<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BallBucketController;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [BallBucketController::class, 'index']);
Route::post('/add-bucket', [BallBucketController::class, 'addBucket']);
Route::post('/add-ball', [BallBucketController::class, 'addBall']);
Route::post('/distribute-balls', [BallBucketController::class, 'distributeBalls']);
Route::get('/ball-names', [BallBucketController::class, 'suggestedBallCount']);