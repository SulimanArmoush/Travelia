<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Finance;
use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Organizers\Trip;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\TouristArea;
use App\Traits\MyTrait;
use App\Traits\FacilityCreateTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    use FacilityCreateTrait, MyTrait;

    public function createAccount(Request $request): JsonResponse
    {
        if (Auth::user()->confirmation == '2') {
            return response()->json(['message' => 'You already have Account']);
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
        if (Auth::user()->role_id == 5) {
            Auth::user()->update(['type' => $facility->transporter->type]);
        }
        return response()->json(['message' => 'Your Account created successfully']);
    }

    public function getProfile(): JsonResponse
    {
        $user = Auth::user();

        $id = 0;
        $facility = $user->facility;
        if (!$facility) {
            return response()->json(['error' => 'Facility Not Found']);
        }
        if ($facility->hotel) {
            $id = $facility->hotel->id;
        } else if ($facility->restaurant) {
            $id = $facility->restaurant->id;
        } else if ($facility->transporter) {
            $id = $facility->transporter->id;
        }else if ($facility->organizer) {
            $id = $facility->organizer->id;
        }
        else {
            return response()->json(['error' => 'Not Found']);
        }

        if ($id == 0) {
            return response()->json(['error' => 'Not']);
        }

        return response()->json([
            'id' => $id,
            'name' => $user->firstName . ' ' . $user->lastName,
            'role' => $user->role->name,
            'email' => $user->email,
            'facilityName' => $user->facility->name,
            'country' => $user->facility->location->country,
            'wallet' => $user->wallet,
            'photo' => $user->photo,
            'facilityPhoto' => $user->facility->img,
            'type' => $user->type
        ]);
    }

    public function imgUpload(Request $request): JsonResponse
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

        return response()->json(['imgs' => $image, 'message' => 'Your images Added successfully']);
    }

    public function getNearHotel($area_id): JsonResponse
    {

        $area = TouristArea::find($area_id);
        if (!$area) {
            return response()->json(['error' => 'Area not Found']);
        }
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
            return response()->json(['error' => "near Not Found"]);
        }
        return response()->json(['near' => $near]);

    }

    public function getNearRestaurant($area_id): JsonResponse
    {

        $area = TouristArea::find($area_id);
        if (!$area) {
            return response()->json(['error' => 'Area not Found']);
        }
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
            return response()->json(['error' => "near Not Found"]);
        }
        return response()->json(['near' => $near]);

    }


    public function getFinances(): JsonResponse
    {
        $fins = Auth::user()->finances;
        if ($fins->isEmpty()) {
            return response()->json(['error' => "Report is Empty"]);
        }
        $totalBalance = Auth::user()->wallet;
        $format = [];
        foreach ($fins as $fin) {
            $format[] = [
                'id' => $fin->id,
                'Description' => $fin->Description,
                'name' => $fin->fromUser->firstName . ' ' . $fin->fromUser->lastName,
                'from' => $fin->fromUser->email,
                'Intake' => intval($fin->Intake),
                'before' => intval($fin->before),
                'after' => intval($fin->after),
                'Date' => $fin->created_at->format('d-m-Y')
            ];
        }
        return response()->json(['TotalBalance' => $totalBalance, 'Report' => $format]);
    }


    public function makeContact(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'msg' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        Contact::create([
            'from' => Auth::id(),
            'title' => $request->title,
            'msg' => $request->msg,
        ]);
        return response()->json(['message' => "your message sent successfully"]);
    }

    public function getWallet(): JsonResponse
    {
        $Balance = Auth::user()->wallet;
        return response()->json(['Balance' => $Balance]);
    }

    public function organizerBooking(): JsonResponse
    {
        $facility = Auth::user()->facility;
        $trips = collect();

        if ($facility->hotel) {
            $id = $facility->hotel->id;
            $trips = Trip::where('hotel_id', $id)->get();
        } else if ($facility->restaurant) {
            $id = $facility->restaurant->id;
            $trips = Trip::where('restaurant_id', $id)->get();
        } else if ($facility->transporter) {
            $id = $facility->transporter->id;
            $trips = Trip::where('transporter_id', $id)->get();
        }
        if ($trips->isEmpty()) {
            return response()->json(['message' => 'empty list']);
        }

        $formatted = $trips->map(function ($trip) {
            return [
                'organizer' => $trip->organizer->facility->name,
                'organizerImg' => $trip->organizer->facility->img,
                'strDate' => $trip->strDate,
                'endDate' => $trip->endDate,
                'capacity' => $trip->capacity . '/' . $trip->totalCapacity,
                'touristArea' => $trip->touristArea->name,
                'touristAreaImg' => $trip->touristArea->img,
            ];
        });

        return response()->json(['trips' => $formatted]);
    }

}
