<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\TouristArea;
use App\Traits\FacilityCreateTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\PhotoTrait;

class TouristAreaController extends Controller
{
    use FacilityCreateTrait, PhotoTrait;

    public function createArea(Request $request)
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

        $touristArea = TouristArea::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'location_id' => $location->id,
            'imgs' => $images
        ]);
        $touristArea->imgs = json_decode($touristArea->imgs);
        return response()->json(['touristArea'=> $touristArea,'message' => 'TouristArea created successfully'], 200);
    }

    public function getTouristArea($TouristArea_id)
    {
        try {
            $touristArea = TouristArea::with('location')->findOrFail($TouristArea_id);
            $touristArea->imgs = json_decode($touristArea->imgs);
            return response()->json($touristArea, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'TouristArea not Found'], 404);
        }
    }

    public function getTouristAreas()
    {
        $touristAreas = TouristArea::with('location')->get();

        if ($touristAreas->isEmpty()) {
            return response()->json(['error' => 'TouristAreas not Found'], 404);
        }
        foreach ($touristAreas as $touristArea) {
            $touristArea->imgs = json_decode($touristArea->imgs);
        }
        return response()->json(['touristAreas' => $touristAreas], 200);
    }

}
