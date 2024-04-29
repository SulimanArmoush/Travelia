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
        $validator = validator::make($request->all(), [
            'imgs'=>['min:1','max:3'],
            'imgs.*' => ['image','mimes:jpeg,png,jpg,gif','max:512'], 
        ]);
        if ($validator->fails()) {return response()->json($validator->errors()->all(), status: 400);}

        $images = $this->upload($request->imgs);
        
        Facility::find(auth()->user()->facility()->first()->id)->update([
            'imgs'=> $images,
        ]);
        $the_images = json_decode($images);
        return response()->json(['imgs'=> $the_images,'message' => 'Your images Added successfully'], 200);
    }
}
