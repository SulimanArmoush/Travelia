<?php

namespace App\Models;

use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'title',
        'msg',
    ];

    //protected $hidden = ['created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from');
    }
}
