<?php

namespace App\Models\TheWorld\Facilities\Organizers;

use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Location;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use App\Models\TheWorld\TouristArea;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'cost',
        'strDate',
        'endDate',
        'totalCapacity',
        'strLocation',
        'img',
        'touristArea_id',
        'hotel_id',
        'restaurant_id',
        'transporter_id',
        'capacity',
    ];
    protected $hidden = ['created_at', 'updated_at'];


    public function tripReservations(): hasMany
    {
        return $this->hasMany(TripReservation::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'strLocation');
    }

    public function touristArea(): BelongsTo
    {
        return $this->belongsTo(TouristArea::class, 'touristArea_id');
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class, 'transporter_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(TouristArea::class, 'touristArea_id');
    }

}
