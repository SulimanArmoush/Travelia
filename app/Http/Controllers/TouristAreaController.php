<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\TouristArea;
use App\Traits\AreaImages;
use App\Traits\FacilityCreateTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TouristAreaController extends Controller
{
    use FacilityCreateTrait, AreaImages;

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

            'img' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
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

        $image = $this->areaSaveImage($request->img);

        TouristArea::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'location_id' => $location->id,
            'img' => $image,
        ]);
        return response()->json(['message' => 'TouristArea created successfully'], 200);
    }

    public function getTouristArea($touristArea_id)
    {
        try {
            $touristArea = TouristArea::with('location')->findOrFail($touristArea_id);
            return response()->json($touristArea, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'TouristArea not Found'], 404);
        }
    }

    public function getTouristAreas()
    {
        $touristAreas = TouristArea::all();
        if ($touristAreas->isEmpty()) {
            return response()->json(['error' => 'TouristAreas not Found'], 404);
        }

        $formattedAreas = [];
        foreach ($touristAreas as $area) {
            $formattedAreas[] = [
                'id' => $area->id,
                'name' => $area->name,
                'type'=>$area->type,
                'img'=>$area->img,
                'country'=>$area->location->country
            ];
        }
        return response()->json(['touristAreas' => $formattedAreas], 200);
    }

}
