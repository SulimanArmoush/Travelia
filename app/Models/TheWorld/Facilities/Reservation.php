<?php

namespace App\Models\TheWorld\Facilities;

use App\Models\Permissions\User;
use App\Models\TheWorld\Facilities\Hotels\Room;
use App\Models\TheWorld\Facilities\Transporters\Routing;
use App\Models\TheWorld\TouristArea;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'area_id',
        'strDate',
        'placeNum',
        'endDate',
        'room_id',
        'routing_id',
        'cost'
    ];

    protected $hidden = ['created_at', 'updated_at'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function routing(): belongsTo
    {
        return $this->belongsTo(Routing::class);
    }

    public function restaurantReservations(): HasMany
    {
        return $this->hasMany(RestaurantReservation::class);
    }

    public function area(): belongsTo
    {
        return $this->belongsTo(TouristArea::class, 'area_id');
    }

}
