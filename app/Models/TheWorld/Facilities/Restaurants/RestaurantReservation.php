<?php

namespace App\Models\TheWorld\Facilities\Restaurants;

use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dates\Date;

class RestaurantReservation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','table_id','date_id'] ;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function date()
    {
        return $this->hasOne(Date::class);
    }
}
