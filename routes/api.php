<?php

use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "customer", "middleware" => ["appauth"]], function () {
    Route::post('/login', [CustomerController::class, 'login']);
    Route::post('/find-customer-by-email', [CustomerController::class, 'findMe']);
    Route::post('verify-otp', [CustomerController::class, 'verifyOTP']);
    Route::post('register-by-email', [CustomerController::class, 'registerByEmail']);
    Route::post('/signup', [CustomerController::class, 'register']);
    Route::post('/send-password-reset-otp', [CustomerController::class, 'passwordReset']);
    Route::post('/verify-signup', [CustomerController::class, 'verifySignup']);
    Route::post('/get-notify', [CustomerController::class, 'getNotify']);



    // Meditation & Sleep
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::get('/lessons/{id}', [LessonController::class, 'show']);
    Route::post('/lessons/{id}/complete', [LessonController::class, 'complete']);
    Route::get('/get-single-course', [CourseController::class, 'getSingleCourse']);
    Route::get('/get-category', [CourseController::class, 'getCategory']);
    Route::get('/get-sleep-course', [CourseController::class, 'getSleepCourse']);
    Route::get('/get-sleep-screen', [CourseController::class, 'getSleepScreen']);
});

// Home & Content
Route::get('/get-home', [HomeController::class, 'getHome']);
Route::get('/get-home-emoji', [HomeController::class, 'getHomeEmoji']);
Route::get('/get-quote', [HomeController::class, 'getQuote']);
Route::get('/course/{id}/{course_order_by}', [HomeController::class, 'getCourse']);
Route::get('/get-user/{id}', [HomeController::class, 'getUser']);
Route::get('/get-home-sleep-audio/{id}/{date}', [HomeController::class, 'getHomeSleepAudio']);
Route::get('/get-random-course', [HomeController::class, 'getRandomCourse']);
