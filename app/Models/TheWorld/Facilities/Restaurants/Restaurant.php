<?php

namespace App\Models\TheWorld\Facilities\Restaurants;

use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;
    protected $fillable = ['facility_id','type'] ;
    protected $hidden = ['created_at', 'updated_at'];


    public function facility() {
        return $this->belongsTo(Facility::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

}
