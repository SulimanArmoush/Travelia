<?php

namespace App\Models\TheWorld\Facilities\Organizers;

use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;
    protected $fillable = ['facility_id','type'] ;

    
    public function facility() { 
        return $this->belongsTo(Facility::class); 
    } 

    public function trips() 
    {
        return $this->hasMany(Trip::class);
    }
}