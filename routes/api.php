<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CountryController;
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

Route::controller(AdminController::class)->group(function () {
    Route::Get('getRequiermemts', 'getRequiermemts')->name('getRequiermemts');
    Route::Get('getRequiermemt/{requiermemt_id}', 'getRequiermemt')->name('getRequiermemt');
    Route::Put('handlingRequierment/{requiermemt_id}', 'handlingRequierment')->name('handlingRequierment');

});

Route::controller(CountryController::class)->group(function () {
    Route::Post('createCountry', 'createCountry')->name('createCountry');
    Route::Post('createCity', 'createCity')->name('createCity');
    Route::Post('createArea', 'createArea')->name('createArea');

});
});