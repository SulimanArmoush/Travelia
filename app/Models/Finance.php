<?php

namespace App\Models;

use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'to',
        'before',
        'after',
        'Intake',
        'Description',
    ];

    //protected $hidden = ['created_at', 'updated_at'];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to');
    }
}
