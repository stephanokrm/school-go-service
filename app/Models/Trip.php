<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trip extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'path',
        'arrive_at',
        'latitude',
        'longitude',
        'started_at',
        'finished_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'arrive_at' => 'datetime',
        'finished_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'path' => 'array',
        'started_at' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'itinerary',
    ];

    /**
     * @return Itinerary
     */
    public function getItinerary(): Itinerary
    {
        return $this->getAttribute('itinerary');
    }

    /**
     * @return BelongsTo
     */
    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    /**
     * @return BelongsToMany
     */
    public function students(): BelongsToMany
    {
        return $this
            ->belongsToMany(Student::class)
            ->withPivot('order', 'embarked_at', 'disembarked_at')
            ->withTimestamps()
            ->latest('embarked_at')
            ->orderBy('order');
    }
}
