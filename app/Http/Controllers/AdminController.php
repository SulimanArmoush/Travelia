<?php

namespace App\Http\Controllers;


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
        $requiermemt_info = Requirement::with('user.facility')->find($requiermemt_id);
        if (!$requiermemt_info) {
            return response()->json(['Requirement Not Found'], 404);
        }

        if ($requiermemt_info->user->facility->imgs) {
            $requiermemt_info->user->facility->imgs = json_decode($requiermemt_info->user->facility->imgs);
        }
        // إزالة الطوابع الزمنية
        unset($requiermemt_info->created_at);
        unset($requiermemt_info->updated_at);
        unset($requiermemt_info->user->created_at);
        unset($requiermemt_info->user->updated_at);
        unset($requiermemt_info->user->facility->created_at);
        unset($requiermemt_info->user->facility->updated_at);
        unset($requiermemt_info->user->facility->location->created_at);
        unset($requiermemt_info->user->facility->location->updated_at);

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
        $user = User::find($requirement->user_id);
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
    }
}
