<?php

namespace App\Models\TheWorld\Facilities\Organizers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dates\Date;


class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'cost',
        'dateTime',
        'totalCapacity',
        'strLocation',
        'imgs',
        'touristArea',
        'hotel',
        'restaurant',
        'transporter',
        'status',
    ];


    public function tripReservations()
    {
        return $this->hasMany(TripReservation::class);
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

}
