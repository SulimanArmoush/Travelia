<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Transporters\Transportation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransporterController extends Controller
{
    public function createTransportations(Request $request)
    {
        $transporter = auth()->user()->facility->transporter;

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
        if ($transporter->type == 'air') {
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
        }
        if ($transporter->type == 'land') {
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
                        'type' => 'van',
                    ]);
                }
            }
        }
        return response()->json(['message' => 'Your Transportation created successfully'], 200);
    }

    public function getTransportation($transportation_id)
    {
        $transportation = Transportation::find($transportation_id);
        return response()->json(['transportation' => $transportation], 200);
    }

    public function getTransportations($transporter_id)
    {
        $transportations = Transportation::Where('transporter_id', '=', $transporter_id)
            ->paginate(10);
        return response()->json(['transportations' => $transportations], 200);
    }

    public function getAvailableTransportations($transporter_id)
    {
        $transportations = Transportation::
        Where('transporter_id', $transporter_id)
            ->where('status', 'available')
            ->paginate(10);
        return response()->json(['transportations' => $transportations], 200);
    }

}
