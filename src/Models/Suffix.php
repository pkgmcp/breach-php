<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Eloquent model for the breachphp_suffixes table.
 *
 * Stores SHA-1 hash suffixes with their breach counts, linked to a prefix.
 */
final class Suffix extends Model
{
    protected $table = 'breachphp_suffixes';

    protected $fillable = [
        'prefix_id',
        'suffix',
        'count',
    ];

    protected $casts = [
        'count' => 'integer',
    ];

    /**
     * Get the prefix that owns this suffix.
     */
    public function prefix(): BelongsTo
    {
        return $this->belongsTo(Prefix::class, 'prefix_id');
    }
}
