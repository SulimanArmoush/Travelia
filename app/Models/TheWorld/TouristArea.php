<?php

namespace App\Models\TheWorld;

use App\Models\TheWorld\Facilities\Location;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TouristArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'img',
        'location_id',
        'type'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
