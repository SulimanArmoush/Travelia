<?php

namespace App\Models\TheWorld;

use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $fillable = ['name','imgs','city_id'] ;


    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function Facilities()
    {
        return $this->hasMany(Facility::class);
    }

}
