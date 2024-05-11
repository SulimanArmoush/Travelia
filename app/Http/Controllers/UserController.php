<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permissions\User;


class UserController extends Controller
{


    public function getUser($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $data['user'] = $user;
        if (in_array($user->role_id, [2, 3, 4, 5])) {
            $user->facility = $user->facility;
        }

        return response()->json($user, 200);
    }

    public function getAllUsers()
    {
        $users = User::paginate(10);
        return response()->json(['users' => $users], 200);
    }

    public function getAllOrganizers()
    {
        $users = User::where('role_id', '=', '2')->with('facility.organizer')
            ->paginate(10);

        return response()->json(['users' => $users], 200);
    }

    public function getAllHotelManagers()
    {
        $users = User::where('role_id', '=', '3')->with('facility.hotel')
            ->paginate(10);

        return response()->json(['users' => $users], 200);
    }

    public function getAllRestaurantManagers()
    {
        $users = User::where('role_id', '=', '4')->with('facility.restaurant')
            ->paginate(10);

        return response()->json(['users' => $users], 200);
    }

    public function getAllTransporters()
    {
        $users = User::where('role_id', '=', '5')->with('facility.transporter')
            ->paginate(10);

        return response()->json(['users' => $users], 200);
    }

    public function getAllTourists()
    {
        $users = User::where('role_id', '=', '6')
            ->paginate(10);

        return response()->json(['users' => $users], 200);
    }

}
