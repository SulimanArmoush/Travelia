<?php

namespace App\Models\Dates;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;
    protected $fillable = ['name'] ;



    public function date(){
        return $this->belongsTo(Date::class);
    }
}
