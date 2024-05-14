<?php

namespace App\Http\Controllers;


use App\Models\TheWorld\Facilities\Requirement;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Permissions\User;

class AdminController extends Controller
{
    public function getRequierments()
    {
        $requierments = Requirement::all();
        if ($requierments->isEmpty()) {
            return response()->json(['message' => "Requirements Not Found"], 404);
        }
        return response()->json(['requierments' => $requierments], 200);
    }

    public function getRequierment($requierment_id)
    {
        try {
            $requierment_info = Requirement::with('user.facility')->findOrFail($requierment_id);

            if (isset($requierment_info->user->facility->imgs) && is_string($requierment_info->user->facility->imgs)) {
                $requierment_info->user->facility->imgs = json_decode($requierment_info->user->facility->imgs);
            }

            return response()->json($requierment_info, 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Requirement Not Found"], 404);
        }
    }


    public function handlingRequierment(Request $request, $requierment_id)
    {
        $validator = validator::make($request->all(), [
            'status' => ['required', 'in:accept,reject'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        try {
            $requirement = Requirement::findOrFail($requierment_id);
            $user = User::findOrFail($requirement->user_id);

            $requirement->update([
                'status' => $request->status,
            ]);

            if ($request->status == 'accept') {
                $user->update(['confirmation' => '1']);
                return response()->json(['message' => "You have accepted this account"], 200);
            }

            if ($request->status == 'reject') {
                $user->delete();
                return response()->json(['message' => "You have deleted this account"], 200);
            }

            return response()->json(['error' => "something is wrong"], 400);
        }

        catch (ModelNotFoundException $e) {
            return response()->json(['message' => "Requirement or User Not Found"], 404);
        }
    }
}
