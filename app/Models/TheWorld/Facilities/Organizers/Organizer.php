<?php

namespace App\Models\TheWorld\Facilities\Organizers;

use App\Models\Permissions\User;
use App\Models\TheWorld\Facilities\Facility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organizer extends Model
{
    use HasFactory;

    protected $fillable = ['facility_id', 'type'];
    protected $hidden = ['created_at', 'updated_at'];


    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function trips(): hasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Organizer::class, 'favorites');
    }
}
