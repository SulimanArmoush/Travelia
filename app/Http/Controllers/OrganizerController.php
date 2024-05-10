<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Organizers\Trip;
use App\Traits\facilityCreateTrait;
use App\Traits\PhotoTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizerController extends Controller
{/*
    use facilityCreateTrait, PhotoTrait;

    public function createTrip(Request $request)
    {
        $organizer = auth()->user()->facility->organizer;

        $validator = validator::make($request->all(), [
            'totalCapacity' => ['required', 'integer'],
            'cost' => ['required', 'numeric'],
            'imgs' => ['min:1', 'max:3'],
            'imgs.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $images = $this->upload($request->imgs);

        Trip::create([
                'organizer_id' => $organizer->id,
                'totalCapacity' => $request->totalCapacity,
                'cost' => $request->cost,
                'imgs' => $images,
        ]);

        return response()->json(['message' => 'Your Trip created successfully'], 200);
    }*/
}
