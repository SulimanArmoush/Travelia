<?php

namespace App\Models\TheWorld\Facilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','facility_id'] ;
    protected $hidden = ['created_at', 'updated_at'];

}
