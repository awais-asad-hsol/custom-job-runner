<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

Route::get('/', function () {
    return view('welcome');
});
// routes/web.php
Route::get('/run-job-helper-function', [JobController::class, 'runJobUsingHelperFunction']);
Route::get('/run-job-service', [JobController::class, 'runJobDirectly']);    
