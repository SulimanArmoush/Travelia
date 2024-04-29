<?php

namespace App\Http\Controllers;


use App\Models\TheWorld\Facilities\Requirement;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\facilityCreateTrait;
use App\Traits\PhotoTrait;
use Illuminate\Support\Facades\Auth;

class TransporterController extends Controller
{

    use facilityCreateTrait,PhotoTrait;
    public function createTransporterAccount(Request $request)
    {
        if(auth()->user()->role_id != 5) 
        {
            return response()->json(['message' => 'you are not a Transport_Manager'], 400);
        }
        
        $validator = validator::make($request->all(), [
            'name'=>['required','string', 'max:25'],
            'description' =>['required','string', 'max:255'],
            'latitude'=>['required','string'],
            'longitude'=>['required','string'],
            'area_id' =>['required','integer'],
            'type' =>['required','string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        $location = $this->createLocation($request->latitude, $request->longitude, $request->area_id);
        $facility = $this->createFacility($request->name,$request->description, $location, Auth::id());
        
        Transporter::create([
            'facility_id'=>$facility,
             'type'=>$request->type,
        ]);

        Requirement::create([
            'user_id'=> Auth::id(),
            'facility_id'=>$facility,
        ]);

        return response()->json(['message' => 'Your Account created successfully'], 200);
    }
}
