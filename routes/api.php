<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransporterController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::Post('register', 'register')->name('register');
    Route::Post('login', 'login')->name('login');

    Route::middleware(['auth:api'])->group(function () {
        Route::Put('profile', 'profile')->name('profile');
        Route::Put('photo', 'photo')->name('photo');
        Route::Put('passport', 'passport')->name('passport');
        Route::Post('logout', 'logout')->name('logout');
    });
});

Route::middleware(['auth:api'])->group(function () {

    Route::controller(FacilityController::class)->group(function () {
        Route::Post('createAccount', 'createAccount')->name('createAccount');
        Route::Put('imgUpload', 'imgUpload')->name('imgUpload');
    });

    Route::controller(TransporterController::class)->group(function () {
        Route::Post('createTransportations', 'createTransportations')->name('createTransportations');
        Route::Get('getTransportation/{transportation_id}', 'getTransportation')->name('getTransportation');
        Route::Get('getTransportations/{transporter_id}', 'getTransportations')->name('getTransportations');
        Route::Get('getAvailableTransportations/{transporter_id}', 'getAvailableTransportations')->name('getAvailableTransportations');
    });
    Route::controller(HotelController::class)->group(function () {
        Route::Post('createRooms', 'createRooms')->name('createRooms');
        Route::Get('getRoom/{room_id}', 'getRoom')->name('getRoom');
        Route::Get('getRooms/{hotel_id}', 'getRooms')->name('getRooms');
        Route::Get('getAvailableRooms/{hotel_id}', 'getAvailableRooms')->name('getAvailableRooms');
    });
    Route::controller(RestaurantController::class)->group(function () {
        Route::Post('createTables', 'createTables')->name('createTables');
        Route::Get('getTable/{table_id}', 'getTable')->name('getTable');
        Route::Get('getTables/{restaurant_id}', 'getTables')->name('getTables');
        Route::Get('getAvailableTables/{restaurant_id}', 'getAvailableTables')->name('getAvailableTables');
    });
    Route::controller(AdminController::class)->group(function () {
        Route::Get('getRequiermemts', 'getRequiermemts')->name('getRequiermemts');
        Route::Get('getRequiermemt/{requiermemt_id}', 'getRequiermemt')->name('getRequiermemt');
        Route::Put('handlingRequierment/{requiermemt_id}', 'handlingRequierment')->name('handlingRequierment');
    });
    Route::controller(UserController::class)->group(function () {
        Route::Get('getUser/{userId}', 'getUser')->name('getUser');
        Route::Get('getAllUsers', 'getAllUsers')->name('getAllUsers');
        Route::Get('getAllOrganizers', 'getAllOrganizers')->name('getAllOrganizers');
        Route::Get('getAllHotelManagers', 'getAllHotelManagers')->name('getAllHotelManagers');
        Route::Get('getAllRestaurantManagers', 'getAllRestaurantManagers')->name('getAllRestaurantManagers');
        Route::Get('getAllTransporters', 'getAllTransporters')->name('getAllTransporters');
        Route::Get('getAllTourists', 'getAllTourists')->name('getAllTourists');
    });
});
