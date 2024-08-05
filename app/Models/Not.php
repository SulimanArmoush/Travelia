<?php

namespace App\Models;

use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Not extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from');
    }
}
