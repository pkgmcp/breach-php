<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Cache\ArrayCache;
use ShamimStack\BreachPHP\Hash\Sha1Hasher;
use ShamimStack\BreachPHP\Http\HttpClient;
use ShamimStack\BreachPHP\Http\RequestFactory;
use ShamimStack\BreachPHP\Parsers\HibpParser;
use ShamimStack\BreachPHP\Providers\HibpProvider;
use ShamimStack\BreachPHP\Services\PasswordChecker;

if (! function_exists('breach_check')) {
    /**
     * Check whether a password has appeared in known data breaches.
     *
     * When running inside Laravel, uses the container's PasswordCheckerInterface.
     * When running standalone (pure PHP), creates a default PasswordChecker
     * with HIBP provider, array cache, and no persistent storage.
     *
     * @param  string  $password  The plaintext password to check.
     * @return \ShamimStack\BreachPHP\DTO\PasswordResult An immutable result containing the breach status and metadata.
     */
    function breach_check(string $password): \ShamimStack\BreachPHP\DTO\PasswordResult
    {
        if (function_exists('app')) {
            $checker = app(\ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface::class);

            return $checker->check($password);
        }

        $checker = breach_create_checker();

        return $checker->check($password);
    }
}

if (! function_exists('breach_is_safe')) {
    /**
     * Check whether a password is safe (not found in any breach).
     */
    function breach_is_safe(string $password): bool
    {
        return breach_check($password)->isSafe();
    }
}

if (! function_exists('breach_is_breached')) {
    /**
     * Check whether a password has been breached.
     */
    function breach_is_breached(string $password): bool
    {
        return breach_check($password)->isBreached();
    }
}

if (! function_exists('breach_create_checker')) {
    /**
     * Create a standalone PasswordChecker instance for pure PHP usage.
     *
     * Uses HIBP API with SHA-1 hashing, array cache, and no persistent storage.
     * Suitable for scripts, cron jobs, or any non-Laravel context.
     */
    function breach_create_checker(): PasswordChecker
    {
        $hasher = new Sha1Hasher();
        $parser = new HibpParser();
        $requestFactory = new RequestFactory();
        $httpClient = new HttpClient(
            client: new \GuzzleHttp\Client(),
            requestFactory: $requestFactory,
        );
        $provider = new HibpProvider($httpClient, $parser);
        $cache = new ArrayCache();

        return new PasswordChecker(
            provider: $provider,
            hasher: $hasher,
            cache: $cache,
        );
    }
}
