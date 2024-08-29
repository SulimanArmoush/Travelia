<?php

namespace App\Models\TheWorld\Facilities\Restaurants;

use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\Facilities\RestaurantReservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['facility_id', 'type'];
    protected $hidden = ['created_at', 'updated_at'];


    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function tables(): hasMany
    {
        return $this->hasMany(Table::class);
    }

    public function restaurantReservations(): HasManyThrough
    {
        return $this->hasManyThrough(RestaurantReservation::class, Table::class);
    }
}
