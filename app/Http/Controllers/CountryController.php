<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Area;
use App\Models\TheWorld\City;
use App\Models\TheWorld\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\PhotoTrait;

class CountryController extends Controller
{
    use PhotoTrait;

    public function createCountry(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'imgs' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        if ($request->hasFile('imgs')) {
            $image = $this->saveImage($request->imgs);
            Country::create([
                'name' => $request->name,
                'imgs' => $image,
            ]);
            return response()->json(['image' => $image, 'message' => 'Your Country created successfully'], 200);
        }
        Country::create([
            'name' => $request->name,
        ]);
        return response()->json(['message' => 'Your Country created successfully'], 200);
    }
    public function createCity(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'imgs' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
            'country_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        if ($request->hasFile('imgs')) {
            $image = $this->saveImage($request->imgs);
            City::create([
                'name' => $request->name,
                'imgs' => $image,
                'country_id' => $request->country_id,
            ]);
            return response()->json(['image' => $image, 'message' => 'Your City created successfully'], 200);
        }
        City::create([
            'name' => $request->name,
            'country_id' => $request->country_id,
        ]);
        return response()->json(['message' => 'Your City created successfully'], 200);
    }

    public function createArea(Request $request)
    {
        $validator = validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'imgs' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:512'],
            'city_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        if ($request->hasFile('imgs')) {
            $image = $this->saveImage($request->imgs);
            Area::create([
                'name' => $request->name,
                'imgs' => $image,
                'city_id' => $request->city_id,
            ]);
            return response()->json(['image' => $image, 'message' => 'Your Area created successfully'], 200);
        }
        Area::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
        ]);
        return response()->json(['message' => 'Your Area created successfully'], 200);
    }
}
