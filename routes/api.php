<?php

use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\FeelingController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\MusicController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "customer", "middleware" => ["appauth"]], function () {
    // Route::post('/login', [CustomerController::class, 'login']);
    // Route::post('/find-customer-by-email', [CustomerController::class, 'findMe']);
    // Route::post('verify-otp', [CustomerController::class, 'verifyOTP']);
    // Route::post('register-by-email', [CustomerController::class, 'registerByEmail']);
    // Route::post('/signup', [CustomerController::class, 'register']);
    // Route::post('/update/profile', [CustomerController::class, 'updateProfile']);
    // Route::post('/send-password-reset-otp', [CustomerController::class, 'passwordReset']);
    // Route::post('/verify-signup', [CustomerController::class, 'verifySignup']);
    // Route::post('/get-notify', [CustomerController::class, 'getNotify']);
    Route::post('/login', [CustomerController::class, 'login']);
    Route::post('/register', [CustomerController::class, 'register']);
    Route::post('/register/email', [CustomerController::class, 'registerByEmail']);

    // Verification / OTP
    Route::post('/verify-otp', [CustomerController::class, 'verifyOTP']);
    Route::post('/verify-signup', [CustomerController::class, 'verifySignup']);
    Route::post('/send-password-reset', [CustomerController::class, 'passwordReset']);

    // Customer info
    Route::post('/profile/update', [CustomerController::class, 'updateProfile']);
    Route::get('/notify', [CustomerController::class, 'getNotify']);
    Route::post('/notify/update', [CustomerController::class, 'updateNotify']);

    // Lookup
    Route::post('/find-by-email', [CustomerController::class, 'findMe']);


    // Meditation & Sleep
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::get('/lessons/{id}', [LessonController::class, 'show']);
    Route::post('/lessons/{id}/complete', [LessonController::class, 'complete']);
    Route::get('/get-single-course', [CourseController::class, 'getSingleCourse']);
    Route::get('/get-category', [CourseController::class, 'getCategory']);
    Route::get('/get-sleep-course', [CourseController::class, 'getSleepCourse']);
    Route::get('/get-sleep-screen', [CourseController::class, 'getSleepScreen']);


    // Journal, Music & Journey need
    Route::get('/get-music', [MusicController::class, 'getMusic']);
    Route::get('/get-emoji', [FeelingController::class, 'getEmoji']);
    // Route::resource('/journal', JournalController::class); // store get 
    Route::get('/get-links', [CourseController::class, 'getLinks']);

    // Counseling Session need
    // Route::get('/get-paginated-counselors-data', [CounselorController::class, 'getCounselorsPagination']);
    // Route::get('/counselor/calendar', [CounselorController::class, 'getCalendarAvailability']);
    // Route::get('/available-slots', [BookingController::class, 'getAvailableSlots']);
    // Route::post('/book-slot', [BookingController::class, 'bookSlot']);
    // Route::post('/reschedule-slot', [BookingController::class, 'rescheduleSlot']);
    // Route::post('/cancel-booking', [BookingController::class, 'cancelBooking']);
    // Route::get('/upcoming-sessions', [CounselorController::class, 'getCustomerUpcomingSessions']);
    // Route::get('/preferences', [UserPreferenceController::class, 'index']);
    // Route::get('/get-preference-info', [CounselorController::class, 'getPreferenceInfo']);
    // Route::post('/preferences', [UserPreferenceController::class, 'store']);
    // Route::post('/reserved-slot', [BookingController::class, 'reservedSlot']);
    // Route::delete('/preferences', [UserPreferenceController::class, 'destroy']);



    // Booking routes





});

// Home & Content
Route::get('/get-home', [HomeController::class, 'getHome']);
Route::get('/get-home-emoji', [HomeController::class, 'getHomeEmoji']);
Route::get('/get-quote', [HomeController::class, 'getQuote']);
Route::get('/course/{id}/{course_order_by}', [HomeController::class, 'getCourse']);
Route::get('/get-user/{id}', [HomeController::class, 'getUser']);
Route::get('/get-home-sleep-audio/{id}/{date}', [HomeController::class, 'getHomeSleepAudio']);
Route::get('/get-random-course', [HomeController::class, 'getRandomCourse']);

// Legacy / Utility need
// Route::post('/check-version', [\App\Http\Controllers\api\AppVersionController::class, 'check']);
Route::post('/get-code-information', [CustomerController::class, 'getCodeInformation']);
Route::post('/goalupdate/{email}/goal/{goal_id}', [CustomerController::class, 'updateGoalIdByEmail']);
