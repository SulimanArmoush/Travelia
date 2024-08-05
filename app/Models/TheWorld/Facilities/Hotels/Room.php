<?php

namespace App\Models\TheWorld\Facilities\Hotels;

use App\Models\TheWorld\Facilities\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_id', 'cost', 'type'];
    protected $hidden = ['created_at', 'updated_at'];


    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reservations(): hasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
