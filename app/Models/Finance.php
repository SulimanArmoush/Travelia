<?php

namespace App\Models;

use App\Models\Permissions\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'to',
        'Expense',
        'Intake',
        'Description',
    ];

    //protected $hidden = ['created_at', 'updated_at'];

    public function fromUser()
    {
        return $this->belongsTo(User::class,'from');
    }
    public function toUser()
    {
        return $this->belongsTo(User::class,'to');
    }
}
