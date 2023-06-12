<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Trip extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'latitude',
        'longitude',
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
        'round' => 'boolean',
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
     * @return Collection
     */
    public function getStudents(): Collection
    {
        return $this->getAttribute('students');
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
            ->withPivot('order', 'absent', 'embarked_at', 'disembarked_at')
            ->withTimestamps()
            ->latest($this->getAttribute('round') ? 'disembarked_at' : 'embarked_at')
            ->orderBy('order');
    }
}
