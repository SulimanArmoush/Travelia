<?php

namespace App\Http\Controllers;


use App\Models\Contact;
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
            return response()->json(['error' => "Requirements Not Found"], 200);
        }
        return response()->json(['requierments' => $requierments], 200);
    }

    public function getRequierment($requierment_id)
    {
        try {
            $requierment_info = Requirement::with('user')->findOrFail($requierment_id);

            return response()->json($requierment_info, 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => "Requirement Not Found"], 404);
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
            return response()->json(['error' => "Requirement or User Not Found"], 404);
        }
    }

    public function getContact()
    {
        $contacts = Contact::all();
        if ($contacts->isEmpty()) {
            return response()->json(['error' => 'No massage Found'], 200);
        }
        $format = [];
        foreach ($contacts as $contact) {
            $format[] = [
                'id' => $contact->id,
                'from' => $contact->user->email,
                'name' => $contact->user->firstName .' '. $contact->user->lastName,
                'title' => $contact->title,
                'msg' => $contact->msg,
            ];
        }
        return response()->json(['Massages' => $format], 200);
    }
}
