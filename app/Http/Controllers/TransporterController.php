<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Transporters\Transportation;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransporterController extends Controller
{
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

        try {
            $transporter = auth()->user()->facility->transporter->firstOrFail();

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
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Transporter not Found'], 404);
        }
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

        try {
            $transporter = auth()->user()->facility->transporter->firstOrFail();

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
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Transporter not Found'], 404);
        }
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
                return response()->json(['error' => 'Transportations not Found'], 404);
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
                return response()->json(['error' => 'Transportations not Found'], 404);
            }
            return response()->json(['transportations' => $transportations], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Transporter not Found'], 404);
        }
    }

}
