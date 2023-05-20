<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'description',
        'place_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'morning' => 'boolean',
        'afternoon' => 'boolean',
        'night' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * @return HasOne
     */
    public function school(): HasOne
    {
        return $this->hasOne(School::class);
    }
}
