<?php

namespace App\Models\TheWorld\Facilities\Transporters;

use App\Models\TheWorld\Facilities\Location;
use App\Models\TheWorld\Facilities\Reservation;
use App\Models\TheWorld\TouristArea;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Routing extends Model
{
    use HasFactory;

    protected $fillable = [
        'transportation_id',
        'strLocation',
        'endLocation',
        'dateTime',
        'cost',
        'capacity',
    ];
    protected $hidden = ['created_at', 'updated_at'];


    public function transportation(): BelongsTo
    {
        return $this->belongsTo(Transportation::class);
    }

    public function startLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'strLocation');
    }

    public function endedLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'endLocation');
    }

    public function reservation(): hasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
