<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminFeedbackController;
use App\Http\Controllers\AdminTopicController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserFeedbackController;
use App\Http\Controllers\UserProfileController;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin route

// Login, Logout

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/admin/logout', [AdminAuthController::class, 'logout']);

// Univerities 
Route::middleware('auth:sanctum')->group(function () {  
    Route::post('/admin/universities', [UniversityController::class, 'store']);
    Route::put('/admin/universities/{id}', [UniversityController::class, 'update']);
    Route::delete('/admin/universities/{id}', [UniversityController::class, 'destroy']);
});

Route::get('/admin/universities', [UniversityController::class, 'index']);
Route::get('/admin/universities/{id}', [UniversityController::class, 'show']);

// Faculaties
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/faculties', [FacultyController::class, 'store']);
    Route::put('/admin/faculties/{id}', [FacultyController::class, 'update']);
    Route::delete('/admin/faculties/{id}', [FacultyController::class, 'destroy']);
});

Route::get('/admin/faculties', [FacultyController::class, 'index']);
Route::get('/admin/faculties/{id}', [FacultyController::class, 'show']);

// Admin topic 
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/topics', [AdminTopicController::class, 'index']);
    Route::get('/admin/topics/{id}', [AdminTopicController::class, 'show']);
    Route::put('/admin/topics/{id}/status', [AdminTopicController::class, 'updateStatus']);
    Route::put('/admin/topics/{id}/award', [AdminTopicController::class, 'awardTopic']);
});



// Admin feedback
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/feedback', [AdminFeedbackController::class, 'index']);
    Route::delete('/admin/feedback/{id}', [AdminFeedbackController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
});


// User

// User Auth
Route::post('/user/register', [UserAuthController::class, 'register']);
Route::post('/user/login', [UserAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/user/logout', [UserAuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/change-password', [UserAuthController::class, 'changePassword']);
});

// User create topic
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/topics', [TopicController::class, 'store']);
});

// Get approved topic
Route::get('/user/topics', [TopicController::class, 'index']);
//User Show a topic 
Route::get('/user/topics/{id}', [TopicController::class, 'show']);
// User search
Route::post('/user/topics/search', [TopicController::class, 'search']);



// User feedback
Route::middleware('auth:sanctum')->post('/user/feedback', [UserFeedbackController::class, 'store']);

// User update profile
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user/profile', [UserProfileController::class, 'update']);
});