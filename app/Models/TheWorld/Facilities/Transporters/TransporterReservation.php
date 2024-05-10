<?php

namespace App\Models\TheWorld\Facilities\Transporters;

use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dates\Date;

class TransporterReservation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'transportation_id', 'placeNum', 'dateTime'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transportation()
    {
        return $this->belongsTo(Transportation::class);
    }

    public function date()
    {
        return $this->hasOne(Date::class);
    }
}
