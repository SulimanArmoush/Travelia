<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Hotels\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function createRooms(Request $request)
    {
        $hotel = auth()->user()->facility->hotel;

        $validator = validator::make($request->all(), [
            'bedNum' => ['required', 'integer'],
            'cost' => ['required', 'numeric'],
            'type' => ['required', 'integer'],
            'number' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        for ($i = 0; $i < $request->number; $i++) {
            Room::create([
                'hotel_id' => $hotel->id,
                'bedNum' => $request->totalCapacity,
                'cost' => $request->cost,
                'type' => $request->type,
            ]);
        }
        return response()->json(['message' => 'Your Rooms created successfully'], 200);
    }

    public function getRoom($Room_id)
    {
        $room = Room::find($Room_id);
        return response()->json(['room' => $room], 200);
    }

    public function getRooms($hotel_id)
    {
        $rooms = Room::Where('hotel_id', $hotel_id)
            ->paginate(10);
        return response()->json(['rooms' => $rooms], 200);
    }

    public function getAvailableRooms($hotel_id)
    {
        $rooms = Room::Where('hotel_id', $hotel_id)
            ->where('status', 'available')
            ->paginate(10);
        return response()->json(['rooms' => $rooms], 200);

    }

}
