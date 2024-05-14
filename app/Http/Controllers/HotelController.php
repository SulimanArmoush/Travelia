<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Hotels\Room;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function createRooms(Request $request)
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
        try {
            $hotel = auth()->user()->facility->hotel->firstOrFail();

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
            return response()->json(['message' => 'Your Rooms created successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Hotel not Found'], 404);
        }
    }


    public function getRoom($room_id)
    {
        try {
            $room = Room::findOrFail($room_id);
            return response()->json($room, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Room Not Found"], 404);
        }
    }


    public function getRooms($hotel_id)
    {
        try {
            $hotel = Hotel::findOrFail($hotel_id);
            $rooms = Room::Where('hotel_id', $hotel->id)
                ->paginate(10);

            if ($rooms->isEmpty()) {
                return response()->json(['message' => "Rooms Not Found"], 404);
            }
            return response()->json(['rooms' => $rooms], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Hotel Not Found"], 404);
        }
    }

    public function getAvailableRooms($hotel_id)
    {
        try {
            $hotel = Hotel::findOrFail($hotel_id);
            $rooms = Room::Where('hotel_id', $hotel->id)
                ->where('status', 'available')
                ->paginate(10);

            if ($rooms->isEmpty()) {
                return response()->json(['message' => "Rooms Not Found"], 404);
            }
            return response()->json(['rooms' => $rooms], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Hotel Not Found"], 404);
        }
    }


}
