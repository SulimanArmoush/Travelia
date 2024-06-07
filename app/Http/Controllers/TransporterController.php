<?php

namespace App\Http\Controllers;

use App\Models\Permissions\User;
use App\Models\TheWorld\Facilities\Transporters\Routing;
use App\Models\TheWorld\Facilities\Transporters\Transportation;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use App\Models\TheWorld\TouristArea;
use App\Traits\MyTrait;
use App\Traits\FacilityCreateTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransporterController extends Controller
{
    use FacilityCreateTrait, MyTrait;

    public function createAirTransportations(Request $request)
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
            return response()->json(['message' => 'Your Transportation created successfully'], 200);
    }

    public function createLandTransportations(Request $request)
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

            return response()->json(['message' => 'Your Transportation created successfully'], 200);
    }

    public function getTransportation($transportation_id)
    {
        try {
            $transportation = Transportation::findOrFail($transportation_id);
            return response()->json($transportation, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Transportation not Found'], 404);
        }
    }

    public function getTransportations($transporter_id)
    {
        try {
            $transporter = Transporter::findOrFail($transporter_id);
            $transportations = Transportation::where('transporter_id', $transporter->id)
                ->paginate(10);

            if ($transportations->isEmpty()) {
                return response()->json(['error' => 'Transportations not Found'], 200);
            }
            return response()->json($transportations, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Transporter not Found'], 404);
        }
    }

    public function getAvailableTransportations($transporter_id)
    {
        try {
            $transporter = Transporter::findOrFail($transporter_id);
            $transportations = Transportation::where('transporter_id', $transporter->id)
                ->where('status', 'available')
                ->paginate(10);

            if ($transportations->isEmpty()) {
                return response()->json(['error' => 'Transportations not Found'], 200);
            }
            return response()->json(['transportations' => $transportations], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Transporter not Found'], 404);
        }
    }

    public function createRouting(Request $request)
    {
        $validator = validator::make($request->all(), [
            'transportation_id' => ['required', 'integer'],

            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
            'address' => ['required', 'string'],
            'country' => ['required', 'string'],
            'state' => ['required', 'string'],
            'city' => ['required', 'string'],

            'touristArea_id' => ['required', 'integer'],
            'dateTime' => ['required', 'date'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        try {
            $transportation = Transportation::findOrFail($request->transportation_id);
            $touristArea = TouristArea::findOrFail($request->touristArea_id);

            $location = $this->createLocation(
                $request->latitude,
                $request->longitude,
                $request->address,
                $request->country,
                $request->state,
                $request->city
            );

            $dist = $this->distance(
                $location->latitude,
                $location->longitude,
                $touristArea->location->latitude,
                $touristArea->location->longitude
            );

            $cost = $dist * $transportation->cost;

            Routing::create([
                'transportation_id' => $transportation->id,
                'strLocation' => $location->id,
                'touristArea_id' => $touristArea->id,
                'dateTime' => $request->dateTime,
                'cost' => $cost
            ]);
            return response()->json(['message' => "Routing created successfully"], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Invalid foreign key"], 404);
        }
    }

    public function getRoute($route_id)
    {
        try {
            $route = Routing::with('location', 'transportation', 'touristArea.location')->findOrFail($route_id);
            return response()->json(['route' => $route], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Routing Not Found"], 404);
        }
    }

    public function getNearRoute(Request $request)
    {
        $validator = validator::make($request->all(), [
            'latitude' => ['required', 'string'],
            'longitude' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $routes = Routing::with('location', 'transportation', 'touristArea.location')->get();

        if ($routes->isEmpty()) {
            return response()->json(['error' => "Routes Not Found"], 200);
        }

        $nearRoutes = [];

        foreach ($routes as $route) {
            $dist = $this->distance(
                $request->latitude,
                $request->longitude,
                $route->location->latitude,
                $route->location->longitude,
            );

            if ($dist <= 20.0) {
                $nearRoutes [] = $route;
            }
        }

        if (!$nearRoutes) {
            return response()->json(['error' => "Routes Not Found"], 200);
        }

        return response()->json(['nearRoute' => $nearRoutes], 200);
    }

    public function getOwnerRoutes($transporter_id)
    {
        try {
            $transporter = Transporter::findOrFail($transporter_id);
            $transportationIds = $transporter->transportations->pluck('id');

            $routes = Routing::whereIn('transportation_id', $transportationIds)
                ->with('location', 'transportation', 'touristArea.location')
                ->get();

            return response()->json(['route' => $routes], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Transporter Not Found"], 404);
        }
    }

    public function getTransporters()
    {
        $users = User::where('role_id', '5')->where('confirmation', '2')->get();
        if ($users->isEmpty()) {
            return response()->json(['error' => 'Users not found'], 200);
        }

        $formatted = [];
        foreach ($users as $user) {
            $formatted[] = [
                'id' => $user->facility->transporter->id,
                'name' => $user->facility->name,
                'type' => $user->facility->transporter->type,
                'img' => $user->facility->img,
            ];
        }
        return response()->json(['transporters' => $formatted], 200);
    }

}
