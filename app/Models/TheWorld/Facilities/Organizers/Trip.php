<?php

namespace App\Models\TheWorld\Facilities\Organizers;

use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Location;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use App\Models\TheWorld\TouristArea;
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
        'touristArea_id',
        'hotel_id',
        'restaurant_id',
        'transporter_id',
        'status',
    ];
    protected $hidden = ['created_at', 'updated_at'];


    public function tripReservations()
    {
        return $this->hasMany(TripReservation::class);
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'strLocation');
    }

    public function touristArea()
    {
        return $this->belongsTo(TouristArea::class,'touristArea_id');
    }
    public function hotel()
    {
        return $this->belongsTo(Hotel::class,'hotel_id');
    }
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id');
    }
    public function transporter()
    {
        return $this->belongsTo(Transporter::class,'transporter_id');
    }

}
