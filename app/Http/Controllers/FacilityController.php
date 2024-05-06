<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Requirement;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Transporters\Transporter;

use Illuminate\Support\Facades\Auth;
use App\Traits\facilityCreateTrait;
use App\Traits\PhotoTrait;
use Illuminate\Http\Request;
use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Support\Facades\Validator;

class FacilityController extends Controller
{
    use facilityCreateTrait, PhotoTrait;

    public function createAccount(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => ['required', 'string', 'max:25'],
            'description' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'area' => ['required', 'string'],
            'type' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        $location = $this->createLocation($request->latitude, $request->longitude, $request->area);
        if ($location === null) {
            return response()->json(['error' => 'Failed to create location. Area not found.'], 400);
        }
        $facility = $this->createFacility($request->name, $request->description, $location, Auth::id());

        $roles = [
            2 => Organizer::class,
            3 => Hotel::class,
            4 => Restaurant::class,
            5 => Transporter::class,
        ];

        if (array_key_exists(auth()->user()->role_id, $roles)) {
            $roles[auth()->user()->role_id]::create([
                'facility_id' => $facility,
                'type' => $request->type,
            ]);
        }

        return response()->json(['message' => 'Your Account created successfully'], 200);
    }



    public function imgUpload(Request $request)
    {

        if (!$request->hasFile('imgs')) {
            return response()->json(['error' => 'No images provided'], 400);
        }

        $validator = validator::make($request->all(), [
            'imgs' => ['min:1', 'max:3'],
            'imgs.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $facility = Facility::find(auth()->user()->facility()->first()->id);
        if (!$facility) {
            return response()->json(['error' => 'Facility not found'], 404);
        }


        $images = $this->upload($request->imgs);

        $facility->update([
            'imgs' => $images,
        ]);

        return response()->json(['imgs' => $images, 'message' => 'Your images Added successfully'], 200);
    }
}
