<?php

namespace App\Models\Permissions;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Finance;
use App\Models\TheWorld\Facilities\Requirement;
use App\Models\TheWorld\Facilities\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'updated_at'
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


    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function tripReservations()
    {
        return $this->hasMany(TripReservation::class);
    }

    public function facility()
    {
        return $this->hasOne(Facility::class)->with('location');
    }

    public function favorites()
    {
        return $this->belongsToMany(Facility::class, 'favorites');
    }

    public function recuirement()
    {
        return $this->hasOne(Requirement::class);
    }
    public function finance()
    {
        return $this->hasMany(Finance::class);
    }
}
