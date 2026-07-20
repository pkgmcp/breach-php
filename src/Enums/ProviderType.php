<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Enums;

/**
 * Enumerates the supported breach data providers.
 */
enum ProviderType: string
{
    case HIBP = 'hibp';

    /**
     * Get the human-readable name for the provider.
     */
    public function label(): string
    {
        return match ($this) {
            self::HIBP => 'Have I Been Pwned',
        };
    }
}
