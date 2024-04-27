<?php

namespace App\Models\TheWorld\Facilities\Transporters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    use HasFactory;
    protected $fillable = ['transporter_id','totalCapacity','cost','type','status'] ;


    
    public function transporterReservations()
    {
        return $this->hasMany(TransporterReservation::class);
    }

    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

}
