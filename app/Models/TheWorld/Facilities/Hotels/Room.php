<?php

namespace App\Models\TheWorld\Facilities\Hotels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $fillable = ['hotel_id','cost','type','status'] ;


    public function hotelReservations()
    {
        return $this->hasMany(HotelReservation::class);
    }
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

}
