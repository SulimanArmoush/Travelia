<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\Facilities\Location;
use App\Models\TheWorld\Facilities\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Permissions\User;

class AdminController extends Controller
{
    public function getRequiermemts()
    {
        $requierments = Requirement::Get();
        if ($requierments->isEmpty()) {
            return response()->json(['message' => "No Requirements"], 404);
        }
        return response()->json($requierments, 200);
    }

    public function getRequiermemt($requiermemt_id)
    {
        $requiermemt_info = Requirement::with('user.facility.location')->find($requiermemt_id);
        if (!$requiermemt_info) {
            return response()->json(['Requirement Not Found'], 404);
        }

        return response()->json($requiermemt_info, 200);
    }

    public function handlingRequierment(Request $request, $requiermemt_id)
    {
        $validator = validator::make($request->all(), [
            'status' => ['required', 'in:accept,reject'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $requirement = Requirement::find($requiermemt_id);
        $requirement->update([
            'status' => $request->status,
        ]);

        if ($request->status == 'accept') {
            Facility::find($requirement->facility_id)->update([
                'confirmation' => '1'
            ]);
            return response()->json(['message' => "You have accepted this account"], 200);
        }

        if ($request->status == 'reject') {
            $facility = Facility::find($requirement->facility_id);
            Location::find($facility->location_id)->delete();
            User::find($requirement->user_id)->delete();

            return response()->json(['message' => "You have deleted this account"], 200);
        }
    }
}
