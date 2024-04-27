<?php

namespace App\Models\TheWorld\Facilities\Hotels;

use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dates\Date;

class HotelReservation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','room_id','date_id'] ;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function date()
    {
        return $this->hasOne(Date::class);
    }
}
