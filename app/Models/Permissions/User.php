<?php

namespace App\Models\Permissions;

use App\Models\Finance;
use App\Models\Not;
use App\Models\TheWorld\Facilities\Organizers\Organizer;
use App\Models\TheWorld\Facilities\Requirement;
use App\Models\TheWorld\Facilities\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\TheWorld\Facilities\Organizers\TripReservation;
use App\Models\TheWorld\Facilities\Facility;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'phone',
        'password',
        'age',
        'address',
        'wallet',
        'confirmation',
        'photo',
        'passport',
        'role_id',
        'type',
        'deviceToken'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deviceToken'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function reservations(): hasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function tripReservations(): hasMany
    {
        return $this->hasMany(TripReservation::class);
    }

    public function facility(): hasOne
    {
        return $this->hasOne(Facility::class)->with('location');
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Organizer::class, 'favorites');
    }

    public function recuirement(): hasOne
    {
        return $this->hasOne(Requirement::class);
    }

    public function finances(): hasMany
    {
        return $this->hasMany(Finance::class, 'to');
    }

    public function notifications(): hasMany
    {
        return $this->hasMany(Not::class);

    }
}
