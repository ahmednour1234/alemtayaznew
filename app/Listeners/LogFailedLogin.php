<?php

namespace App\Listeners;

use App\Services\Security\SecurityLogger;
use Illuminate\Auth\Events\Failed;

/**
 * Persists every failed authentication attempt (wrong credentials) to the
 * failed_login_logs table via the central SecurityLogger. The attempted password
 * is never read or stored.
 */
class LogFailedLogin
{
    public function __construct(private readonly SecurityLogger $logger) {}

    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? null;

        $this->logger->logFailedLogin(
            is_string($email) ? $email : null,
            $event->guard ?? 'web',
            request(),
            'bad_credentials',
        );
    }
}
