<?php

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Restaurants\Table;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class RestaurantController extends Controller
{
    public function createTables(Request $request)
    {
        $restaurant = auth()->user()->facility->restaurant;

        $validator = validator::make($request->all(), [
            'chairNum' => ['required', 'integer'],
            'cost' => ['required', 'numeric'],
            'type' => ['required', 'integer'],
            'number' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        for ($i = 0; $i < $request->number; $i++) {
            Table::create([
                'restaurant_id' => $restaurant->id,
                'chairNum' => $request->totalCapacity,
                'cost' => $request->cost,
                'type' => $request->type,
            ]);
        }
        return response()->json(['message' => 'Your Tables created successfully'], 200);
    }

    public function getTable($Table_id)
    {
        $table = Table::find($Table_id);
        return response()->json(['table' => $table], 200);
    }

    public function getTables($restaurant_id)
    {
        $tables = Table::Where('restaurant_id', $restaurant_id)
            ->paginate(10);
        return response()->json(['tables' => $tables], 200);
    }

    public function getAvailableTables($restaurant_id)
    {

        $tables = Table::Where('restaurant_id', $restaurant_id)
            ->where('status', 'available')
            ->paginate(10);
        return response()->json(['tables' => $tables], 200);

    }

}
