<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Finance;
use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\TouristArea;
use App\Traits\MyTrait;
use App\Traits\FacilityCreateTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    use FacilityCreateTrait, MyTrait ;

    public function createAccount(Request $request)
    {
        if(Auth::user()->confirmation == '2'){
            return response()->json(['message' => 'You already have Account'], 200);
        }

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

            'img' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
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

        $facility = $this->createFacility(
            $request->name,
            $request->description,
            $location->id,
            Auth::id(),
            $request->img
        );

        $roles = [
            2 => Organizer::class,
            3 => Hotel::class,
            4 => Restaurant::class,
            5 => Transporter::class,
        ];

        if (array_key_exists(Auth::user()->role_id, $roles)) {
            $roles[Auth::user()->role_id]::create([
                'facility_id' => $facility->id,
                'type' => $request->type,
            ]);
        }
        Auth::user()->update(['confirmation' => '2']);

        return response()->json(['message' => 'Your Account created successfully'], 200);
    }

    public function getProfile()
    {
        $user = Auth::user();
        $facility = $user->facility;
        return response()->json([
            'name'=> $user->firstName .' '. $user->lastName,
            'role'=> $user->role->name,
            'email'=>$user->email,
            'facilityName'=> $facility->name,
            'country'=>$facility->location->country,
            'wallet'=>$user->wallet,
            'photo'=>$user->photo,
            'facilityPhoto'=> $facility->img,
        ], 200);
    }

    public function imgUpload(Request $request)
    {
        if (!$request->hasFile('img')) {
            return response()->json(['error' => 'No images provided'], 400);
        }
        $validator = validator::make($request->all(), [
            'img' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $facility = auth()->user()->facility;

        $image = $this->saveImage($request->img);

        $facility->update([
            'img' => $image,
        ]);

        return response()->json(['imgs' => $image, 'message' => 'Your images Added successfully'], 200);
    }

    public function getNearHotel($area_id)
    {
        try {
            $area = TouristArea::findOrFail($area_id);
            $hotels = Hotel::all();
            $near = [];
            foreach ($hotels as $hotel) {
                $dist = $this->distance(
                    $hotel->facility->location->latitude,
                    $hotel->facility->location->longitude,
                    $area->location->latitude,
                    $area->location->longitude,
                );
                if ($dist <= 20.0) {
                    $near [] = $hotel;
                }
            }
            if (empty($near)) {
                return response()->json(['error' => "near Not Found"], 200);
            }
            return response()->json(['near' => $near], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Area Not Found"], 404);
        }
    }
    public function getNearRestaurant($area_id)
    {
        try {
            $area = TouristArea::findOrFail($area_id);
            $restaurants = Restaurant::all();
            $near = [];
            foreach ($restaurants as $restaurant) {
                $dist = $this->distance(
                    $restaurant->facility->location->latitude,
                    $restaurant->facility->location->longitude,
                    $area->location->latitude,
                    $area->location->longitude,
                );
                if ($dist <= 20.0) {
                    $near [] = $restaurant;
                }
            }
            if (empty($near)) {
                return response()->json(['error' => "near Not Found"], 200);
            }
            return response()->json(['near' => $near], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Area Not Found"], 404);
        }
    }

    public function getFinances()
    {
        $fins = Finance::where('to',Auth::id())->get();
        if ($fins->isEmpty()) {
            return response()->json(['error' => "Report is Empty"], 200);
        }
        $format = [];
        foreach ($fins as $fin) {
            $format[] = [
                'id' => $fin->id,
                'Description' => $fin->Description,
                'name' => $fin->fromUser->firstName .' '. $fin->fromUser->lastName,
                'from' => $fin->fromUser->email,
                'Intake' => $fin->Intake,
                'Expense' => $fin->Expense,
            ];
        }
        return response()->json(['Report' => $format], 200);
    }

    public function makeContact(Request $request)
    {
        $validator = validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'msg' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
         Contact::create([
            'from'=> Auth::id(),
            'title' => $request->title,
            'msg' => $request->msg,
        ]);
        return response()->json(['message' => "your massage sended successfully"], 200);
    }
}
