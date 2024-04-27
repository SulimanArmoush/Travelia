<?php

namespace App\Models\TheWorld\Facilities\Restaurants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = ['restaurant_id','chairNum','cost','type','status'] ;

    public function restaurantReservations()
    {
        return $this->hasMany(RestaurantReservation::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

}
