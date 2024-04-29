<?php

namespace App\Models\TheWorld\Facilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','facility_id','status'] ;

}
