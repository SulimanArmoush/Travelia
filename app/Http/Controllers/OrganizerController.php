<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Not;
use App\Models\Permissions\User;
use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\Facilities\Favorite;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Organizers\Trip;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use App\Models\TheWorld\TouristArea;
use App\Traits\FacilityCreateTrait;
use App\Traits\MyTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\NotificationTrait;

class OrganizerController extends Controller
{
    use facilityCreateTrait, MyTrait, NotificationTrait;


    public function createTrip(Request $request): JsonResponse
    {
        if (Auth::user()->role_id != 2) {
            return response()->json(['error' => 'You are not a Trip Organizer'], 400);
        }
        $validator = validator::make($request->all(), [
            'cost' => ['required', 'numeric'],
            'strDate' => ['required', 'date'],
            'endDate' => ['required', 'date'],

            'totalCapacity' => ['required', 'integer'],

            'touristArea_id' => ['required', 'integer'],
            'hotel_id' => ['required', 'integer'],
            'restaurant_id' => ['required', 'integer'],
            'transporter_id' => ['required', 'integer'],

            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'address' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $transporter = Transporter::find($request->transporter_id);
        $area = TouristArea::find($request->touristArea_id);
        if (!$area) {
            return response()->json(['error' => 'Area not Found']);
        }

        $dist = $this->distance(
            $request->latitude,
            $request->longitude,
            $area->location->latitude,
            $area->location->longitude
        );
        if ($dist < 200.0 && $transporter->type == 'air') {
            return response()->json([
                'error' => 'How Would You Like To Use A Plane And Your Distance Is Lower Than 200 KM'
            ]);
        }
        if ($dist > 2000.0 && $transporter->type == 'land') {
            return response()->json([
                'error' => 'بدك تمشي بالباص 2000 كيلومتر ؟؟!!(:!!؟؟'
            ]);
        }

        $organizer = auth()->user()->facility->organizer;
        $hotel = Facility::find($request->hotel_id)->hotel;
        if (!$hotel) {
            return response()->json(['error' => 'Hotel not Found']);
        }
        $restaurant = Facility::find($request->restaurant_id)->restaurant;
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not Found']);
        }

        $location = $this->createLocation(
            $request->latitude,
            $request->longitude,
            $request->address,
            $request->country,
            $request->state,
            $request->city
        );

        Trip::create([
            'organizer_id' => $organizer->id,
            'cost' => $request->cost,
            'strDate' => $request->strDate,
            'endDate' => $request->endDate,
            'totalCapacity' => $request->totalCapacity,
            'img' => $area->img,
            'strLocation' => $location->id,
            'touristArea_id' => $area->id,
            'hotel_id' => $hotel->id,
            'restaurant_id' => $restaurant->id,
            'transporter_id' => $transporter->id,
        ]);

        $users = Favorite::where('organizer_id', $organizer->id)
            ->pluck('user_id');

        foreach ($users as $user_id) {
            $user = User::find($user_id);
            if ($user) {
                $this->send($user, 'new trip from ' . $organizer->facility->name,
                    'A new trip to ' . $area->name . ' has been added');
            }
        }

        return response()->json(['message' => 'Your Trip created successfully']);
    }

    public function deleteTrip($trip_id): JsonResponse
    {
        $trip = Trip::find($trip_id);
        if (!$trip) {
            return response()->json(['error' => 'Trip Not Found']);
        }

        $organizer = $trip->organizer->facility->user;
        if ($organizer->id != Auth::id()) {
            return response()->json(['error' => 'this trip is not belongs to you']);
        }

        if (Carbon::now()->greaterThan(Carbon::parse($trip->strDate)->subDay())) {
            return response()->json(['error' => 'you cant delete this trip after now']);
        }

        foreach ($trip->tripReservations as $reservation) {

            $before = $organizer->wallet;
            $organizer->decrement('wallet', $reservation->cost);
            $after = $organizer->wallet;

            $reservation->user->increment('wallet', $reservation->cost);

            Finance::create([
                'from' => $reservation->user->id,
                'to' => $organizer->id,
                'before' => $before,
                'after' => $after,
                'Intake' => $reservation->cost,
                'Description' => 'for delete a trip to ' . $trip->area->name . ' and canceled reservations',
            ]);

            $this->send($reservation->user, 'Canceled reservation', 'the Trip to ' . $trip->area->name . ' has been canceled, and your money returned to you');

        }

        $trip->delete();
        return response()->json(['message' => 'you did delete this trip']);
    }

    public function getTrip($trip_id): JsonResponse
    {
        $trip = Trip::with(
            'location',
            'hotel.facility',
            'restaurant.facility',
            'transporter.facility',
            'touristArea.location'
        )->find($trip_id);
        if (!$trip) {
            return response()->json(['error' => 'Trip not Found']);
        }
        return response()->json($trip);
    }

    public function getTrips(): JsonResponse
    {
        $trips = Trip::all();
        if ($trips->isEmpty()) {
            return response()->json(['error' => 'Trips not Found']);
        }

        $format = [];
        foreach ($trips as $trip) {
            $format[] = [
                "id" => $trip->id,
                "organizer" => $trip->organizer->facility->name,
                "cost" => $trip->cost,
                "strDate" => $trip->strDate,
                "endDate" => $trip->endDate,
                "totalCapacity" => $trip->totalCapacity,
                "img" => $trip->img,
                "strLocation" => $trip->location->address,
                "touristArea" => $trip->touristArea->name,
                "hotel" => $trip->hotel->facility->name,
                "restaurant" => $trip->restaurant->facility->name,
                "transporter" => $trip->transporter->facility->name,
                "availablePlaces" => $trip->totalCapacity - $trip->capacity,
            ];
        }

        return response()->json(['trips' => $format]);
    }

    public function getOrganizerTrips(): JsonResponse
    {
        $trips = auth()->user()->facility->organizer->trips;
        if ($trips->isEmpty()) {
            return response()->json(['error' => 'Trips not Found']);
        }
        $format = [];
        foreach ($trips as $trip) {
            $format[] = [
                "id" => $trip->id,
                "organizer" => $trip->organizer->facility->name,
                "cost" => $trip->cost,
                "strDate" => $trip->strDate,
                "endDate" => $trip->endDate,
                "totalCapacity" => $trip->totalCapacity,
                "img" => $trip->img,
                "strLocation" => $trip->location->address,
                "touristArea" => $trip->touristArea->name,
                "hotel" => $trip->hotel->facility->name,
                "restaurant" => $trip->restaurant->facility->name,
                "transporter" => $trip->transporter->facility->name,
                "availablePlaces" => $trip->totalCapacity - $trip->capacity
            ];
        }
        return response()->json(['trips' => $format]);
    }

    public function organizerTrips(): JsonResponse
    {
        $trips = auth()->user()->facility->organizer->trips;
        if ($trips->isEmpty()) {
            return response()->json(['error' => 'Trips not Found']);
        }
        $pastTrips = [];
        $currentTrips = [];
        $upcomingTrips = [];

        foreach ($trips as $trip) {
            $formattedTrip = [
                "id" => $trip->id,
                "organizer" => $trip->organizer->facility->name,
                "cost" => $trip->cost,
                "strDate" => $trip->strDate,
                "endDate" => $trip->endDate,
                "totalCapacity" => $trip->totalCapacity,
                "img" => $trip->img,
                "strLocation" => $trip->location->address,
                "touristArea" => $trip->touristArea->name,
                "hotel" => $trip->hotel->facility->name,
                "restaurant" => $trip->restaurant->facility->name,
                "transporter" => $trip->transporter->facility->name,
                "availablePlaces" => $trip->totalCapacity - $trip->capacity
            ];

            if ($trip->endDate < now()) {
                $pastTrips[] = $formattedTrip;
            } elseif ($trip->strDate <= now() && $trip->endDate >= now()) {
                $currentTrips[] = $formattedTrip;
            } else {
                $upcomingTrips[] = $formattedTrip;
            }
        }

        return response()->json([
            'pastTrips' => $pastTrips,
            'currentTrips' => $currentTrips,
            'upcomingTrips' => $upcomingTrips
        ]);
    }

    public function getAvailableTrips($organizer_id): JsonResponse
    {
        $organizer = Organizer::find($organizer_id);
        if (!$organizer) {
            return response()->json(['error' => 'Organizer Not Found'], 401);
        }
        $availableTrips = [];
        foreach ($organizer->trips as $trip) {
            if ($trip->totalCapacity - $trip->capacity <= 0) {
                continue;
            }
            if ($trip->strDate < now()) {
                continue;
            }

            $availableTrips [] = [
                "id" => $trip->id,
                "organizer" => $trip->organizer->facility->name,
                "cost" => $trip->cost,
                "strDate" => $trip->strDate,
                "endDate" => $trip->endDate,
                "totalCapacity" => $trip->totalCapacity,
                "img" => $trip->img,
                "strLocation" => $trip->location->address,
                "touristArea" => $trip->touristArea->name,
                "hotel" => $trip->hotel->facility->name,
                "restaurant" => $trip->restaurant->facility->name,
                "transporter" => $trip->transporter->facility->name,
                "availablePlaces" => $trip->totalCapacity - $trip->capacity
            ];
        }
        if (empty($availableTrips)) {
            return response()->json(['error' => 'No Available Trips Found']);
        }


        return response()->json(['availableTrips' => $availableTrips]);
    }

    public function getAllAvailableTrips(): JsonResponse
    {
        $trips = Trip::all();
        if (!$trips) {
            return response()->json(['error' => 'Trips Not Found'], 401);
        }
        $availableTrips = [];
        foreach ($trips as $trip) {
            if ($trip->totalCapacity - $trip->capacity <= 0) {
                continue;
            }
            if ($trip->strDate < now()) {
                continue;
            }

            $availableTrips [] = [
                "id" => $trip->id,
                "organizer" => $trip->organizer->facility->name,
                "cost" => $trip->cost,
                "strDate" => $trip->strDate,
                "endDate" => $trip->endDate,
                "totalCapacity" => $trip->totalCapacity,
                "img" => $trip->img,
                "strLocation" => $trip->location->address,
                "touristArea" => $trip->touristArea->name,
                "hotel" => $trip->hotel->facility->name,
                "restaurant" => $trip->restaurant->facility->name,
                "transporter" => $trip->transporter->facility->name,
                "availablePlaces" => $trip->totalCapacity - $trip->capacity,
            ];
        }
        if (empty($availableTrips)) {
            return response()->json(['error' => 'No Available Trips Found']);
        }


        return response()->json(['availableTrips' => $availableTrips]);
    }

}
