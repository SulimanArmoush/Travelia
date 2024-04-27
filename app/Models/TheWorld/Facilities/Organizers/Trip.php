<?php

namespace App\Models\TheWorld\Facilities\Organizers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dates\Date;


class Trip extends Model
{
    use HasFactory;
    protected $fillable = ['organizer_id','status','cost','imgs','totalCapacity','date_id'] ;


    public function tripReservations()
    {
        return $this->hasMany(TripReservation::class);
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function date()
    {
        return $this->hasOne(Date::class);
    }
}
