<?php

namespace App\Http\Controllers;

use App\Models\Permissions\User;
use App\Models\TheWorld\Facilities\Reservation;
use App\Models\TheWorld\Facilities\Transporters\Routing;
use App\Models\TheWorld\Facilities\Transporters\Transportation;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use App\Models\TheWorld\TouristArea;
use App\Traits\MyTrait;
use App\Traits\FacilityCreateTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransporterController extends Controller
{
    use FacilityCreateTrait, MyTrait;

    public function createAirTransportations(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'num1' => ['integer'],
            'totalCapacity1' => ['integer'],
            'cost1' => ['numeric'],
            'num2' => ['integer'],
            'totalCapacity2' => ['integer'],
            'cost2' => ['numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $transporter = auth()->user()->facility->transporter;

        if ($transporter->type != 'air') {
            return response()->json(['error' => 'your not in airType'], 400);
        }

        if ($request->num1) {
            for ($i = 0; $i < $request->num1; $i++) {
                Transportation::create([
                    'transporter_id' => $transporter->id,
                    'totalCapacity' => $request->totalCapacity1,
                    'cost' => $request->cost1,
                    'type' => 'normalPlane',
                ]);
            }
        }
        if ($request->num2) {
            for ($i = 0; $i < $request->num2; $i++) {
                Transportation::create([
                    'transporter_id' => $transporter->id,
                    'totalCapacity' => $request->totalCapacity2,
                    'cost' => $request->cost2,
                    'type' => 'businessClassPlane',
                ]);
            }
        }
        return response()->json(['message' => 'Your Transportation created successfully']);
    }

    public function createLandTransportations(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'num1' => ['integer'],
            'totalCapacity1' => ['integer'],
            'cost1' => ['numeric'],
            'num2' => ['integer'],
            'totalCapacity2' => ['integer'],
            'cost2' => ['numeric'],
            'num3' => ['integer'],
            'totalCapacity3' => ['integer'],
            'cost3' => ['numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        $transporter = auth()->user()->facility->transporter;

        if ($transporter->type != 'land') {
            return response()->json(['error' => 'your not in landType'], 400);
        }

        if ($request->num1) {
            for ($i = 0; $i < $request->num1; $i++) {
                Transportation::create([
                    'transporter_id' => $transporter->id,
                    'totalCapacity' => $request->totalCapacity1,
                    'cost' => $request->cost1,
                    'type' => 'pullman',
                ]);
            }
        }
        if ($request->num2) {
            for ($i = 0; $i < $request->num2; $i++) {
                Transportation::create([
                    'transporter_id' => $transporter->id,
                    'totalCapacity' => $request->totalCapacity2,
                    'cost' => $request->cost2,
                    'type' => 'bus',
                ]);
            }
        }
        if ($request->num3) {
            for ($i = 0; $i < $request->num3; $i++) {
                Transportation::create([
                    'transporter_id' => $transporter->id,
                    'totalCapacity' => $request->totalCapacity3,
                    'cost' => $request->cost3,
                    'type' => 'van',
                ]);
            }
        }

        return response()->json(['message' => 'Your Transportation created successfully']);
    }

    public function getTransportation($transportation_id): JsonResponse
    {
        $transportation = Transportation::find($transportation_id);
        if (!$transportation) {
            return response()->json(['error' => 'Transportation not Found']);
        }
        return response()->json($transportation);
    }

    public function getTransportations($transporter_id): JsonResponse
    {
        $transporter = Transporter::find($transporter_id);
        if (!$transporter) {
            return response()->json(['error' => 'Transporter not Found']);
        }
        if ($transporter->transportations->isEmpty()) {
            return response()->json(['error' => 'Transportations not Found']);
        }
        return response()->json($transporter->transportations);
    }

    public function getAvailableTransportations(Request $request, $transporter_id): JsonResponse
    {
        $transporter = Transporter::find($transporter_id);
        if (!$transporter) {
            return response()->json(['error' => 'Transporter not found']);
        }
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $date = Carbon::parse($request->date)->startOfDay();
        $endDate = $date->copy()->endOfDay();

        $availableTransportations = $transporter->transportations()
            ->whereDoesntHave('routings', function ($query) use ($date, $endDate) {
                $query->whereBetween('dateTime', [$date, $endDate]);
            })->get();

        if ($availableTransportations->isEmpty()) {
            return response()->json(['error' => 'No available transportations found for the specified date'], 404);
        }

        return response()->json(['availableTransportations' => $availableTransportations]);
    }

    public function createRouting(Request $request): JsonResponse
    {
        $transporter = Auth::user()->facility->transporter;
        $validator = Validator::make($request->all(), [
            'transportation_type' => ['required', 'string'],

            'strLatitude' => ['required', 'string'],
            'strLongitude' => ['required', 'string'],
            'strAddress' => ['required', 'string'],
            'strCountry' => ['required', 'string'],
            'strState' => ['required', 'string'],
            'strCity' => ['required', 'string'],

            'endLatitude' => ['required', 'string'],
            'endLongitude' => ['required', 'string'],
            'endAddress' => ['required', 'string'],
            'endCountry' => ['required', 'string'],
            'endState' => ['required', 'string'],
            'endCity' => ['required', 'string'],

            'dateTime' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $dist = $this->distance(
            $request->strLatitude,
            $request->strLongitude,
            $request->endLatitude,
            $request->endLongitude
        );

        if ($dist < 200.0 && $transporter->type == 'air') {
            return response()->json([
                'error' => 'How Would You Like To Use A Plane And Your Distance Is Lower Than 200 KM'
            ]);
        }

        if ($dist > 2000.0 && $transporter->type == 'land') {
            return response()->json([
                'error' => 'بدك تمشي بالباص اكتر من 2000 كيلومتر ؟؟؟؟'
            ]);
        }

        $date = Carbon::parse($request->dateTime)->startOfDay();
        $endDate = $date->copy()->endOfDay();

        $AvailableTransportations = $transporter->transportations()
            ->where('type', $request->transportation_type)
            ->whereDoesntHave('routings', function ($query) use ($date, $endDate) {
                $query->whereBetween('dateTime', [$date, $endDate]);
            })->get();

        if ($AvailableTransportations->isEmpty()) {
            return response()->json(['error' => 'No available transportation found for the given date and type.']);
        }

        $transportation = $AvailableTransportations->first();

        $cost = $dist * $transportation->cost;

        $strLocation = $this->createLocation(
            $request->strLatitude,
            $request->strLongitude,
            $request->strAddress,
            $request->strCountry,
            $request->strState,
            $request->strCity
        );

        $endLocation = $this->createLocation(
            $request->endLatitude,
            $request->endLongitude,
            $request->endAddress,
            $request->endCountry,
            $request->endState,
            $request->endCity
        );

        Routing::create([
            'transportation_id' => $transportation->id,
            'strLocation' => $strLocation->id,
            'endLocation' => $endLocation->id,
            'dateTime' => $request->dateTime,
            'cost' => $cost
        ]);

        return response()->json(['message' => "Routing created successfully"]);
    }

    public function getRoute($route_id): JsonResponse
    {
        $route = Routing::with('startLocation', 'endedLocation', 'transportation')
            ->find($route_id);
        if (!$route) {
            return response()->json(['error' => 'Route not Found']);
        }
        return response()->json(['route' => $route]);
    }

    public function getAvailableRoute(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],

            'touristArea_id' => ['required', 'integer'],

            'date' => ['required', 'date'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $area = TouristArea::find($request->touristArea_id);
        if (!$area) {
            return response()->json(['error' => 'Area not Found']);
        }

        $routes = Routing::with('startLocation', 'endedLocation', 'transportation')
            ->get();
        if ($routes->isEmpty()) {
            return response()->json(['error' => "Routes Not Found"]);
        }

        $date = Carbon::parse($request->date);

        $strNearRoutes = [];
        foreach ($routes as $route) {

            $routeDate = Carbon::parse($route->dateTime);
            if (!$routeDate->isSameDay($date) && !$routeDate->isSameDay($date->copy()->subDay())) {
                continue;
            }

            $strDist = $this->distance(
                $request->latitude,
                $request->longitude,
                $route->startLocation->latitude,
                $route->startLocation->longitude,
            );

            if ($strDist <= 20.0) {
                $strNearRoutes [] = $route;
            }
        }
        if (empty($strNearRoutes)) {
            return response()->json(['error' => "There arent Routes nearby"]);
        }

        $nearRoutes = [];
        foreach ($strNearRoutes as $route) {
            $endDist = $this->distance(
                $area->location->latitude,
                $area->location->longitude,
                $route->endedLocation->latitude,
                $route->endedLocation->longitude,
            );

            if ($endDist <= 20.0) {
                $nearRoutes [] = $route;
            }
        }
        if (empty($nearRoutes)) {
            return response()->json(['error' => "There are no Routes nearby"]);
        }

        return response()->json(['nearRoute' => $nearRoutes]);
    }

    public function getTransporterRoutes(): JsonResponse
    {
        $routes = auth()->user()->facility->transporter->routs;
        if ($routes->isEmpty()) {
            return response()->json(['error' => 'Routes not Found']);
        }
        $formatted = collect();;
        foreach ($routes as $route) {

            if ($route->dateTime < now()) {
                continue;
            }

            $dist = $this->distance(
                $route->startLocation->latitude,
                $route->startLocation->longitude,
                $route->endedLocation->latitude,
                $route->endedLocation->longitude
            );

            $formatted->push([
                'id' => $route->id,
                'strAddress' => $route->startLocation->address,
                'strCountry' => $route->startLocation->country,
                'strCity' => $route->startLocation->city,
                'strState' => $route->startLocation->state,
                'endAddress' => $route->endedLocation->address,
                'endCountry' => $route->endedLocation->country,
                'endCity' => $route->endedLocation->city,
                'endState' => $route->endedLocation->state,
                'transportation' => $route->transportation->type,
                'dateTime' => Carbon::parse($route->dateTime)->format('Y-m-d'),
                'totalCapacity' => $route->transportation->totalCapacity,
                'availableCapacity' => ($route->transportation->totalCapacity - $route->capacity),
                'cost' => intval($route->cost),
                'distance' => $dist
            ]);
        }
        $sortedList = $formatted->sortBy('dateTime')->values();

        return response()->json(['routes' => $sortedList]);
    }

    public function getTransportationsForRoutes(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }
        $date = Carbon::parse($request->date)->startOfDay();
        $endDate = $date->copy()->endOfDay();

        $transporter = Auth::user()->facility->transporter;
        if (!$transporter) {
            return response()->json(['error' => 'transporter not found']);
        }

        $transportations = $transporter->transportations;

        if ($transportations->isEmpty()) {
            return response()->json(['error' => 'Transportation not found']);
        }

        if ($transporter->type == 'air') {
            $normalPlanes = [];
            $businessClassPlanes = [];
            $availableNormalPlanes = [];
            $availableBusinessClassPlanes = [];

            foreach ($transportations as $transportation) {
                if ($transportation->type == 'normalPlane') {
                    $normalPlanes [] = $transportation;
                }
                if ($transportation->type == 'businessClassPlane') {
                    $businessClassPlanes [] = $transportation;
                }
            }
            $normalPlaneCount = count($normalPlanes);
            $businessClassPlaneCount = count($businessClassPlanes);

            foreach ($normalPlanes as $normalPlane) {
                $routingsOnDate = $normalPlane->routings->filter(function ($routing) use ($date, $endDate) {
                    $routingDate = Carbon::parse($routing->dateTime);
                    return $routingDate->between($date, $endDate);
                });
                if ($routingsOnDate->isEmpty()) {
                    $availableNormalPlanes[] = $normalPlane;
                }
            }
            foreach ($businessClassPlanes as $businessClassPlane) {
                $routingsOnDate = $businessClassPlane->routings->filter(function ($routing) use ($date, $endDate) {
                    $routingDate = Carbon::parse($routing->dateTime);
                    return $routingDate->between($date, $endDate);
                });
                if ($routingsOnDate->isEmpty()) {
                    $availableBusinessClassPlanes[] = $businessClassPlane;
                }
            }

            $availableNormalPlanesCount = count($availableNormalPlanes);
            $availableBusinessClassPlanesCount = count($availableBusinessClassPlanes);

            return response()->json([
                'v1' => $normalPlaneCount,
                'availableV1' => $availableNormalPlanesCount,
                'v2' => $businessClassPlaneCount,
                'availableV2' => $availableBusinessClassPlanesCount,
                'v3' => 0,
                'availableV3' => 0]);
        }

        if ($transporter->type == 'land') {

            $pullmans = [];
            $buses = [];
            $vans = [];
            $availablePullmans = [];
            $availableBuses = [];
            $availableVans = [];

            foreach ($transportations as $transportation) {
                if ($transportation->type == 'pullman') {
                    $pullmans [] = $transportation;
                }
                if ($transportation->type == 'bus') {
                    $buses [] = $transportation;
                }
                if ($transportation->type == 'van') {
                    $vans [] = $transportation;
                }
            }
            $pullmansCount = count($pullmans);
            $busesCount = count($buses);
            $vansCount = count($vans);


            foreach ($pullmans as $pullman) {
                $routingsOnDate = $pullman->routings->filter(function ($routing) use ($date, $endDate) {
                    $routingDate = Carbon::parse($routing->dateTime);
                    return $routingDate->between($date, $endDate);
                });
                if ($routingsOnDate->isEmpty()) {
                    $availablePullmans[] = $pullman;
                }
            }
            foreach ($buses as $bus) {
                $routingsOnDate = $bus->routings->filter(function ($routing) use ($date, $endDate) {
                    $routingDate = Carbon::parse($routing->dateTime);
                    return $routingDate->between($date, $endDate);
                });
                if ($routingsOnDate->isEmpty()) {
                    $availableBuses[] = $bus;
                }
            }
            foreach ($vans as $van) {
                $routingsOnDate = $van->routings->filter(function ($routing) use ($date, $endDate) {
                    $routingDate = Carbon::parse($routing->dateTime);
                    return $routingDate->between($date, $endDate);
                });
                if ($routingsOnDate->isEmpty()) {
                    $availableVans[] = $van;
                }
            }

            $availablePullmansCount = count($availablePullmans);
            $availableBusesCount = count($availableBuses);
            $availableVansCount = count($availableVans);

            return response()->json([
                'v1' => $pullmansCount,
                'availableV1' => $availablePullmansCount,
                'v2' => $busesCount,
                'availableV2' => $availableBusesCount,
                'v3' => $vansCount,
                'availableV3' => $availableVansCount]);
        }

        return response()->json(['message' => 'Hello World']);
    }

    public function getTransporters(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'touristArea_id' => ['required', 'integer'],
        ]);

        $users = User::where('role_id', '5')->where('confirmation', '2')->get();
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found']);
        }
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

        $formatted = [];
        foreach ($users as $user) {
            if ($dist < 200.0 && $user->facility->transporter->type == 'air') {
                continue;
            }
            if ($dist > 2000.0 && $user->facility->transporter->type == 'land') {
                continue;
            }

            $formatted[] = [
                'id' => $user->facility->transporter->id,
                'name' => $user->facility->name,
                'type' => $user->facility->transporter->type,
                'img' => $user->facility->img,
            ];
        }
        if (empty($formatted)) {
            return response()->json(['error' => 'Transporters Not Found']);
        }
        return response()->json(['transporters' => $formatted]);
    }

    public function getRouteReservation($routing_id): JsonResponse
    {
        $reservations = Reservation::where('routing_id', $routing_id)->get();
        if ($reservations->isEmpty()) {
            return response()->json(['error' => 'Reservations not Found']);
        }
        $format = [];
        foreach ($reservations as $reservation) {
            $format[] = [
                'id' => $reservation->id,
                'name' => $reservation->user->firstName . ' ' . $reservation->user->lastName,
                'email' => $reservation->user->email,
                'phone' => $reservation->user->phone,
                'age' => $reservation->user->age,
                'address' => $reservation->user->address,
                'photo' => $reservation->user->photo,
                'placeNum' => $reservation->placeNum,
                'cost' => intval($reservation->placeNum * $reservation->routing->cost),
            ];
        }
        return response()->json([
            'Reservations' => $format,
        ]);
    }
}
