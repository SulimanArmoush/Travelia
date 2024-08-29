<?php

namespace App\Models\TheWorld\Facilities\Restaurants;

use App\Models\TheWorld\Facilities\RestaurantReservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    protected $fillable = ['restaurant_id', 'cost', 'type'];
    protected $hidden = ['created_at', 'updated_at'];


    public function restaurantReservations(): hasMany
    {
        return $this->hasMany(RestaurantReservation::class, 'table_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

}
