<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
