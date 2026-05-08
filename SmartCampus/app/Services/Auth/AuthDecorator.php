<?php

namespace App\Services\Auth;

use App\Contracts\AuthServiceInterface;

/**
 * AuthDecorator — Abstract Decorator (Decorator Pattern)
 *
 * Kelas abstrak yang membungkus AuthServiceInterface.
 * Subclass (seperti OTPDecorator) menambahkan fungsionalitas tambahan
 * tanpa mengubah implementasi BasicAuth yang dibungkus.
 */
abstract class AuthDecorator implements AuthServiceInterface
{
    /**
     * AuthService yang dibungkus oleh decorator ini.
     */
    protected AuthServiceInterface $wrapped;

    public function __construct(AuthServiceInterface $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * Delegasi autentikasi ke wrapped service.
     * Subclass dapat meng-override untuk menambahkan fungsionalitas.
     */
    public function authenticate(array $credentials): bool
    {
        return $this->wrapped->authenticate($credentials);
    }
}
