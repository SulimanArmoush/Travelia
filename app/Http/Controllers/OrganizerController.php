<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Organizers\Trip;
use App\Models\TheWorld\TouristArea;
use App\Traits\FacilityCreateTrait;
use App\Traits\MyTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrganizerController extends Controller
{
    use facilityCreateTrait, MyTrait;

    public function createTrip(Request $request)
    {
        if (Auth::user()->role_id != 2) {
            return response()->json(['error' => 'You are not a Trip Organizer'], 400);
        }
        $validator = validator::make($request->all(), [
            'cost' => ['required', 'numeric'],
            'strDate' => ['required', 'date'],
            'endDate' => ['required', 'date'],

            'totalCapacity' => ['required', 'integer'],

            'touristArea_id' => ['required', 'integer'],
            'hotel_id' => ['required', 'integer'],
            'restaurant_id' => ['required', 'integer'],
            'transporter_id' => ['required', 'integer'],

            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'address' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        try {
            $organizer = auth()->user()->facility->organizer->firstOrFail();
            $area = TouristArea::findOrFail($request->touristArea_id);

            $hotel = Facility::findOrFail($request->hotel_id)->hotel;
            $restaurant = Facility::findOrFail($request->restaurant_id)->restaurant;

            $location = $this->createLocation(
                $request->latitude,
                $request->longitude,
                $request->address,
                $request->country,
                $request->state,
                $request->city
            );

            Trip::create([
                'organizer_id' => $organizer->id,
                'cost' => $request->cost,
                'strDate' => $request->strDate,
                'endDate' => $request->endDate,
                'totalCapacity' => $request->totalCapacity,
                'img' => $area->img,
                'strLocation' => $location->id,
                'touristArea_id' => $area->id,
                'hotel_id' => $hotel->id,
                'restaurant_id' => $restaurant->id,
                'transporter_id' => $request->transporter_id,
            ]);

            return response()->json(['message' => 'Your Trip created successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Not Found'], 404);
        }
    }

    public function getTrip($trip_id)
    {
        try {
            $trip = Trip::with(
                'location',
                'hotel.facility',
                'restaurant.facility',
                'transporter.facility',
                'touristArea.location'
            )->findOrFail($trip_id);

            return response()->json($trip, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Trip not Found'], 404);
        }
    }

    public function getTrips()
    {
        $trips = Trip::all();
        if ($trips->isEmpty()) {
            return response()->json(['error' => 'Trips not Found'], 200);
        }
        return response()->json(['trips' => $trips], 200);
    }

    public function getOrganizerTrips($organizer_id)
    {
        try {
            $organizer = Organizer::findOrFail($organizer_id);
            $trips = Trip::where('organizer_id', $organizer->id)
                ->with('location', 'touristArea', 'hotel.facility', 'restaurant.facility', 'transporter.facility')
                ->get();

            if ($trips->isEmpty()) {
                return response()->json(['error' => 'Trips not Found'], 200);
            }

            $format = [];
            foreach ($trips as $trip) {
                $format[] = [
                    "id" => $trip->id,
                    "organizer" => $trip->organizer->facility->name,
                    "cost" => $trip->cost,
                    "strDate" => $trip->strDate,
                    "endDate" => $trip->endDate,
                    "totalCapacity" => $trip->totalCapacity,
                    "img" => $trip->img,
                    "strLocation" => $trip->location->address,
                    "touristArea" => $trip->touristArea->name,
                    "hotel" => $trip->hotel->facility->name,
                    "restaurant" => $trip->restaurant->facility->name,
                    "transporter" => $trip->transporter->facility->name,
                    "status" => $trip->status,
                ];
            }
            return response()->json(['trips' => $format], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Organizer Not Found"], 404);
        }
    }
}
