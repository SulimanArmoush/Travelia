<?php

namespace App\Http\Controllers;

use App\Models\Permissions\Role;
use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\Facilities\Favorite;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Requirement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Permissions\User;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use NotificationTrait;

    public function getUser($userId): JsonResponse
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not Found']);
        }
        if (in_array($user->role_id, [2, 3, 4, 5])) {
            if ($user->confirmation == '2') {
                $user->facility = $user->facility;
            }
        }
        return response()->json($user);
    }

    public function getAllUsers(): JsonResponse
    {
        $users = User::paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found']);
        }
        return response()->json(['users' => $users]);
    }

    public function getAllOrganizers(): JsonResponse
    {
        $users = User::where('role_id', '2')
            ->where('confirmation', '2')
            ->with(['facility.organizer' => function ($query) {
                $query->withCount('trips');
            }])
            ->get();

        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found']);
        }

        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
                'phone' => $user->phone,
                'age' => $user->age,
                'address' => $user->address,
                'photo' => $user->photo,
                'passport' => $user->passport,
                'role_id' => $user->role_id,
                'wallet' => $user->wallet,
                'type' => $user->type,
                'confirmation' => $user->confirmation,
                'email_verified_at' => $user->email_verified_at,
                'facility' => $user->facility ? [
                    'id' => $user->facility->id,
                    'name' => $user->facility->name,
                    'description' => $user->facility->description,
                    'img' => $user->facility->img,
                    'location' => $user->facility->location ? [
                        'id' => $user->facility->location->id,
                        'latitude' => $user->facility->location->latitude,
                        'longitude' => $user->facility->location->longitude,
                        'address' => $user->facility->location->address,
                        'country' => $user->facility->location->country,
                        'state' => $user->facility->location->state,
                        'city' => $user->facility->location->city,
                    ] : null,
                    'organizer' => $user->facility->organizer ? [
                        'id' => $user->facility->organizer->id,
                        'type' => $user->facility->organizer->type,
                        'trip_count' => $user->facility->organizer->trips_count,
                    ] : null,
                ] : null,
            ];
        });
        $sortedList = $formattedUsers->sortByDesc('facility.organizer.trip_count')->values();

        return response()->json(['users' => $sortedList]);
    }


    public function getAllHotelManagers(): JsonResponse
    {
        $users = User::where('role_id', '=', '3')->where('confirmation', '2')->with('facility.hotel')
            ->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found']);
        }
        return response()->json(['users' => $users]);
    }

    public function getAllRestaurantManagers(): JsonResponse
    {
        $users = User::where('role_id', '=', '4')->where('confirmation', '2')->with('facility.restaurant')
            ->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found']);
        }
        return response()->json(['users' => $users]);
    }

    public function getAllTransporters(): JsonResponse
    {
        $users = User::where('role_id', '=', '5')->where('confirmation', '2')->with('facility.transporter')
            ->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found']);
        }
        return response()->json(['users' => $users]);
    }

    public function getAllTourists(): JsonResponse
    {
        $users = Role::find(6)->users()->paginate(10);
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found']);
        }
        return response()->json(['users' => $users]);
    }

    public function transferRequest(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $user = Auth::id();
        Requirement::create([
            'user_id' => $user->id,
            'note' => 'Accept the transfer request with the required value',
            'amount' => $request->amount
        ]);

        $this->send($user->deviceToken,'Balance charge',
            'Your request has been sent successfully. You will receive a message when the process is successful');

        return response()->json(['massage' => 'your request is being verified']);

    }

    public function addToFav($organizer_id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found']);
        }

        $organizer = Organizer::find($organizer_id);
        if (!$organizer) {
            return response()->json(['error' => 'Organizer Not Found']);
        }

        $favoriteExists = Favorite::where('user_id', $user->id)
            ->where('organizer_id', $organizer->id)
            ->exists();

        if (!$favoriteExists) {
            Favorite::create([
                'user_id' => $user->id,
                'organizer_id' => $organizer->id
            ]);
            return response()->json(['message' => 'This Account has been added to favorites']);
        }
        return response()->json(['message' => 'This Account is already in your favorites']);
    }

    public function removeFromFav($organizer_id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found']);
        }

        $organizer = Organizer::find($organizer_id);
        if (!$organizer) {
            return response()->json(['error' => 'Organizer Not Found']);
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('organizer_id', $organizer->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'This Account has been removed from favorites']);
        }
        return response()->json(['message' => 'This Account is not in your favorites']);
    }

    public function getFav(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User Not Found']);
        }

        $favorites = $user->favorites;
        if (empty($favorites)) {
            return response()->json(['error' => 'Organizer Not Found']);
        }

        $formatted = collect();;
        foreach ($favorites as $favorite) {
            $formatted->push([
                'id' => $favorite->id,
                'name' => $favorite->facility->name,
                'description' => $favorite->facility->description,
                'img' => $favorite->facility->img,
                'type' => $favorite->type,
                'email' => $favorite->facility->user->email,
                'photo' => $favorite->facility->user->photo,
            ]);
        }
        return response()->json(['favorites' => $formatted]);
    }



/*    public function testSend($token):String
    {
            return $this->send($token,
                'شو ما كان','كان ياما كان');
    }*/

}

