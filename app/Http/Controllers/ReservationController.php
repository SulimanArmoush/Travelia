<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\Not;
use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Hotels\Room;
use App\Models\TheWorld\Facilities\Organizers\Trip;
use App\Models\TheWorld\Facilities\Organizers\TripReservation;
use App\Models\TheWorld\Facilities\Reservation;
use App\Models\TheWorld\Facilities\RestaurantReservation;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Restaurants\Table;
use App\Models\TheWorld\Facilities\Transporters\Routing;
use App\Models\TheWorld\TouristArea;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\NotificationTrait;

class ReservationController extends Controller
{
    use NotificationTrait;

    public function tripBooking(Request $request, $trip_id): JsonResponse
    {
        $user = Auth::user();
        $trip = Trip::find($trip_id);
        if (!$trip) {
            return response()->json(['error' => 'Trip not found']);
        }

        $validator = validator::make($request->all(), [
            'placeNum' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $cost = $request->placeNum * $trip->cost;

        if ($cost > $user->wallet) {
            return response()->json(['error' => 'you dont have money']);
        }
        if (($trip->totalCapacity - $trip->capacity) < $request->placeNum) {
            return response()->json(['error' => 'Not enough places']);
        }
        if ($trip->strDate < now()) {
            return response()->json(['error' => 'Missed trip']);
        }

        TripReservation::create([
            'user_id' => $user->id,
            'trip_id' => $trip->id,
            'placeNum' => $request->placeNum,
            'cost' => $cost,
        ]);

        $before = $trip->organizer->facility->user->wallet;
        $trip->organizer->facility->user->increment('wallet', $cost);
        $after = $trip->organizer->facility->user->wallet;
        $trip->increment('capacity', $request->placeNum);
        $user->decrement('wallet', $cost);

        Finance::create([
            'from' => $user->id,
            'to' => $trip->organizer->facility->user->id,
            'before' => $before,
            'after' => $after,
            'Intake' => $cost,
            'Description' => 'for booking ' . $request->placeNum . ' person on a trip',
        ]);

        $this->send($user, 'Successful reservation', 'The reservation was successful and ' . $cost . ' was withdrawn from your account');

        return response()->json([
            'message' => "Your reservation has been completed successfully",
            'remaining time' => Carbon::parse($trip->strDate)->diffForHumans(now()),
        ]);
    }

    public function deleteTripReservation($reservation_id): JsonResponse
    {
        $reservation = TripReservation::find($reservation_id);
        if (!$reservation) {
            return response()->json(['error' => 'Reservation No Found']);
        }

        if ($reservation->user->id != Auth::id()) {
            return response()->json(['error' => 'This reservation is not belongs to you']);
        }

        if (Carbon::now()->greaterThan(Carbon::parse($reservation->trip->strDate)->subDay())) {
            return response()->json(['error' => 'you cant delete this reservation after now']);
        }

        $before = $reservation->trip->organizer->facility->user->wallet;
        $reservation->trip->organizer->facility->user->decrement('wallet', $reservation->cost);
        $after = $reservation->trip->organizer->facility->user->wallet;
        $reservation->trip->decrement('capacity', $reservation->placeNum);
        $reservation->user->increment('wallet', $reservation->cost);

        Finance::create([
            'from' => $reservation->user->id,
            'to' => $reservation->trip->organizer->facility->user->id,
            'before' => $before,
            'after' => $after,
            'Intake' => $reservation->cost,
            'Description' => 'for cancel a reservation to' . $reservation->trip->area->name . ' with' . $reservation->trip->organizer->facility->name,
        ]);

        $this->send($reservation->user, 'Canceled reservation', 'your reservation has been canceled successfully');

        $reservation->delete();
        return response()->json(['message' => 'reservation deleted successfully']);
    }

    public function getReservation($trip_id): JsonResponse
    {
        $reservations = TripReservation::where('trip_id', $trip_id)->get();
        if ($reservations->isEmpty()) {
            return response()->json(['error' => 'Reservations not Found']);
        }
        $format = [];
        foreach ($reservations as $reservation) {
            $format[] = [
                'id' => $reservation->id,
                'name' => $reservation->user->firstName . ' ' . $reservation->user->lastName,
                'email' => $reservation->user->email,
                'phone' => $reservation->user->phone,
                'age' => $reservation->user->age,
                'address' => $reservation->user->address,
                'photo' => $reservation->user->photo,
                'placeNum' => $reservation->placeNum,
                'cost' => intval($reservation->cost)
            ];
        }
        return response()->json([
            'Reservations' => $format,
        ]);
    }

    public function booking(Request $request): JsonResponse
    {
        $user = Auth::user();
        $validator = validator::make($request->all(), [
            'area_id' => ['required', 'integer'],
            'placeNum' => ['required', 'integer'],
            'strDate' => ['required', 'date'],
            'endDate' => ['required', 'date'],
            'hotel_id' => ['required', 'integer'],
            'room_type' => ['required', 'string'],
            'routing_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }

        $area = TouristArea::find($request->area_id);
        if (!$area) {
            return response()->json(['error' => 'Area not found']);
        }

        $hotel = Hotel::find($request->hotel_id);
        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found']);
        }

        $startDate = Carbon::parse($request->strDate);
        $endDate = Carbon::parse($request->endDate);

        $availableRooms = $hotel->rooms()
            ->where('type', $request->room_type) // تصفية الغرف بناءً على النوع المحدد
            ->whereDoesntHave('reservations', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('strDate', [$startDate, $endDate])
                    ->orWhereBetween('endDate', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('strDate', '<=', $startDate)
                            ->where('endDate', '>=', $endDate);
                    });
            })
            ->get();

        if ($availableRooms->isEmpty()) {
            return response()->json(['error' => 'No available rooms of the specified type found for the given date range.']);
        }

        $room = $availableRooms->first();


        $rout = Routing::find($request->routing_id);
        if (!$rout) {
            return response()->json(['error' => 'Rout not found']);
        }

        if (($rout->transportation->totalCapacity - $rout->capacity) < $request->placeNum) {
            return response()->json(['error' => 'Not enough places']);
        }

        $routDate = Carbon::parse($rout->dateTime);
        if (!$routDate->isSameDay($startDate) &&
            !$routDate->isSameDay($startDate->copy()->subDay())) {
            return response()->json(['error' => 'Routing date must be the same as the first day of the booking or the day before'], 400);
        }

        $daysNum = $startDate->diffInDays($endDate) + 1;

        $roomCost = $room->cost * $daysNum;
        $routCost = $request->placeNum * $rout->cost;
        $cost = $routCost + $roomCost;
        if ($cost > $user->wallet) {
            return response()->json(['error' => 'you dont have money']);
        }

        Reservation::create([
            'area_id' => $area->id,
            'user_id' => $user->id,
            'placeNum' => $request->placeNum,
            'strDate' => $request->strDate,
            'endDate' => $request->endDate,
            'room_id' => $room->id,
            'routing_id' => $rout->id,
            'cost' => $cost,
        ]);

        $user->decrement('wallet', $cost);
        $hotelBefore = $room->hotel->facility->user->wallet;
        $transporterBefore = $rout->transportation->transporter->facility->user->wallet;
        $room->hotel->facility->user->increment('wallet', $roomCost);
        $rout->transportation->transporter->facility->user->increment('wallet', $routCost);
        $hotelAfter = $room->hotel->facility->user->wallet;
        $transporterAfter = $rout->transportation->transporter->facility->user->wallet;

        $rout->increment('capacity', $request->placeNum);

        Finance::create([
            'from' => $user->id,
            'to' => $room->hotel->facility->user->id,
            'before' => $hotelBefore,
            'after' => $hotelAfter,
            'Intake' => $roomCost,
            'Description' => 'for booking ' . $room->type,
        ]);
        Finance::create([
            'from' => $user->id,
            'to' => $rout->transportation->transporter->facility->user->id,
            'before' => $transporterBefore,
            'after' => $transporterAfter,
            'Intake' => $routCost,
            'Description' => 'for booking for ' . $request->placeNum . ' person on a route',
        ]);

        $this->send($user, 'Successful reservation', 'The reservation ' . $area->name . ' was successful and ' . $cost . ' was withdrawn from your account');

        return response()->json([
            'message' => "Your reservation has been completed successfully",
            'remaining time' => $startDate->diffForHumans(now()),
        ]);
    }

    public function getCostForBook(Request $request): JsonResponse
    {
        $validator = validator::make($request->all(), [
            'placeNum' => ['required', 'integer'],
            'strDate' => ['required', 'date'],
            'endDate' => ['required', 'date'],
            'hotel_id' => ['required', 'integer'],
            'room_type' => ['required', 'string'],
            'routing_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), status: 400);
        }
        $startDate = Carbon::parse($request->strDate);
        $endDate = Carbon::parse($request->endDate);

        $hotel = Hotel::find($request->hotel_id);
        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found']);
        }
        $rooms = $hotel->rooms()->where('type', $request->room_type)->get();
        if ($rooms->isEmpty()) {
            return response()->json(['error' => 'No available rooms of the specified type found.']);
        }
        $room = $rooms->first();

        $rout = Routing::find($request->routing_id);
        if (!$rout) {
            return response()->json(['error' => 'Rout not found']);
        }

        $daysNum = $startDate->diffInDays($endDate) + 1;

        $roomCost = $room->cost * $daysNum;
        $routCost = $request->placeNum * $rout->cost;
        $cost = $routCost + $roomCost;

        return response()->json([
            'totalCost' => intval($cost),
        ]);
    }

    public function restaurantBooking(Request $request, $reservation_id): JsonResponse
    {
        $reservation = Reservation::find($reservation_id);
        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found']);
        }

        $validator = Validator::make($request->all(), [
            'restaurant_id' => ['required', 'integer'],
            'table_type' => ['required', 'string'],
            'DateTime' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $restaurant = Restaurant::find($request->restaurant_id);
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found']);
        }

        $startDateTime = Carbon::parse($request->DateTime);
        $endDateTime = $startDateTime->copy()->addHours(2);
        $mainStartDateTime = Carbon::parse($reservation->strDate);
        $mainEndDateTime = Carbon::parse($reservation->endDate);


        $availableTables = $restaurant->tables()
            ->where('type', $request->table_type)
            ->whereDoesntHave('restaurantReservations', function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('DateTime', [$startDateTime, $endDateTime])
                    ->orWhereRaw('? BETWEEN DateTime AND DATE_ADD(DateTime, INTERVAL 2 HOUR)', [$startDateTime])
                    ->orWhereRaw('? BETWEEN DateTime AND DATE_ADD(DateTime, INTERVAL 2 HOUR)', [$endDateTime])
                    ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                        $query->where('DateTime', '<=', $startDateTime)
                            ->whereRaw('DATE_ADD(DateTime, INTERVAL 2 HOUR) >= ?', [$endDateTime]);
                    });
            })
            ->get();


        if ($availableTables->isEmpty()) {
            return response()->json(['error' => 'No available tables of the specified type for the selected time']);
        }

        $table = $availableTables->first();

        if ($startDateTime->isBefore($mainStartDateTime) || $endDateTime->isAfter($mainEndDateTime)) {
            return response()->json(['error' => 'Table booking time must be within the main reservation period']);
        }

        $cost = $table->cost;
        if ($cost > $reservation->user->wallet) {
            return response()->json(['error' => 'you dont have money']);
        }

        RestaurantReservation::create([
            'reservation_id' => $reservation->id,
            'table_id' => $table->id,
            'DateTime' => $request->DateTime,
            'cost' => $cost
        ]);

        $reservation->user->decrement('wallet', $cost);
        $before = $table->restaurant->facility->user->wallet;
        $table->restaurant->facility->user->increment('wallet', $cost);
        $after = $table->restaurant->facility->user->wallet;

        Finance::create([
            'from' => $reservation->user->id,
            'to' => $table->restaurant->facility->user->id,
            'before' => $before,
            'after' => $after,
            'Intake' => $cost,
            'Description' => 'for booking ' . $table->type,
        ]);

        $this->send($reservation->user, 'Successful reservation', 'The reservation was successful and ' . $cost . ' was withdrawn from your account');

        return response()->json([
            'message' => "Your Table has been reserved successfully",
            'remaining time' => $startDateTime->diffForHumans(now()),
        ]);
    }

    public function deleteRestaurantReservation($reservation_id): JsonResponse
    {
        $reservation = RestaurantReservation::find($reservation_id);
        if (!$reservation) {
            return response()->json(['error' => 'Reservation No Found']);
        }

        if ($reservation->reservation->user->id != Auth::id()) {
            return response()->json(['error' => 'This reservation is not belongs to you']);
        }

        if (Carbon::now()->greaterThan(Carbon::parse($reservation->strDate))) {
            return response()->json(['error' => 'you cant delete this reservation after now']);
        }

        $cost = $reservation->table->cost;

        $reservation->reservation->user->increment('wallet', $cost);
        $before = $reservation->table->restaurant->facility->user->wallet;
        $reservation->table->restaurant->facility->user->decrement('wallet', $cost);
        $after = $reservation->table->restaurant->facility->user->wallet;

        Finance::create([
            'from' => $reservation->reservation->user->id,
            'to' => $reservation->table->restaurant->facility->user->id,
            'before' => $before,
            'after' => $after,
            'Intake' => $cost,
            'Description' => 'for booking ' . $reservation->table->type,
        ]);

        $this->send($reservation->reservation->user, 'Canceled reservation', 'your reservation has been canceled successfully');

        $reservation->delete();
        return response()->json(['message' => 'reservation deleted successfully']);
    }


    public function deleteReservation($reservation_id): JsonResponse
    {
        $reservation = Reservation::find($reservation_id);
        if (!$reservation) {
            return response()->json(['error' => 'Reservation Not Found']);
        }

        if ($reservation->user->id != Auth::id()) {
            return response()->json(['error' => 'This reservation does not belong to you']);
        }

        if (Carbon::now()->greaterThan(Carbon::parse($reservation->strDate)->subDay())) {
            $this->send($reservation->user, 'Cannot Canceled reservation', 'you cannot canceled this reservation after now');
            return response()->json(['error' => 'You cannot delete this reservation after now']);
        }


        $startDate = Carbon::parse($reservation->strDate);
        $endDate = Carbon::parse($reservation->endDate);

        $daysNum = $startDate->diffInDays($endDate) + 1;

        $roomCost = $reservation->room->cost * $daysNum;
        $routeCost = $reservation->placeNum * $reservation->routing->cost;
        $totalCost = $routeCost + $roomCost;

        $reservation->user->increment('wallet', $totalCost);
        $hotelBefore = $reservation->room->hotel->facility->user->wallet;
        $transporterBefore = $reservation->routing->transportation->transporter->facility->user->wallet;
        $reservation->room->hotel->facility->user->decrement('wallet', $roomCost);
        $reservation->routing->transportation->transporter->facility->user->decrement('wallet', $routeCost);
        $hotelAfter = $reservation->room->hotel->facility->user->wallet;
        $transporterAfter = $reservation->routing->transportation->transporter->facility->user->wallet;

        $reservation->routing->decrement('capacity', $reservation->placeNum);

        Finance::create([
            'from' => $reservation->user->id,
            'to' => $reservation->room->hotel->facility->user->id,
            'before' => $hotelBefore,
            'after' => $hotelAfter,
            'Intake' => $roomCost,
            'Description' => 'for cancel booking ' . $reservation->room->type,
        ]);

        Finance::create([
            'from' => $reservation->user->id,
            'to' => $reservation->routing->transportation->transporter->facility->user->id,
            'before' => $transporterBefore,
            'after' => $transporterAfter,
            'Intake' => $routeCost,
            'Description' => 'cancel a reservation on a route',
        ]);

        $this->send($reservation->user, 'Canceled reservation', 'Your reservation has been canceled successfully');

        // Handle restaurant reservations
        $restaurantReservations = $reservation->restaurantReservations;
        foreach ($restaurantReservations as $restaurantReservation) {

            if (Carbon::now()->greaterThan(Carbon::parse($restaurantReservation->strDate))) {
                return response()->json(['error' => 'You cannot delete this reservation after now']);
            }

            $restaurantCost = $restaurantReservation->table->cost;

            $restaurantReservation->reservation->user->increment('wallet', $restaurantCost);
            $before = $restaurantReservation->table->restaurant->facility->user->wallet;
            $restaurantReservation->table->restaurant->facility->user->decrement('wallet', $restaurantCost);
            $after = $restaurantReservation->table->restaurant->facility->user->wallet;

            Finance::create([
                'from' => $restaurantReservation->reservation->user->id,
                'to' => $restaurantReservation->table->restaurant->facility->user->id,
                'before' => $before,
                'after' => $after,
                'Intake' => $restaurantCost,
                'Description' => 'for booking ' . $restaurantReservation->table->type,
            ]);

            $this->send($restaurantReservation->reservation->user, 'Canceled restaurant reservation', 'Your restaurant reservation has been canceled successfully');

            $restaurantReservation->delete();
        }

        $reservation->delete();

        return response()->json(['message' => 'Reservation deleted successfully']);
    }

    public function getUserTripBooking(): JsonResponse
    {
        $user = Auth::user();
        $formatted = collect();
        foreach ($user->tripReservations as $reservation) {
            $formatted->push([
                'id' => $reservation->id,
                'trip_id' => $reservation->trip->id,
                'organizerName' => $reservation->trip->organizer->facility->name,
                'organizerImg' => $reservation->trip->organizer->facility->img,
                'organizerAddress' => $reservation->trip->organizer->facility->location->address,
                'tripImg' => $reservation->trip->img,
                'strDate' => $reservation->trip->strDate,
                'endDate' => $reservation->trip->endDate,
                'tripCost' => $reservation->trip->cost,
                'capacity' => $reservation->trip->capacity . '/' . $reservation->trip->totalCapacity,
                'reservationPlaceNum' => $reservation->placeNum,
                'reservationCost' => $reservation->cost,
            ]);
        }

        if ($formatted->isEmpty()) {
            return response()->json(['error' => 'Reservations Not Found']);

        }
        return response()->json(['Reservations' => $formatted]);
    }


    public function getUserBooking(): JsonResponse
    {
        $user = Auth::user();
        $formatted = collect();
        foreach ($user->reservations as $reservation) {
            $restaurantTotalCost = $reservation->restaurantReservations->sum(function ($restaurantReservation) {
                return $restaurantReservation->table->cost;
            });

            $totalCost = $reservation->cost + $restaurantTotalCost;

            $reservationDetails = [
                'id' => $reservation->id,
                'area_id' => $reservation->area->id,
                'area' => $reservation->area->name,
                'area_img' => $reservation->area->img,
                'address' => $reservation->area->location->address,

                'strDate' => $reservation->strDate,
                'endDate' => $reservation->endDate,
                'daysNumber' => Carbon::parse($reservation->strDate)->diffInDays(Carbon::parse($reservation->endDate)) + 1,
                'placeNum' => $reservation->placeNum,
                'cost' => intval($totalCost),

                'hotel' => $reservation->room->hotel->facility->name,
                'hotelImg' => $reservation->room->hotel->facility->img,
                'room' => $reservation->room->type,
                'roomCost' => intval($reservation->room->cost),

                'transporter' => $reservation->routing->transportation->transporter->facility->name,
                'transporterImg' => $reservation->routing->transportation->transporter->facility->img,
                'transportation' => $reservation->routing->transportation->type,
                'strLocation' => $reservation->routing->startLocation->address,
                'endLocation' => $reservation->routing->endedLocation->address,
                'routCost' => intval($reservation->routing->cost),
                'routCapacity' => $reservation->routing->capacity . '/' . $reservation->routing->transportation->totalCapacity,

                'restaurants' => collect()
            ];

            foreach ($reservation->restaurantReservations as $restaurantReservation) {
                $reservationDetails['restaurants']->push([
                    'id' => $restaurantReservation->id,
                    'restaurant' => $restaurantReservation->table->restaurant->facility->name,
                    'restaurantImg' => $restaurantReservation->table->restaurant->facility->img,
                    'table' => $restaurantReservation->table->type,
                    'tableCost' => $restaurantReservation->table->cost,
                    'restaurantDateTime' => $restaurantReservation->DateTime,
                ]);
            }

            $formatted->push($reservationDetails);
        }

        if ($formatted->isEmpty()) {
            return response()->json(['error' => 'Reservations Not Found']);

        }
        return response()->json(['Reservations' => $formatted]);
    }
}
