<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class LegacyUserProvider extends EloquentUserProvider
{
    /**
     * Validate a user against the given credentials.
     * Handles both MD5 (legacy) and bcrypt passwords.
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        $plain = $credentials['password'];
        $storedHash = $user->getAuthPassword();

        // Check if this is an MD5 hash (32 characters)
        if (strlen($storedHash) === 32) {
            // Legacy MD5 authentication
            if (md5($plain) === $storedHash) {
                // Upgrade to bcrypt
                $this->upgradePassword($user, $plain);
                return true;
            }
            return false;
        }

        // Standard bcrypt authentication
        return Hash::check($plain, $storedHash);
    }

    /**
     * Upgrade a legacy MD5 password to bcrypt.
     */
    protected function upgradePassword(Authenticatable $user, string $plainPassword): void
    {
        $user->password = Hash::make($plainPassword);
        $user->save();
    }

    /**
     * Retrieve a user by the given credentials.
     * Allows login by email or login field.
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        // Remove password from credentials for lookup
        $credentials = array_filter(
            $credentials,
            fn ($key) => !str_contains($key, 'password'),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($credentials)) {
            return null;
        }

        $query = $this->newModelQuery();

        // If email is provided, search by email
        if (isset($credentials['email'])) {
            $query->where('email', $credentials['email']);
        }

        // If login is provided, search by login
        if (isset($credentials['login'])) {
            $query->where('login', $credentials['login']);
        }

        return $query->first();
    }
}
