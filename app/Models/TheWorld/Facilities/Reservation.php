<?php

namespace App\Models\TheWorld\Facilities;

use App\Models\Permissions\User;
use App\Models\TheWorld\Facilities\Hotels\Room;
use App\Models\TheWorld\Facilities\Restaurants\Table;
use App\Models\TheWorld\Facilities\Transporters\Transportation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','dateTime','room_id','table_id'
        ,'transportation_id','placeNum','daysNum','eatDateTime'] ;

    protected $hidden = ['created_at', 'updated_at'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function transportation()
    {
        return $this->belongsTo(Transportation::class);
    }
}
