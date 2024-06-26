<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Restaurants\Table;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class RestaurantController extends Controller
{
    public function createTables(Request $request)
    {
        $validator = validator::make($request->all(), [
            'num1' => ['integer'],
            'cost1' => ['numeric'],
            'num2' => ['integer'],
            'cost2' => ['numeric'],
            'num3' => ['integer'],
            'cost3' => ['numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        try {
            $restaurant = auth()->user()->facility->restaurant;

            if ($request->num1) {
                for ($i = 0; $i < $request->num1; $i++) {
                    Table::create([
                        'restaurant_id' => $restaurant->id,
                        'cost' => $request->cost1,
                        'type' => 'table with two chairs',
                    ]);
                }
            }
            if ($request->num2) {
                for ($i = 0; $i < $request->num2; $i++) {
                    Table::create([
                        'restaurant_id' => $restaurant->id,
                        'cost' => $request->cost2,
                        'type' => 'table with four chairs',
                    ]);
                }
            }
            if ($request->num3) {
                for ($i = 0; $i < $request->num3; $i++) {
                    Table::create([
                        'restaurant_id' => $restaurant->id,
                        'cost' => $request->cost3,
                        'type' => 'table with more than 4 chairs',
                    ]);
                }
            }
            return response()->json(['message' => 'Your Tables created successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not Found'], 404);
        }
    }

    public function getTable($table_id)
    {
        try {
            $table = Table::findOrFail($table_id);
            return response()->json($table, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Table not Found'], 404);
        }
    }

    public function getTables($restaurant_id)
    {
        try {
            $restaurant = Restaurant::findOrFail($restaurant_id);
            $tables = Table::Where('restaurant_id', $restaurant->id)
                ->paginate(10);

            if ($tables->isEmpty()) {
                return response()->json(['error' => 'Tables not Found'], 200);
            }
            return response()->json(['tables' => $tables], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not Found'], 404);
        }
    }

    public function getAvailableTables($restaurant_id)
    {
        try {
            $restaurant = Restaurant::findOrFail($restaurant_id);
            $tables = Table::Where('restaurant_id', $restaurant->id)
                ->where('status', 'available')
                ->paginate(10);

            if ($tables->isEmpty()) {
                return response()->json(['error' => 'Tables not Found'], 200);
            }
            return response()->json(['tables' => $tables], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not Found'], 404);
        }
    }
}
