<?php

namespace App\Models\Dates;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    use HasFactory;
    protected $fillable = ['month_id','day_id','hour_id'] ;



    public function day(){
        return $this->hasOne(Day::class);
    }
    public function hour(){
        return $this->hasOne(Hour::class);
    }
    public function month(){
        return $this->hasOne(Month::class);
    }
}
