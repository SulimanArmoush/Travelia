<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Hotels\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function createRooms(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'num1' => ['integer'],
            'cost1' => ['numeric'],
            'num2' => ['integer'],
            'cost2' => ['numeric'],
            'num3' => ['integer'],
            'cost3' => ['numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $hotel = auth()->user()->facility->hotel;

        if ($request->num1) {
            for ($i = 0; $i < $request->num1; $i++) {
                Room::create([
                    'hotel_id' => $hotel->id,
                    'cost' => $request->cost1,
                    'type' => 'room for one person',
                ]);
            }
        }
        if ($request->num2) {
            for ($i = 0; $i < $request->num2; $i++) {
                Room::create([
                    'hotel_id' => $hotel->id,
                    'cost' => $request->cost2,
                    'type' => 'room for two person',
                ]);
            }
        }
        if ($request->num3) {
            for ($i = 0; $i < $request->num3; $i++) {
                Room::create([
                    'hotel_id' => $hotel->id,
                    'cost' => $request->cost3,
                    'type' => 'suite',
                ]);
            }
        }
        return response()->json(['message' => 'Your Rooms created successfully']);

    }


    public function getRoom($room_id): JsonResponse
    {
        $room = Room::find($room_id);
        if (!$room) {
            return response()->json(['error' => 'Room not Found']);
        }
        return response()->json($room);
    }


    public function getRooms($hotel_id): JsonResponse
    {
        $hotel = Hotel::find($hotel_id);
        if (!$hotel) {
            return response()->json(['error' => 'Hotel not Found']);
        }
        if ($hotel->rooms->isEmpty()) {
            return response()->json(['error' => "Rooms Not Found"]);
        }
        return response()->json(['rooms' => $hotel->rooms]);
    }

    public function getAvailableRooms(Request $request, $hotel_id): JsonResponse
    {
        $hotel = Hotel::find($hotel_id);
        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found']);
        }

        $roomForOnePerson = [];
        $roomForTwoPerson = [];
        $suite = [];

        foreach ($hotel->rooms as $room) {
            if ($room->type == 'room for one person') {
                $roomForOnePerson [] = $room;
            }
            if ($room->type == 'room for two person') {
                $roomForTwoPerson [] = $room;
            }
            if ($room->type == 'suite') {
                $suite [] = $room;
            }
        }

        $validator = Validator::make($request->all(), [
            'strDate' => ['required', 'date'],
            'endDate' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $startDate = Carbon::parse($request->strDate);
        $endDate = Carbon::parse($request->endDate);

        $availableRooms = $hotel->rooms()->
        whereDoesntHave('reservations', function ($query) use ($startDate, $endDate) {
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('strDate', [$startDate, $endDate])
                    ->orWhereBetween('endDate', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('strDate', '<=', $startDate)
                            ->where('endDate', '>=', $endDate);
                    });
            });
        })->get();

        if ($availableRooms->isEmpty()) {
            return response()->json(['error' => "Rooms Not Found"]);
        }

        $availableRoomForOnePerson = [];
        $availableRoomForTwoPerson = [];
        $availableSuite = [];

        foreach ($availableRooms as $availableRoom) {
            if ($availableRoom->type == 'room for one person') {
                $availableRoomForOnePerson [] = $availableRoom;
            }
            if ($availableRoom->type == 'room for two person') {
                $availableRoomForTwoPerson [] = $availableRoom;
            }
            if ($availableRoom->type == 'suite') {
                $availableSuite [] = $availableRoom;
            }
        }

        return response()->json([
            'roomForOnePerson' => count($roomForOnePerson),
            'availableRoomForOnePerson' => count($availableRoomForOnePerson),
            'roomForTwoPerson' => count($roomForTwoPerson),
            'availableRoomForTwoPerson' => count($availableRoomForTwoPerson),
            'suite' => count($suite),
            'availableSuite' => count($availableSuite)
        ]);
    }

    public function getHotelReservation(): JsonResponse
    {
        $list = Auth::user()->facility->hotel->reservations;
        if ($list->isEmpty()) {
            return response()->json(['error' => 'Reservation Not Found']);
        };

        $formattedList = collect();
        foreach ($list as $item) {
            if (Carbon::parse($item->strDate)->isPast()) {
                continue;
            }

            $daysNum = Carbon::parse($item->strDate)->diffInDays($item->endDate) + 1;
            $roomCost = $item->room->cost * $daysNum;

            $formattedList->push([
                'id' => $item->id,
                'room' => $item->room->type,
                'cost' => intval($roomCost),
                'strDate' => Carbon::parse($item->strDate)->format('Y-m-d'),
                'endDate' => Carbon::parse($item->endDate)->format('Y-m-d'),
                'daysNum' => $daysNum,
                'name' => $item->user->firstName . ' ' . $item->user->lastName,
                'email' => $item->user->email,
                'phone' => $item->user->phone,
                'age' => $item->user->age,
                'address' => $item->user->address,
                'photo' => $item->user->photo
            ]);
        }
        $sortedList = $formattedList->sortBy('strDate')->values();

        return response()->json(['reservations' => $sortedList]);
    }
}
