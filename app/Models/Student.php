<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $casts = [
        'goes' => 'boolean',
        'return' => 'boolean',
        'morning' => 'boolean',
        'afternoon' => 'boolean',
        'night' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'goes',
        'return',
        'morning',
        'afternoon',
        'night',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'address',
        'responsible',
        'school',
    ];

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->getAttribute('first_name');
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->getAttribute('address');
    }

    /**
     * @return Responsible
     */
    public function getResponsible(): Responsible
    {
        return $this->getAttribute('responsible');
    }

    /**
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return BelongsTo
     */
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Responsible::class);
    }

    /**
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsToMany
     */
    public function itineraries(): BelongsToMany
    {
        return $this->belongsToMany(Itinerary::class)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function trips(): BelongsToMany
    {
        return $this
            ->belongsToMany(Trip::class)
            ->withPivot('order', 'absent', 'embarked_at', 'disembarked_at')
            ->withTimestamps()
            ->oldest('started_at')
            ->oldest('arrive_at')
            ->oldest('finished_at');
    }
}
