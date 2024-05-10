<?php

namespace App\Models\TheWorld\Facilities\Organizers;

use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripReservation extends Model
{
    use HasFactory;
    protected $fillable = [
    'user_id',
    'trip_id',
    'placeNum',
    'dateTime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
