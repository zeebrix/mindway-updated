<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CategoryCoursesController;
use App\Http\Controllers\Admin\CounselorsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CSMController;
use App\Http\Controllers\Admin\CoursesController;
use App\Http\Controllers\Admin\CoursesAudioController;
use App\Http\Controllers\Admin\FeelingsController;
use App\Http\Controllers\Admin\HomeEmojisController;
use App\Http\Controllers\Admin\LinksController;
use App\Http\Controllers\Admin\MusicController;
use App\Http\Controllers\Admin\ProgramsController;
use App\Http\Controllers\Admin\QuotesController;
use App\Http\Controllers\Admin\RequestSessionsController;
use App\Http\Controllers\Admin\SingleCoursesController;
use App\Http\Controllers\Admin\SleepAudiosController;
use App\Http\Controllers\Admin\SleepScreensController;
use App\Http\Controllers\Admin\SosAudiosController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Counselor\SessionController;
use App\Http\Controllers\CounselorController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProgramController;

/*
|--------------------------------------------------------------------------
| Web Routes - Revamped for Mindway
|--------------------------------------------------------------------------
| This file follows a strict, resource-oriented structure.
| Naming: {actor}.{resource}.{action}
| Example: admin.counsellors.index
*/


//==============================================================================
// GUEST & AUTHENTICATION ROUTES
//==============================================================================

//--- Public-facing static pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/terms-of-use', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');

//--- Authentication Routes (Login, Logout, 2FA, Password Reset)
Route::controller(AuthController::class)->group(function () {
    // Login Routes
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.attempt')->middleware('throttle:5,1');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');

    // Google OAuth
    Route::get('/auth/google/redirect', 'redirectToGoogle')->name('auth.google.redirect');
    Route::get('/auth/google/callback', 'handleGoogleCallback')->name('auth.google.callback');

    // Two-Factor Authentication
    Route::get('/verify-2fa', 'show2fa')->name('2fa.form')->middleware('auth');
    Route::post('/verify-2fa', 'verify2fa')->name('2fa.verify')->middleware(['auth', 'throttle:5,1']);


    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    // Reset Password
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});




//==============================================================================
// ADMIN PORTAL
//==============================================================================
// 'can:access-admin-panel'
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:access-admin-panel'])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Customer only route named as users
    Route::resource('/users', UserController::class)->names('users');
    Route::get('/users-data', [UserController::class, 'getData'])->name('users.data');

    // CSM Role from User Table
    Route::resource('/csm', CSMController::class)->names('csm');
    Route::get('/csm-data', [CSMController::class, 'getData'])->name('csm.data');


    // Program
    Route::resource('/programs', ProgramsController::class)->names('programs');
    Route::get('/programs-data', [ProgramsController::class, 'getData'])->name('programs.data');
    Route::post('/programs/{program}/status', [ProgramsController::class, 'updateStatus'])->name('programs.status');
    Route::post('/programs/{program}/status', [ProgramsController::class, 'updateStatus'])->name('programs.status');
    Route::get('/programs/{program}/reset-sessions', [ProgramsController::class, 'resetMaxSessions'])->name('programs.reset-sessions');

    // counselors
    Route::resource('/counselors', CounselorsController::class)->names('counselors');
    Route::get('/counselors-data', [CounselorsController::class, 'getData'])->name('counselors.data');

    Route::get('/counselors/{counselor}/availability', [CounselorsController::class, 'availability'])->name('counselors.availability');
    Route::post('/counselors/{counselor}/availability', [CounselorsController::class, 'saveAvailability'])->name('counselors.saveAvailability');

    // request-sessions
    Route::resource('/request-sessions', RequestSessionsController::class)->only(['index', 'show'])->names('request-sessions');
    Route::get('/request-sessions-data', [RequestSessionsController::class, 'getData'])->name('request-sessions.data');
    Route::post('/request-sessions/{request_session}/approve', [RequestSessionsController::class, 'approve'])->name('request-sessions.approve');
    Route::post('/request-sessions/{request_session}/deny', [RequestSessionsController::class, 'deny'])->name('request-sessions.deny');

    // Courses
    Route::resource('/courses', CoursesController::class)->names('courses');
    Route::get('/courses-data', [CoursesController::class, 'getData'])->name('courses.data');

    // Courses Audio
    Route::resource('/courses-audio', CoursesAudioController::class)->names('courses-audio');
    Route::get('/courses-audio-data', [CoursesAudioController::class, 'getData'])->name('courses-audio.data');

    // SosAudios
    Route::resource('/sos-audios', SosAudiosController::class)->names('sos-audios');
    Route::get('/sos-audios-data', [SosAudiosController::class, 'getData'])->name('sos-audios.data');

    // Category
    Route::resource('/categories', CategoriesController::class)->names('categories');
    Route::get('/categories-data', [CategoriesController::class, 'getData'])->name('categories.data');

    // category-courses    
    Route::resource('/category-courses', CategoryCoursesController::class)->names('category-courses');
    Route::get('/category-courses-data', [CategoryCoursesController::class, 'getData'])->name('category-courses.data');

    // sleep-audios
    Route::resource('/sleep-audios', SleepAudiosController::class)->names('sleep-audios');
    Route::get('/sleep-audios-data', [SleepAudiosController::class, 'getData'])->name('sleep-audios.data');

    // links
    Route::resource('/links', LinksController::class)->names('links');
    Route::get('/links-data', [LinksController::class, 'getData'])->name('links.data');

    // feelings
    Route::resource('/feelings', FeelingsController::class)->names('feelings');
    Route::get('/feelings-data', [FeelingsController::class, 'getData'])->name('feelings.data');

    // music
    Route::resource('/music', MusicController::class)->names('music');
    Route::get('/music-data', [MusicController::class, 'getData'])->name('music.data');

    // sleep-screens
    Route::resource('/sleep-screens', SleepScreensController::class)->names('sleep-screens');
    Route::get('/sleep-screens-data', [SleepScreensController::class, 'getData'])->name('sleep-screens.data');

    // home-emojis
    Route::resource('/home-emojis', HomeEmojisController::class)->names('home-emojis')->parameter('home-emojis', 'homeEmoji');
    Route::get('/home-emojis-data', [HomeEmojisController::class, 'getData'])->name('home-emojis.data');

    // single-courses
    Route::resource('/single-courses', SingleCoursesController::class)->names('single-courses');
    Route::get('/single-courses-data', [SingleCoursesController::class, 'getData'])->name('single-courses.data');

    // quotes
    Route::resource('/quotes', QuotesController::class)->names('quotes');
    Route::get('/quotes-data', [QuotesController::class, 'getData'])->name('quotes.data');
});


