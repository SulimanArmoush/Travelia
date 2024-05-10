<?php

namespace App\Models\TheWorld\Facilities;

use App\Models\Permissions\User;
use App\Models\Dates\Date;
use App\Models\TheWorld\Area;
use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'imgs',
        'location_id',
        'user_id',
    ] ;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function hotel() {
        return $this->hasOne(Hotel::class);
    }
    public function restaurant() {
        return $this->hasOne(Restaurant::class);
    }

    public function organizer() {
        return $this->hasOne(Organizer::class);
    }

    public function transporter() {
        return $this->hasOne(Transporter::class);
    }

}



