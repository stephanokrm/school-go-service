<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'license',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'user',
    ];

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->getAttribute('user');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->orderBy('first_name')->orderBy('last_name');
    }

    /**
     * @return HasMany
     */
    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class);
    }
}
