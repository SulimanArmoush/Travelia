<?php

namespace App\Models\TheWorld\Facilities\Restaurants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = ['restaurant_id','cost','type','status'] ;
    protected $hidden = ['created_at', 'updated_at'];

    public function restaurantReservations()
    {
        return $this->hasMany(RestaurantReservation::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

}
