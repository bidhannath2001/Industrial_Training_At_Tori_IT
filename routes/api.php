<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SaloonServiceController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\UserProfileController;


//Public routes

//Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
});

//Doctor routes
Route::prefix('doctor')->group(function () {
    Route::get('/',[DoctorController::class,'index']);
    Route::get('/{id}',[DoctorController::class,'show']);
    Route::get('/by-district/{districtId}',[DoctorController::class,'byDistrict']);
    Route::get('/by-subcategory/{subcategoryId}',[DoctorController::class,'bySubcategory']);
    Route::get('/{doctorId}/available-slots',[DoctorController::class,'getAvailableSlots']);
});

//Clinic routes
Route::prefix('clinics')->group(function () {
    Route::get('/', [ClinicController::class, 'index']);
    Route::get('/{id}', [ClinicController::class, 'show']);
    Route::get('/by-district/{districtId}', [ClinicController::class, 'byDistrict']);
});

//Saloon routes
Route::prefix('services')->group(function () {
    Route::get('/{id}', [SaloonServiceController::class, 'show']);
});

// Reviews
Route::prefix('reviews')->group(function () {
    Route::get('/doctor/{doctorId}', [ReviewController::class, 'doctorReviews']);
});

//Miscellaneous
Route::prefix('misc')->group(function () {
    Route::get('/districts', [DistrictController::class, 'index']);
    Route::get('/districts/{id}', [DistrictController::class, 'show']);
    Route::get('/subcategories', [SubcategoryController::class, 'index']);
    Route::get('/subcategories/{id}', [SubcategoryController::class, 'show']);
});

//Search
Route::get('/search', [SearchController::class, 'search']);




//Protected routes

Route::middleware('auth:sanctum')->group(function(){

    //Auth routes
    Route::prefix('auth')->group(function(){
        Route::get('/me', [AuthController::class, 'currentUser']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
    
    //User Profile
    Route::prefix('profile')->group(function(){
       Route::get('/', [UserProfileController::class, 'show']);
       Route::put('/', [UserProfileController::class, 'update']);
       Route::post('change-password', [UserProfileController::class, 'changePassword']); 
    });

    //Appointments
    Route::prefix('appointments')->group(function(){
       Route::post('/',[AppointmentController::class,'store']); 
       Route::get('/',[AppointmentController::class,'userAppointments']); 
       Route::get('/{id}',[AppointmentController::class,'show']); 
       Route::patch('/{id}/cancel',[AppointmentController::class,'cancel']);
    });

    //Reviews
    Route::prefix('reviews')->group(function(){
        Route::post('/', [ReviewController::class, 'store']);
        Route::put('/{id}', [ReviewController::class, 'update']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });

    //Saloon Appointments
    Route::prefix('saloon-appointments')->group(function(){
        Route::post('/',[AppointmentController::class,'store']); 
        Route::get('/',[AppointmentController::class,'userAppointments']); 
        Route::get('/{id}',[AppointmentController::class,'show']); 
        Route::patch('/{id}/cancel',[AppointmentController::class,'cancel']);
    });

});