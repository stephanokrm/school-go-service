<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
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
     * @return Carbon|null
     */
    public function getStartedAt(): ?Carbon
    {
        return $this->getAttribute('started_at');
    }

    /**
     * @return Carbon|null
     */
    public function getFinishedAt(): ?Carbon
    {
        return $this->getAttribute('finished_at');
    }

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

    public function isRound(): bool
    {
        return $this->getAttribute('round');
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
            ->withTimestamps();
    }
}
