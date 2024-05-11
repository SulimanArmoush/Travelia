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

    public function createTrip(Request $request, $touristArea_id)
    {
        $organizer = auth()->user()->facility->organizer;

        $validator = validator::make($request->all(), [
            'cost' => ['required', 'numeric'],
            'dateTime' => ['required', 'date'],
            'totalCapacity' => ['required', 'integer'],
            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'address' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'country_code' => ['required', 'string'],
            'imgs' => ['min:1', 'max:3'],
            'imgs.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
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
            $request->country_code
        );

        Trip::create([
            'organizer_id' => $organizer->id,
            'cost' => $request->cost,
            'dateTime' => $request->dateTime,
            'totalCapacity' => $request->totalCapacity,
            'imgs' => $images,
            'location_id' => $location->id,
            'touristArea' => $touristArea_id,
        ]);

        return response()->json(['message' => 'Your Trip created successfully'], 200);
    }
}
