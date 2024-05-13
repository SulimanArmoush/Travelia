<?php

namespace App\Models\TheWorld\Facilities\Transporters;

use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    use HasFactory;
    protected $fillable = ['facility_id', 'type'];
    protected $hidden = ['created_at', 'updated_at'];


    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function transportations()
    {
        return $this->hasMany(Transportation::class);
    }

}
