<?php

namespace Database\Seeders;

use App\Models\Permissions\Permission;
use App\Models\Permissions\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin'],//1
            ['name' => 'Trip_Organizer'],//2
            ['name' => 'Hotel_Manager'],//3
            ['name' => 'Restaurant_Manager'],//4
            ['name' => 'Transport_Manager'],//5
            ['name' => 'tourist'],//6
        ];
        $permissions = [
            ['name' => 'logout'],//1
            ['name' => 'profile'],//2
            ['name' => 'photo'],//3
            ['name' => 'passport'],//4
            ['name' => 'getRequierments'],//5
            ['name' => 'getRequierment'],//6
            ['name' => 'handlingRequierment'],//7
            ['name' => 'getContact'],//8
            ['name' => 'createAccount'],//9
            ['name' => 'getProfile'],//10
            ['name' => 'imgUpload'],//11
            ['name' => 'getNearHotel'],//12
            ['name' => 'getNearRestaurant'],//13
            ['name' => 'getFinances'],//14
            ['name' => 'makeContact'],//15
            ['name' => 'getWallet'],//16
            ['name' => 'createRooms'],//17
            ['name' => 'getRoom'],//18
            ['name' => 'getRooms'],//19
            ['name' => 'getAvailableRooms'],//20
            ['name' => 'getHotelReservation'],//21
            ['name' => 'createTrip'],//22
            ['name' => 'getTrip'],//23
            ['name' => 'getTrips'],//24
            ['name' => 'getOrganizerTrips'],//25
            ['name' => 'organizerTrips'],//26
            ['name' => 'getAvailableTrips'],//27
            ['name' => 'getAllAvailableTrips'],//28
            ['name' => 'tripBooking'],//29
            ['name' => 'getReservation'],//30
            ['name' => 'booking'],//31
            ['name' => 'restaurantBooking'],//32
            ['name' => 'createTables'],//33
            ['name' => 'getTable'],//34
            ['name' => 'getTables'],//35
            ['name' => 'getAvailableTables'],//36
            ['name' => 'getRestaurantReservation'],//37
            ['name' => 'createArea'],//38
            ['name' => 'getTouristArea'],//39
            ['name' => 'getTouristAreas'],//40
            ['name' => 'getAreas'],//41
            ['name' => 'createAirTransportations'],//42
            ['name' => 'createLandTransportations'],//43
            ['name' => 'getTransportation'],//44
            ['name' => 'getTransportations'],//45
            ['name' => 'getAvailableTransportations'],//46
            ['name' => 'createRouting'],//47
            ['name' => 'getRoute'],//48
            ['name' => 'getAvailableRoute'],//49
            ['name' => 'getTransporterRoutes'],//50
            ['name' => 'getTransportationsForRoutes'],//51
            ['name' => 'getTransporters'],//52
            ['name' => 'getRouteReservation'],//53
            ['name' => 'getUser'],//54
            ['name' => 'getAllUsers'],//55
            ['name' => 'getAllOrganizers'],//56
            ['name' => 'getAllHotelManagers'],//57
            ['name' => 'getAllRestaurantManagers'],//58
            ['name' => 'getAllTransporters'],//59
            ['name' => 'getAllTourists'],//60
            ['name' => 'transferRequest'],//61
            ['name' => 'getTransferRequests'],//62
            ['name' => 'handlingTransferRequests'],//63
            ['name' => 'addToFav'],//64
            ['name' => 'removeFromFav'],//65
            ['name' => 'getFav'],//66
            ['name' => 'getUserTripBooking'],//67
            ['name' => 'getUserBooking'],//68
            ['name' => 'sendMessage'],//69
            ['name' => 'getNotifications'],//70
            ['name' => 'deleteTrip'],//71
            ['name' => 'deleteTripReservation'],//72
            ['name' => 'deleteRestaurantReservation'],//73
            ['name' => 'deleteReservation'],//74
            ['name' => 'organizerBooking'],//75
            ['name' => 'getCostForBook'],//76


        ];
        Role::insert($roles);
        Permission::insert($permissions);
    }
}
