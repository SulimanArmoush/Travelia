<?php

namespace App\Models\TheWorld\Facilities;

use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Restaurants\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RestaurantReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'table_id',
        'DateTime',
        'cost'
    ];

    protected $hidden = ['created_at', 'updated_at'];


    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

}
