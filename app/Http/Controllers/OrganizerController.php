<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Organizers\Trip;
use App\Traits\FacilityCreateTrait;
use App\Traits\PhotoTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizerController extends Controller
{
    use facilityCreateTrait, PhotoTrait;

    public function createTrip(Request $request)
    {
        $organizer = auth()->user()->facility->organizer;

        if (!$organizer) {
            return response()->json(['message' => "Organizer Not Found"], 404);
        }

        $validator = validator::make($request->all(), [
            'cost' => ['required', 'numeric'],
            'dateTime' => ['required', 'date'],
            'totalCapacity' => ['required', 'integer'],

            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'address' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],

            'imgs' => ['required','min:3', 'max:3'],
            'imgs.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],

            'touristArea_id' => ['required', 'integer'],
            'hotel_id' => ['required', 'integer'],
            'restaurant_id' => ['required', 'integer'],
            'transporter_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $images = $this->upload($request->imgs);

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
            'dateTime' => $request->dateTime,
            'totalCapacity' => $request->totalCapacity,
            'imgs' => $images,
            'strLocation' => $location->id,
            'touristArea_id' => $request->touristArea_id,
            'hotel_id' => $request->hotel_id,
            'restaurant_id' => $request->restaurant_id,
            'transporter_id' => $request->transporter_id,
        ]);

        return response()->json(['message' => 'Your Trip created successfully'], 200);
    }

    public function getTrip($trip_id)
    {
        if (!$trip_id) {
            return response()->json(['message' => "Trip Not Found"], 404);
        }
        $trip = Trip::with(
            'location',
            'hotel.facility',
            'restaurant.facility',
            'transporter.facility',
            'touristArea.location'
        )->find($trip_id);
        if (!$trip) {
            return response()->json(['error' => 'Trip not Found'], 404);
        }
        $trip->imgs = json_decode($trip->imgs);
        $trip->hotel->facility->imgs = json_decode($trip->hotel->facility->imgs);
        $trip->restaurant->facility->imgs = json_decode($trip->restaurant->facility->imgs);
        $trip->transporter->facility->imgs = json_decode($trip->transporter->facility->imgs);
        $trip->touristArea->imgs = json_decode($trip->touristArea->imgs);

        return response()->json($trip,200);
    }

    public function getTrips()
    {
        $trips = Trip::all();
        if (!$trips) {
            return response()->json(['error' => 'Trips not Found'], 404);
        }
        foreach ($trips as $trip) {
            $trip->imgs = json_decode($trip->imgs);
        }
        return response()->json(['trips' => $trips], 200);
    }

    public function getOrganizerTrips($organizer_id)
    {
        if (!$organizer_id) {
            return response()->json(['message' => "Organizer Not Found"], 404);
        }

        $trips = Trip::where('organizer_id', $organizer_id)->get();

        if (!$trips) {
            return response()->json(['error' => 'Trips not Found'], 404);
        }

        foreach ($trips as $trip) {
            $trip->imgs = json_decode($trip->imgs);
        }
        return response()->json(['trips' => $trips], 200);
    }
}
