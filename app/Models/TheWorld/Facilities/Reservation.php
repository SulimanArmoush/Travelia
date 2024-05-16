<?php

namespace App\Models\TheWorld\Facilities;

use App\Models\Permissions\User;
use App\Models\TheWorld\Facilities\Hotels\Room;
use App\Models\TheWorld\Facilities\Restaurants\Table;
use App\Models\TheWorld\Facilities\Transporters\Routing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dateTime',
        'room_id',
        'table_id',
        'routing_id',
        'placeNum',
        'daysNum',
        'eatDateTime',
        'cost'
    ];

    protected $hidden = ['created_at', 'updated_at'];


    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room() :BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function routing(): HasOne
    {
        return $this->hasOne(Routing::class);
    }
}
