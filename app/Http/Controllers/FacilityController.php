<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Support\Facades\Validator;
use App\Traits\PhotoTrait;

class FacilityController extends Controller
{
    use PhotoTrait;
    public function imgUpload(Request $request)
    {
        
    if (!$request->hasFile('imgs')) {return response()->json(['error' => 'No images provided'], 400);}

        $validator = validator::make($request->all(), [
            'imgs'=>['min:1','max:3'],
            'imgs.*' => ['image','mimes:jpeg,png,jpg,gif','max:512'], 
        ]);
        if ($validator->fails()) {return response()->json($validator->errors()->all(), status: 400);}

        $facility = Facility::find(auth()->user()->facility()->first()->id);
        if (!$facility) {return response()->json(['error' => 'Facility not found'], 404);}


        $images = $this->upload($request->imgs);
        
        $facility->update([
            'imgs'=> $images,
        ]);
         
        return response()->json(['imgs'=> $images,'message' => 'Your images Added successfully'], 200);
    }
}
