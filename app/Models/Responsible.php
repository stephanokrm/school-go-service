<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Responsible extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $with = [
        'user',
    ];


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->orderBy('first_name')->orderBy('last_name');
    }
}
