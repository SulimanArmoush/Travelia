<?php

namespace App\Models\TheWorld\Facilities\Transporters;

use App\Models\TheWorld\Facilities\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transportation extends Model
{
    use HasFactory;

    protected $fillable = ['transporter_id', 'totalCapacity', 'cost', 'type'];
    protected $hidden = ['created_at', 'updated_at'];


    public function routings(): hasMany
    {
        return $this->hasMany(Routing::class);
    }

    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

}
