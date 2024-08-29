<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\ReservationController;
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
        //Route::middleware(['check'])->group(function () {
        Route::Post('createDeviceToken', 'createDeviceToken')->name('createDeviceToken');
        Route::Post('logout', 'logout')->name('logout');
        Route::Post('profile', 'profile')->name('profile');
        Route::Post('photo', 'photo')->name('photo');
        Route::Post('passport', 'passport')->name('passport');
        //});
    });
});

Route::middleware(['auth:api'])->group(function () {
    //Route::middleware(['check'])->group(function () {

    Route::controller(AdminController::class)->group(function () {
        Route::Get('getRequierments', 'getRequierments')->name('getRequierments');
        Route::Get('getRequierment/{requierment_id}', 'getRequierment')->name('getRequierment');
        Route::Post('handlingRequierment/{requierment_id}', 'handlingRequierment')->name('handlingRequierment');
        Route::Get('getContact', 'getContact')->name('getContact');
        Route::Get('getTransferRequests', 'getTransferRequests')->name('getTransferRequests');
        Route::Post('handlingTransferRequests/{requierment_id}', 'handlingTransferRequests')->name('handlingTransferRequests');
        Route::Post('sendMessage', 'sendMessage')->name('sendMessage');
    });
    Route::controller(FacilityController::class)->group(function () {
        Route::Post('createAccount', 'createAccount')->name('createAccount');
        Route::Get('getProfile', 'getProfile')->name('getProfile');
        Route::Post('imgUpload', 'imgUpload')->name('imgUpload');
        Route::Get('getNearHotel/{area_id}', 'getNearHotel')->name('getNearHotel');
        Route::Get('getNearRestaurant/{area_id}', 'getNearRestaurant')->name('getNearRestaurant');
        Route::Get('getFinances', 'getFinances')->name('getFinances');
        Route::Post('makeContact', 'makeContact')->name('makeContact');
        Route::Get('getWallet', 'getWallet')->name('getWallet');
        Route::Get('organizerBooking', 'organizerBooking')->name('organizerBooking');
    });
    Route::controller(HotelController::class)->group(function () {
        Route::Post('createRooms', 'createRooms')->name('createRooms');
        Route::Get('getRoom/{room_id}', 'getRoom')->name('getRoom');
        Route::Get('getRooms/{hotel_id}', 'getRooms')->name('getRooms');
        Route::Get('getAvailableRooms/{hotel_id}', 'getAvailableRooms')->name('getAvailableRooms');
        Route::Get('getHotelReservation', 'getHotelReservation')->name('getHotelReservation');
    });
    Route::controller(OrganizerController::class)->group(function () {
        Route::Post('createTrip', 'createTrip')->name('createTrip');
        Route::Get('getTrip/{trip_id}', 'getTrip')->name('getTrip');
        Route::Get('getTrips', 'getTrips')->name('getTrips');
        Route::Get('getOrganizerTrips', 'getOrganizerTrips')->name('getOrganizerTrips');
        Route::Get('organizerTrips', 'organizerTrips')->name('organizerTrips');
        Route::Get('getAvailableTrips/{organizer_id}', 'getAvailableTrips')->name('getAvailableTrips');
        Route::Get('getAllAvailableTrips', 'getAllAvailableTrips')->name('getAllAvailableTrips');
        Route::Delete('deleteTrip/{trip_id}', 'deleteTrip')->name('deleteTrip');
    });
    Route::controller(ReservationController::class)->group(function () {
        Route::Post('tripBooking/{trip_id}', 'tripBooking')->name('tripBooking');
        Route::Get('getReservation/{trip_id}', 'getReservation')->name('getReservation');
        Route::Post('booking', 'booking')->name('booking');
        Route::Post('restaurantBooking/{reservation_id}', 'restaurantBooking')->name('restaurantBooking');
        Route::Get('getUserTripBooking', 'getUserTripBooking')->name('getUserTripBooking');
        Route::Get('getUserBooking', 'getUserBooking')->name('getUserBooking');
        Route::Delete('deleteTripReservation/{reservation_id}', 'deleteTripReservation')->name('deleteTripReservation');
        Route::Delete('deleteRestaurantReservation/{reservation_id}', 'deleteRestaurantReservation')->name('deleteRestaurantReservation');
        Route::Delete('deleteReservation/{reservation_id}', 'deleteReservation')->name('deleteReservation');
        Route::Post('getCostForBook', 'getCostForBook')->name('getCostForBook');
    });
    Route::controller(RestaurantController::class)->group(function () {
        Route::Post('createTables', 'createTables')->name('createTables');
        Route::Get('getTable/{table_id}', 'getTable')->name('getTable');
        Route::Get('getTables/{restaurant_id}', 'getTables')->name('getTables');
        Route::Get('getAvailableTables/{restaurant_id}', 'getAvailableTables')->name('getAvailableTables');
        Route::Get('getRestaurantReservation', 'getRestaurantReservation')->name('getRestaurantReservation');
    });
    Route::controller(TouristAreaController::class)->group(function () {
        Route::Post('createArea', 'createArea')->name('createArea');
        Route::Get('getTouristArea/{touristArea_id}', 'getTouristArea')->name('getTouristArea');
        Route::Get('getTouristAreas', 'getTouristAreas')->name('getTouristAreas');
        Route::Get('getAreas', 'getAreas')->name('getAreas');
    });
    Route::controller(TransporterController::class)->group(function () {
        Route::Post('createAirTransportations', 'createAirTransportations')->name('createAirTransportations');
        Route::Post('createLandTransportations', 'createLandTransportations')->name('createLandTransportations');
        Route::Get('getTransportation/{transportation_id}', 'getTransportation')->name('getTransportation');
        Route::Get('getTransportations/{transporter_id}', 'getTransportations')->name('getTransportations');
        Route::Post('getAvailableTransportations/{transporter_id}', 'getAvailableTransportations')->name('getAvailableTransportations');
        Route::Post('createRouting', 'createRouting')->name('createRouting');
        Route::Get('getRoute/{route_id}', 'getRoute')->name('getRoute');
        Route::Get('getAvailableRoute', 'getAvailableRoute')->name('getAvailableRoute');
        Route::Get('getTransporterRoutes', 'getTransporterRoutes')->name('getTransporterRoutes');
        Route::Post('getTransportationsForRoutes', 'getTransportationsForRoutes')->name('getTransportationsForRoutes');
        Route::Post('getTransporters', 'getTransporters')->name('getTransporters');
        Route::Get('getRouteReservation/{routing_id}', 'getRouteReservation')->name('getRouteReservation');
    });
    Route::controller(UserController::class)->group(function () {
        Route::Get('getUser/{userId}', 'getUser')->name('getUser');
        Route::Get('getAllUsers', 'getAllUsers')->name('getAllUsers');
        Route::Get('getAllOrganizers', 'getAllOrganizers')->name('getAllOrganizers');
        Route::Get('getAllHotelManagers', 'getAllHotelManagers')->name('getAllHotelManagers');
        Route::Get('getAllRestaurantManagers', 'getAllRestaurantManagers')->name('getAllRestaurantManagers');
        Route::Get('getAllTransporters', 'getAllTransporters')->name('getAllTransporters');
        Route::Get('getAllTourists', 'getAllTourists')->name('getAllTourists');
        Route::Post('transferRequest', 'transferRequest')->name('transferRequest');
        Route::Post('addToFav/{organizer_id}', 'addToFav')->name('addToFav');
        Route::Delete('removeFromFav/{organizer_id}', 'removeFromFav')->name('removeFromFav');
        Route::Get('getFav', 'getFav')->name('getFav');
        Route::Get('getNotifications', 'getNotifications')->name('getNotifications');
    });
    //});
});
