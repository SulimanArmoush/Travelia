<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Hotels\Room;
use App\Models\TheWorld\Facilities\Organizers\Trip;
use App\Models\TheWorld\Facilities\Organizers\TripReservation;
use App\Models\TheWorld\Facilities\Reservation;
use App\Models\TheWorld\Facilities\RestaurantReservation;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Restaurants\Table;
use App\Models\TheWorld\Facilities\Transporters\Routing;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $this->send($user->deviceToken,'Successful reservation','The reservation was successful and ' . $cost . ' was withdrawn from your account');

        return response()->json([
            'message' => "Your reservation has been completed successfully",
            'remaining time' => Carbon::parse($trip->strDate)->diffForHumans(now()),
        ]);
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
                'cost' => $reservation->cost
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

        $this->send($user->deviceToken,'Successful reservation','The reservation was successful and ' . $cost . ' was withdrawn from your account');


        return response()->json([
            'message' => "Your reservation has been completed successfully",
            'remaining time' => $startDate->diffForHumans(now()),
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

        $this->send($reservation->user->deviceToken,'Successful reservation','The reservation was successful and ' . $cost . ' was withdrawn from your account');

        return response()->json([
            'message' => "Your Table has been reserved successfully",
            'remaining time' => $startDateTime->diffForHumans(now()),
        ]);
    }


    public function getUserTripBooking(): JsonResponse
    {
        $user = Auth::user();
        $formatted = collect();
        foreach ($user->tripReservations as $reservation) {
            $formatted->push([
                'trip_id'=>$reservation->trip->id,
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
            $reservationDetails = [
                'strDate' => $reservation->strDate,
                'endDate' => $reservation->endDate,
                'daysNumber' => Carbon::parse($reservation->strDate)->diffInDays(Carbon::parse($reservation->endDate))+1,
                'placeNum' => $reservation->placeNum,
                'cost' => $reservation->cost,
                'address' => $reservation->room->hotel->facility->location->address ,

                'hotel' => $reservation->room->hotel->facility->name,
                'hotelImg' => $reservation->room->hotel->facility->img,
                'room' => $reservation->room->type,
                'roomCost' => $reservation->room->cost,

                'transporter' => $reservation->routing->transportation->transporter->facility->name,
                'transporterImg' => $reservation->routing->transportation->transporter->facility->img,
                'transportation' => $reservation->routing->transportation->type,
                'strLocation' => $reservation->routing->startLocation->address,
                'endLocation' => $reservation->routing->endedLocation->address,
                'routCost' => $reservation->routing->cost,
                'routCapacity' => $reservation->routing->capacity . '/' .$reservation->routing->transportation->totalCapacity ,

                'restaurants' => collect()
            ];

            foreach ($reservation->restaurantReservations as $restaurantReservation) {
                $reservationDetails['restaurants']->push([
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
