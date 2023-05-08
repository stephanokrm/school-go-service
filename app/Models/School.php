<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $casts = [
        'morning' => 'boolean',
        'afternoon' => 'boolean',
        'night' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'morning',
        'afternoon',
        'night',
        'morning_entry_time',
        'morning_departure_time',
        'afternoon_entry_time',
        'afternoon_departure_time',
        'night_entry_time',
        'night_departure_time',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'address',
    ];

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->getAttribute('address');
    }

    /**
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return HasMany
     */
    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class);
    }
}
