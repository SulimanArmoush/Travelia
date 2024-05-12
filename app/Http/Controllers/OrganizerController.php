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

        $validator = validator::make($request->all(), [
            'cost'=> ['required','numeric'],
            'dateTime'=> ['required','date'],
            'totalCapacity'=> ['required','integer'],

            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'address' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],

            'imgs' => ['min:3', 'max:3'],
            'imgs.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],

            'touristArea'=>['required','integer'],
            'hotel'=>['required','integer'],
            'restaurant'=>['required','integer'],
            'transporter'=>['required','integer'],
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
            'organizer_id'=>$organizer->id,
            'cost' => $request->cost,
            'dateTime'=>$request->dateTime,
            'totalCapacity'=>$request->totalCapacity,
            'imgs' => $images,
            'strLocation'=>$location->id,
            'touristArea'=>$request->touristArea_id,
            'hotel'=>$request->hotel_id,
            'restaurant'=>$request->restaurant_id,
            'transporter'=>$request->transporter_id,
        ]);

        return response()->json(['message' => 'Your Trip created successfully'], 200);
    }

    public function getTrip($trip_id)
    {
        $trip = Trip::find($trip_id);
        if(!$trip){return response()->json(['error' => 'Trip not Found'], 404);}
        return response()->json(['trip' => $trip], 200);
    }

    public function getTrips()
    {
        $trips = Trip::all();
        if(!$trips){return response()->json(['error' => 'Trips not Found'], 404);}
        return response()->json(['trips' => $trips], 200);
    }

    public function getOrganizerTrips($organizer_id)
    {
        $trips = Trip::where('organizer_id',$organizer_id)->get();
        if(!$trips){return response()->json(['error' => 'Trips not Found'], 404);}
        return response()->json(['trips' => $trips], 200);
    }
}
