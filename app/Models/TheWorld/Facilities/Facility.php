<?php

namespace App\Models\TheWorld\Facilities;

use App\Models\Permissions\User;
use App\Models\TheWorld\Facilities\Hotels\Hotel;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Restaurants\Restaurant;
use App\Models\TheWorld\Facilities\Transporters\Transporter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'img',
        'location_id',
        'user_id',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function hotel(): hasOne
    {
        return $this->hasOne(Hotel::class);
    }

    public function restaurant(): hasOne
    {
        return $this->hasOne(Restaurant::class);
    }

    public function organizer(): hasOne
    {
        return $this->hasOne(Organizer::class);
    }

    public function transporter(): hasOne
    {
        return $this->hasOne(Transporter::class);
    }

}



