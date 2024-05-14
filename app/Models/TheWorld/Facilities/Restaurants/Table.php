<?php

namespace App\Models\TheWorld\Facilities\Restaurants;

use App\Models\TheWorld\Facilities\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id','cost','type','status'] ;
    protected $hidden = ['created_at', 'updated_at'];


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

}