//==============================================================================
// COUNSELLOR PORTAL
//==============================================================================
Route::prefix('counselor')->name('counsellor.')->middleware(['auth', 'can:access-counsellor-panel'])->group(function () {
    Route::get('/dashboard', [CounselorController::class, 'dashboard'])->name('dashboard');
    Route::resource('/sessions', SessionController::class)->names('sessions');
    Route::get('/session-history', [SessionController::class, 'history'])->name('sessions.history');
    Route::get('/get-availability', [CounselorController::class, 'getAvailability'])->name('availability.index');
    Route::get('/fetch-counsellor-availability', [CounselorController::class, 'fetchCounsellorAvailability'])->name('availabilitySave');
    Route::get('/profile', [CounselorController::class, 'profile'])->name('profile');

    Route::get('/settings', [CounselorController::class, 'getSettings'])->name('settings.index');
    Route::get('/setting-save', [CounselorController::class, 'saveSettings'])->name('setting.save');
});


//==============================================================================
// PROGRAM (EMPLOYER) PORTAL
//==============================================================================
Route::prefix('program')->name('program.')->middleware(['auth', 'can:access-program-panel'])->group(function () {
    Route::get('/dashboard', [ProgramController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [ProgramController::class, 'getAnalytics'])->name('analytics.index');
    Route::get('/employees', [ProgramController::class, 'getEmployees'])->name('employees.index');
    Route::get('/remove-employee', [ProgramController::class, 'removeEmployees'])->name('employee.remove');
    Route::post('/employee/save', [ProgramController::class, 'storeEmployee'])->name('employee.store');
    Route::get('/requests', [ProgramController::class, 'getRequests'])->name('requests.index');
    Route::get('/requests-view/{status}', [ProgramController::class, 'getRequests'])->name('requests.view');
    Route::get('/settings', [ProgramController::class, 'getSettings'])->name('settings.index');
    Route::get('/setting-save', [ProgramController::class, 'saveSettings'])->name('setting.save');
});


//==============================================================================
// DEVELOPMENT-ONLY ROUTES
//==============================================================================
if (app()->isLocal()) {
    Route::get('/dev/clear', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return "Application cache cleared for local development.";
    })->name('dev.clear');
}
