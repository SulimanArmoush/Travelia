<?php

namespace App\Models\TheWorld\Facilities;

use App\Models\TheWorld\TouristArea;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'country',
        'state',
        'city'
        ];


    public function facility()
    {
        return $this->hasOne(Facility::class);
    }

    public function touristArea()
    {
        return $this->hasOne(TouristArea::class);
    }

}
