<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TouristAreaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransporterController;
use Illuminate\Support\Facades\Route;

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
        Route::Get('getNearHotel/{area_id}', 'getNearHotel')->name('getNearHotel');
        Route::Get('getNearRestaurant/{area_id}', 'getNearRestaurant')->name('getNearRestaurant');
        Route::Get('getProfile', 'getProfile')->name('getProfile');
        Route::Get('getFinances', 'getFinances')->name('getFinances');
        Route::Post('makeContact', 'makeContact')->name('makeContact');
    });

    Route::controller(TransporterController::class)->group(function () {
        Route::Post('createAirTransportations', 'createAirTransportations')->name('createAirTransportations');
        Route::Post('createLandTransportations', 'createLandTransportations')->name('createLandTransportations');
        Route::Get('getTransportation/{transportation_id}', 'getTransportation')->name('getTransportation');
        Route::Get('getTransportations/{transporter_id}', 'getTransportations')->name('getTransportations');
        Route::Get('getAvailableTransportations/{transporter_id}', 'getAvailableTransportations')->name('getAvailableTransportations');
        Route::Post('createRouting', 'createRouting')->name('createRouting');
        Route::Get('getRoute/{route_id}', 'getRoute')->name('getRoute');
        Route::Get('getNearRoute', 'getNearRoute')->name('getNearRoute');
        Route::Get('getOwnerRoutes/{transporter_id}', 'getOwnerRoutes')->name('getOwnerRoutes');
        Route::Get('getTransporters', 'getTransporters')->name('getTransporters');

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
        Route::Get('getRequierments', 'getRequierments')->name('getRequierments');
        Route::Get('getRequierment/{requierment_id}', 'getRequierment')->name('getRequierment');
        Route::Post('handlingRequierment/{requierment_id}', 'handlingRequierment')->name('handlingRequierment');
        Route::Get('getContact', 'getContact')->name('getContact');
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
    Route::controller(OrganizerController::class)->group(function () {
        Route::Post('createTrip', 'createTrip')->name('createTrip');
        Route::Get('getTrip/{trip_id}', 'getTrip')->name('getTrip');
        Route::Get('getTrips', 'getTrips')->name('getTrips');
        Route::Get('getOrganizerTrips', 'getOrganizerTrips')->name('getOrganizerTrips');
        Route::Get('organizerTrips', 'organizerTrips')->name('organizerTrips');
    });
    Route::controller(TouristAreaController::class)->group(function () {
        Route::Post('createArea', 'createArea')->name('createArea');
        Route::Get('getTouristArea/{touristArea_id}', 'getTouristArea')->name('getTouristArea');
        Route::Get('getTouristAreas', 'getTouristAreas')->name('getTouristAreas');
        Route::Get('getAreas', 'getAreas')->name('getAreas');
    });
});
