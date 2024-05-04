<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransporterController;

Route::controller(AuthController::class)->group(function () {
    Route::Post('register', 'register')->name('register');
    Route::Post('login', 'login')->name('login');

    Route::middleware(['auth:api'])->group(function () {
    Route::Post('profile', 'profile')->name('profile');
    Route::Post('photo', 'photo')->name('photo');
    Route::Post('passport', 'passport')->name('passport');
    Route::Post('logout', 'logout')->name('logout');
});
});

Route::middleware(['auth:api'])->group(function () {

Route::controller(FacilityController::class)->group(function () {
    Route::Post('createAccount', 'createAccount')->name('createAccount');
    Route::Post('imgUpload', 'imgUpload')->name('imgUpload');
});

Route::controller(TransporterController::class)->group(function () {
    Route::Post('createTransportation', 'createTransportation')->name('createTransportation');
    Route::Get('getTransportation/{transporter_id}', 'getTransportation')->name('getTransportation');
});

Route::controller(AdminController::class)->group(function () {
    Route::Get('getRequiermemts', 'getRequiermemts')->name('getRequiermemts');
    Route::Get('getRequiermemt/{requiermemt_id}', 'getRequiermemt')->name('getRequiermemt');
    Route::Put('handlingRequierment/{requiermemt_id}', 'handlingRequierment')->name('handlingRequierment');
});
Route::controller(UserController::class)->group(function () {
    Route::Get('getUser/{userId}', 'getUser')->name('getUser');
    Route::Get('getAllUser', 'getAllUser')->name('getAllUser');
    Route::Get('getAllOrganizer', 'getAllOrganizer')->name('getAllOrganizer');
    Route::Get('getAllHotelManager', 'getAllHotelManager')->name('getAllHotelManager');
    Route::Get('getAllRestaurantManager', 'getAllRestaurantManager')->name('getAllRestaurantManager');
    Route::Get('getAllTransporter', 'getAllTransporter')->name('getAllTransporter');
    Route::Get('getAllTourist', 'getAllTourist')->name('getAllTourist');
});

Route::controller(CountryController::class)->group(function () {
    Route::Post('createCountry', 'createCountry')->name('createCountry');
    Route::Post('createCity', 'createCity')->name('createCity');
    Route::Post('createArea', 'createArea')->name('createArea');

});
});