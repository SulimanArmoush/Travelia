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
        'area_id',
    ];



    public function facility()
    {
        return $this->hasOne(Facility::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

}