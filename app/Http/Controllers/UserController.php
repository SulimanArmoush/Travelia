<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function getUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            if (in_array($user->role_id, [2, 3, 4, 5])) {
                if($user->confirmation == '2'){
                $user->facility = $user->facility;
            }}
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function getAllUsers()
    {
        $users = User::paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found'], 200);
        }
        return response()->json(['users' => $users], 200);
    }

    public function getAllOrganizers()
    {
        $users = User::where('role_id', '=', '2')->where('confirmation', '2')->with('facility.organizer')
            ->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found'], 200);
        }
        return response()->json(['users' => $users], 200);
    }

    public function getAllHotelManagers()
    {
        $users = User::where('role_id', '=', '3')->where('confirmation', '2')->with('facility.hotel')
            ->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found'], 200);
        }
        return response()->json(['users' => $users], 200);
    }

    public function getAllRestaurantManagers()
    {
        $users = User::where('role_id', '=', '4')->where('confirmation', '2')->with('facility.restaurant')
            ->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found'], 200);
        }
        return response()->json(['users' => $users], 200);
    }

    public function getAllTransporters()
    {
        $users = User::where('role_id', '=', '5')->where('confirmation', '2')->with('facility.transporter')
            ->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found'], 200);
        }
        return response()->json(['users' => $users], 200);
    }

    public function getAllTourists()
    {
        $users = User::where('role_id', '=', '6')
            ->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found'], 200);
        }
        return response()->json(['users' => $users], 200);
    }
}
