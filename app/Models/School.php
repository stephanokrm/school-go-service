<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    use HasFactory;

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
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
