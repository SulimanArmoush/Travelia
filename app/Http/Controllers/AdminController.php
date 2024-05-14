<?php

namespace App\Http\Controllers;


use App\Models\TheWorld\Facilities\Requirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Permissions\User;

class AdminController extends Controller
{
    public function getRequierments()
    {
        $requierments = Requirement::all();
        if (!$requierments) {
            return response()->json(['message' => "Requirements Not Found"], 404);
        }
        return response()->json(['requierments' =>$requierments], 200);
    }

    public function getRequierment($requierment_id)
    {
        if (!$requierment_id) {
            return response()->json(['message' => "Requirement Not Found"], 404);
        }

        $requierment_info = Requirement::with('user.facility')->find($requierment_id);
        if (!$requierment_info) {
            return response()->json(['Requirement Not Found'], 404);
        }

        if ($requierment_info->user->facility->imgs) {
            $requierment_info->user->facility->imgs = json_decode($requierment_info->user->facility->imgs);
        }

        return response()->json($requierment_info, 200);
    }

    public function handlingRequierment(Request $request, $requierment_id)
    {
        if (!$requierment_id) {
            return response()->json(['message' => "Requirement Not Found"], 404);
        }

        $validator = validator::make($request->all(), [
            'status' => ['required', 'in:accept,reject'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $requirement = Requirement::find($requierment_id);

        if (!$requirement) {
            return response()->json(['message' => "Requirement Not Found"], 404);
        }
        $user = User::find($requirement->user_id);

        if (!$user) {
            return response()->json(['message' => "User Not Found"], 404);
        }

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
}
