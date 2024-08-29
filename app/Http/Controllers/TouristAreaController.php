<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\TouristArea;
use App\Traits\MyTrait;
use App\Traits\FacilityCreateTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class TouristAreaController extends Controller
{
    use FacilityCreateTrait, MyTrait;

    public function createArea(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'name' => ['required', 'string', 'max:25'],
            'description' => ['required', 'string', 'max:1000'],
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
        return response()->json(['message' => 'TouristArea created successfully']);
    }

    public function getTouristArea($touristArea_id): JsonResponse
    {
        $touristArea = TouristArea::with('location')->find($touristArea_id);
        if (!$touristArea) {
            return response()->json(['error' => 'TouristArea not Found']);
        }
        return response()->json($touristArea);
    }

    public function getTouristAreas(): JsonResponse
    {
        $touristAreas = TouristArea::all();
        if ($touristAreas->isEmpty()) {
            return response()->json(['error' => 'TouristAreas not Found']);
        }

        $formattedAreas = [];
        foreach ($touristAreas as $area) {
            $formattedAreas[] = [
                'id' => $area->id,
                'name' => $area->name,
                'type' => $area->type,
                'img' => $area->img,
                'country' => $area->location->country,
            ];
        }
        return response()->json([
            'touristAreas' => $formattedAreas,
        ]);
    }

    public function getAreas(): JsonResponse
    {
        $areas = TouristArea::with('location')->get();
        if ($areas->isEmpty()) {
            return response()->json(['error' => 'TouristAreas not Found']);
        }

        return response()->json([
            'Areas' => $areas,
        ]);
    }



    public function search($query): JsonResponse
    {

        $results = TouristArea::search($query)->get();

        $formattedResults = $results->map(function ($result) {
            return [
                'id' => $result->id,
                'name' => $result->name,
                'description' => $result->description,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedResults,
            'message' => 'Search results retrieved successfully.',
        ]);
    }


}
