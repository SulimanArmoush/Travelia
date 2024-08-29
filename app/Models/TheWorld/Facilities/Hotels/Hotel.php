<?php

namespace App\Models\TheWorld\Facilities\Hotels;

use App\Models\TheWorld\Facilities\Facility;
use App\Models\TheWorld\Facilities\Reservation;
use App\Models\TheWorld\Facilities\RestaurantReservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = ['facility_id', 'type'];
    protected $hidden = ['created_at', 'updated_at'];


    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function rooms(): hasMany
    {
        return $this->hasMany(Room::class);
    }

    public function reservations(): HasManyThrough
    {
        return $this->hasManyThrough(Reservation::class, Room::class);
    }
}
