<?php

namespace App\Models\Permissions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function users(): hasMany
    {
        return $this->hasMany(User::class);
    }

    public function permission_role(): hasMany
    {
        return $this->hasMany(PermissionRole::class);
    }

    public function check($param): bool
    {
        $permission =
            Permission::query()
                ->where('name', '=', $param)->first();

        return PermissionRole::query()
            ->where('permission_id', '=', $permission->id)
            ->where('role_id', '=', $this->id)
            ->exists();
    }
}
