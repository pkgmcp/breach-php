<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Eloquent model for the breachphp_prefixes table.
 *
 * Stores SHA-1 hash prefixes with their last sync timestamp.
 */
final class Prefix extends Model
{
    protected $table = 'breachphp_prefixes';

    protected $fillable = [
        'prefix',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    /**
     * Get the suffixes belonging to this prefix.
     */
    public function suffixes(): HasMany
    {
        return $this->hasMany(Suffix::class, 'prefix_id');
    }
}
