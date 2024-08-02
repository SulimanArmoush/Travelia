<?php

namespace App\Http\Controllers;


use App\Models\Contact;
use App\Models\TheWorld\Facilities\Requirement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Permissions\User;

class AdminController extends Controller
{
    public function getRequierments(): JsonResponse
    {
        $requierments = Requirement::all();
        if ($requierments->isEmpty()) {
            return response()->json(['error' => "Requirements Not Found"]);
        }

        $data = [];
        foreach ($requierments as $requierment) {
            if ($requierment->user->role_id == 6) {
                continue;
            }
            if ($requierment->status != 'onHold') {
                continue;
            }
            $data[] = $requierment;
        }
        if (empty($data)) {
            return response()->json(['error' => "Requirements Not Found"]);
        }

        return response()->json(['requierments' => $data]);
    }

    public function getRequierment($requierment_id): JsonResponse
    {

        $requierment_info = Requirement::with('user')->find($requierment_id);
        if (!$requierment_info) {
            return response()->json(['error' => 'Requierment not Found']);
        }
        return response()->json($requierment_info);

    }


    public function handlingRequierment(Request $request, $requierment_id): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'status' => ['required', 'in:accept,reject'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $requirement = Requirement::find($requierment_id);
        if (!$requirement) {
            return response()->json(['error' => 'Requirement not Found']);
        }
        $user = User::find($requirement->user_id);
        if (!$user) {
            return response()->json(['error' => 'User not Found']);
        }
        $requirement->update([
            'status' => $request->status,
        ]);

        if ($request->status == 'accept') {
            $user->update(['confirmation' => '1']);
            return response()->json(['message' => "You have accepted this account"]);
        }

        $user->update(['confirmation' => '3']);
        return response()->json(['message' => "You have blocked this account"]);
    }

    public function getContact(): JsonResponse
    {
        $contacts = Contact::all();
        if ($contacts->isEmpty()) {
            return response()->json(['error' => 'No massage Found']);
        }
        $format = [];
        foreach ($contacts as $contact) {
            $format[] = [
                'id' => $contact->id,
                'from' => $contact->user->email,
                'name' => $contact->user->firstName . ' ' . $contact->user->lastName,
                'title' => $contact->title,
                'msg' => $contact->msg,
                'Date' => $contact->created_at,
            ];
        }
        return response()->json(['Massages' => $format]);
    }

    public function getTransferRequests(): JsonResponse
    {
        $requests = Requirement::all();
        if ($requests->isEmpty()) {
            return response()->json(['error' => "Requests Not Found"]);
        }

        $data = [];
        foreach ($requests as $request) {
            if ($request->user->role_id != 6) {
                continue;
            }
            if ($request->status != 'onHold') {
                continue;
            }
            $data[] = $request;
        }
        if (empty($data)) {
            return response()->json(['error' => "Requests Not Found"]);
        }

        return response()->json(['requierments' => $data]);
    }


    public function handlingTransferRequests(Request $request, $requierment_id): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'status' => ['required', 'in:accept,reject']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(),400);
        }

        $requierment = Requirement::find($requierment_id);
        if (!$requierment) {
            return response()->json(['error' => 'Requirement not Found']);
        }
        $user = User::find($requierment->user_id);
        if (!$user) {
            return response()->json(['error' => 'User not Found']);
        }
        $requierment->update([
            'status' => $request->status,
        ]);

        if ($request->status == 'accept') {
            $user->increment('wallet', $requierment->amount);
            return response()->json(['message' => "You have accepted this request"]);
        }

        return response()->json(['message' => "You have reject this request"]);
    }
}
