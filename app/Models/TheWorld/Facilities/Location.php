<?php

namespace App\Models\TheWorld\Facilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TheWorld\Area;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'country',
        'state',
        'country_code'
        ];


    public function facility()
    {
        return $this->hasOne(Facility::class);
    }

}
