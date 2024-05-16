<?php

namespace App\Models\TheWorld\Facilities\Transporters;

use App\Models\TheWorld\Facilities\Location;
use App\Models\TheWorld\Facilities\Reservation;
use App\Models\TheWorld\TouristArea;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Routing extends Model
{
    use HasFactory;

    protected $fillable = [
        'transportation_id',
        'strLocation',
        'touristArea_id',
        'dateTime',
        'cost'
    ] ;
    protected $hidden = ['created_at', 'updated_at'];


    public function transportation() :BelongsTo
    {
        return $this->belongsTo(Transportation::class);
    }
    public function location() :BelongsTo
    {
        return $this->belongsTo(Location::class ,'strLocation' );
    }
    public function touristArea() :BelongsTo
    {
        return $this->belongsTo(TouristArea::class , 'touristArea_id');
    }
    public function reservation() :BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
