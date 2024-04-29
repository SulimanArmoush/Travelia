<?php

use App\Http\Controllers\FacilityController;
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
});
});

Route::middleware(['auth:api'])->group(function () {

Route::controller(FacilityController::class)->group(function () {
    Route::Post('imgUpload', 'imgUpload')->name('imgUpload');
});

Route::controller(TransporterController::class)->group(function () {
    Route::Post('createTransporterAccount', 'createTransporterAccount')->name('createTransporterAccount');
});

});