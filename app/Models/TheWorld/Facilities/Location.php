<?php

namespace App\Models\TheWorld\Facilities;

use App\Models\TheWorld\Facilities\Organizers\Trip;
use App\Models\TheWorld\TouristArea;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    protected $hidden = ['created_at', 'updated_at'];


    public function facility(): hasOne
    {
        return $this->hasOne(Facility::class);
    }

    public function touristArea(): hasOne
    {
        return $this->hasOne(TouristArea::class);
    }

    public function trip(): hasOne
    {
        return $this->hasOne(Trip::class);
    }

}
