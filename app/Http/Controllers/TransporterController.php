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
        $types = $transporter->type == 'air' ? Transportation::$airTypes : Transportation::$landTypes;

        $validator = validator::make($request->all(), [
            'totalCapacity' => ['required', 'integer'],
            'cost' => ['required', 'numeric'],
            'type' => ['required', Rule::in($types)],
            'number' => ['required', 'integer'],

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        for ($i = 0; $i < $request->number; $i++) {
            Transportation::create([
                'transporter_id' => $transporter->id,
                'totalCapacity' => $request->totalCapacity,
                'cost' => $request->cost,
                'type' => $request->type,
            ]);
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
