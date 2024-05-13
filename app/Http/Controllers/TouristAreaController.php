<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\TouristArea;
use App\Traits\FacilityCreateTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\PhotoTrait;

class TouristAreaController extends Controller
{
    use FacilityCreateTrait, PhotoTrait;

    public function createAccount(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => ['required', 'string', 'max:25'],
            'description' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],

            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'address' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],

            'imgs' => ['required','min:3', 'max:3'],
            'imgs.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        $location = $this->createLocation(
            $request->latitude,
            $request->longitude,
            $request->address,
            $request->country,
            $request->state,
            $request->city
        );

        $images = $this->upload($request->imgs);

        TouristArea::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'location_id' => $location->id,
            'imgs' => $images
        ]);

        return response()->json(['message' => 'TouristArea created successfully'], 200);
    }

    public function getTouristArea($TouristArea_id)
    {
        $touristArea = TouristArea::with('location')->find($TouristArea_id);

        if (!$touristArea) {
            return response()->json(['error' => 'TouristArea not Found'], 404);
        }
        $touristArea->imgs = json_decode($touristArea->imgs);

        return response()->json(['touristArea' => $touristArea], 200);
    }

    public function getTouristAreas()
    {
        $touristAreas = TouristArea::with('location')->all();

        if (!$touristAreas) {
            return response()->json(['error' => 'TouristAreas not Found'], 404);
        }
        foreach ($touristAreas as $touristArea) {
            $touristArea->imgs = json_decode($touristArea->imgs);
        }
        return response()->json(['touristAreas' => $touristAreas], 200);
    }

}
