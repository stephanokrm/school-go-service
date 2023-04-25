<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\Role as RoleEnum;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['role'];

    /**
     * Get the user's first name.
     */
    protected function role(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => match(RoleEnum::from($value)) {
                RoleEnum::Responsible => 'Responsável',
                RoleEnum::Driver => 'Motorista',
                RoleEnum::Administrator => 'Administrador',
            },
        );
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
