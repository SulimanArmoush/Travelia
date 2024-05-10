<?php

namespace App\Models\TheWorld;

use App\Models\TheWorld\Facilities\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouristArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'imgs',
        'location_id',
    ] ;

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
