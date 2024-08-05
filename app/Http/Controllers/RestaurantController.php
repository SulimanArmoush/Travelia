<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers;

use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Restaurants\Table;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


class RestaurantController extends Controller
{
    public function createTables(Request $request): JsonResponse
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
        return response()->json(['message' => 'Your Tables created successfully']);
    }

    public function getTable($table_id): JsonResponse
    {
        $table = Table::find($table_id);
        if (!$table) {
            return response()->json(['error' => 'Table not Found']);
        }
        return response()->json($table);
    }

    public function getTables($restaurant_id): JsonResponse
    {
        $restaurant = Restaurant::find($restaurant_id);
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not Found']);
        }

        if ($restaurant->tables->isEmpty()) {
            return response()->json(['error' => 'Tables not Found']);
        }
        return response()->json(['tables' => $restaurant->tables]);
    }

    public function getAvailableTables(Request $request, $restaurant_id): JsonResponse
    {
        $restaurant = Restaurant::find($restaurant_id);
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found']);
        }

        $tableWithTwoChairs = [];
        $tableWithFourChairs = [];
        $tableWithMoreThan4Chairs = [];

        foreach ($restaurant->tables as $table) {
            if ($table->type == 'table with two chairs') {
                $tableWithTwoChairs [] = $table;
            }
            if ($table->type == 'table with four chairs') {
                $tableWithFourChairs [] = $table;
            }
            if ($table->type == 'table with more than 4 chairs') {
                $tableWithMoreThan4Chairs [] = $table;
            }
        }

        $validator = Validator::make($request->all(), [
            'DateTime' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $startDateTime = Carbon::parse($request->DateTime);
        $endDateTime = $startDateTime->copy()->addHours(2);

        $availableTables = $restaurant->tables()
            ->whereDoesntHave('restaurantReservations', function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('DateTime', [$startDateTime, $endDateTime])
                        ->orWhereRaw('? BETWEEN DateTime AND DATE_ADD(DateTime, INTERVAL 2 HOUR)', [$startDateTime])
                        ->orWhereRaw('? BETWEEN DateTime AND DATE_ADD(DateTime, INTERVAL 2 HOUR)', [$endDateTime]);
                });
            })->get();

        if ($availableTables->isEmpty()) {
            return response()->json(['error' => 'Tables not Found']);
        }

        $availableTableWithTwoChairs = [];
        $availableTableWithFourChairs = [];
        $availableTableWithMoreThan4Chairs = [];

        foreach ($availableTables as $availableTable) {
            if ($availableTable->type == 'table with two chairs') {
                $availableTableWithTwoChairs [] = $availableTable;
            }
            if ($availableTable->type == 'table with four chairs') {
                $availableTableWithFourChairs [] = $availableTable;
            }
            if ($availableTable->type == 'table with more than 4 chairs') {
                $availableTableWithMoreThan4Chairs [] = $availableTable;
            }
        }

        return response()->json([
            'tableWithTwoChairs' => count($tableWithTwoChairs),
            'availableTableWithTwoChairs' => count($availableTableWithTwoChairs),
            'tableWithFourChairs' => count($tableWithFourChairs),
            'availableTableWithFourChairs' => count($availableTableWithFourChairs),
            'tableWithMoreThan4Chairs' => count($tableWithMoreThan4Chairs),
            'availableTableWithMoreThan4Chairs' => count($availableTableWithMoreThan4Chairs)
        ]);
    }

    public function getRestaurantReservation(): JsonResponse
    {
        $list = Auth::user()->facility->restaurant->restaurantReservations;
        if ($list->isEmpty()) {
            return response()->json(['error' => 'Reservation Not Found']);
        };

        $formattedList = collect();
        foreach ($list as $item) {
            if (Carbon::parse($item->DateTime)->isPast()) {
                continue;
            }
            $formattedList->push([
                'id' => $item->id,
                'table' => $item->table->type,
                'cost' => intval($item->cost),
                'date' => Carbon::parse($item->DateTime)->format('Y-m-d'),
                'hour' => Carbon::parse($item->DateTime)->format('h:i A'),
                'name' => $item->reservation->user->firstName . ' ' . $item->reservation->user->lastName,
                'email' => $item->reservation->user->email,
                'phone' => $item->reservation->user->phone,
                'age' => $item->reservation->user->age,
                'address' => $item->reservation->user->address,
                'photo' => $item->reservation->user->photo,
                'DateTime' => Carbon::parse($item->DateTime)->format('Y-m-d H:i:s'),

            ]);
        }
        $sortedList = $formattedList->sortBy('DateTime')->values();

        $finalList = $sortedList->map(function ($item) {
            return collect($item)->except('DateTime')->all();
        });

        return response()->json(['reservations' => $finalList]);
    }

}
