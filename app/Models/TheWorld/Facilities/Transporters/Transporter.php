<?php

namespace App\Models\TheWorld\Facilities\Transporters;

use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Transporter extends Model
{
    use HasFactory;

    protected $fillable = ['facility_id', 'type'];
    protected $hidden = ['created_at', 'updated_at'];


    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function transportations(): hasMany
    {
        return $this->hasMany(Transportation::class);
    }

    public function routs(): hasManyThrough
    {
        return $this->hasManyThrough(Routing::class, Transportation::class);
    }
}
