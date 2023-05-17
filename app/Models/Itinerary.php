<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Itinerary extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $casts = [
        'monday' => 'boolean',
        'tuesday' => 'boolean',
        'wednesday' => 'boolean',
        'thursday' => 'boolean',
        'friday' => 'boolean',
        'morning' => 'boolean',
        'afternoon' => 'boolean',
        'night' => 'boolean',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'morning',
        'afternoon',
        'night',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'address',
        'driver',
        'school',
    ];

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->getAttribute('address');
    }

    /**
     * @return Driver
     */
    public function getDriver(): Driver
    {
        return $this->getAttribute('driver');
    }

    /**
     * @return School
     */
    public function getSchool(): School
    {
        return $this->getAttribute('school');
    }

    /**
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return BelongsTo
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return BelongsToMany
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
