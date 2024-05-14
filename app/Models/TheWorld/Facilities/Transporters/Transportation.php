<?php

namespace App\Models\TheWorld\Facilities\Transporters;

use App\Models\TheWorld\Facilities\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    use HasFactory;
    protected $fillable = ['transporter_id','totalCapacity','cost','type','status'] ;
    protected $hidden = ['created_at', 'updated_at'];


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

}
