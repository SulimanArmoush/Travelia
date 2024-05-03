<?php

namespace App\Models\TheWorld\Facilities\Hotels;

use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    protected $fillable = ['facility_id','type'] ;

    
    public function facility() { 
        return $this->belongsTo(Facility::class); 
    } 

    public function rooms() 
    {
        return $this->hasMany(Room::class);
    }
}
