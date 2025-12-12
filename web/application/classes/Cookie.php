<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Cookie override class
 *
 * Overrides the default Kohana Cookie class to:
 * 1. Remove User-Agent from salt calculation (fixes proxy/CDN issues)
 * 2. Add SameSite=Lax attribute for modern browser compatibility
 *
 * @package    Application
 * @category   Helpers
 */
class Cookie extends Kohana_Cookie {

    /**
     * Generates a salt string for a cookie based on the name and value.
     *
     * IMPORTANT: This override removes User-Agent from the salt calculation.
     * The original Kohana implementation includes User-Agent which causes
     * issues when User-Agent changes between requests (common with CDNs/proxies).
     *
     * @param   string  $name   name of cookie
     * @param   string  $value  value of cookie
     * @return  string
     */
    public static function salt($name, $value)
    {
        // Require a valid salt
        if (!Cookie::$salt) {
            throw new Kohana_Exception('A valid cookie salt is required. Please set Cookie::$salt.');
        }

        // Generate hash WITHOUT User-Agent (removed to fix proxy/CDN issues)
        return sha1($name . $value . Cookie::$salt);
    }

    /**
     * Sets a signed cookie with SameSite attribute for modern browser compatibility.
     *
     * Modern browsers (Chrome 80+, Firefox 86+, Safari 14+) require the SameSite
     * attribute or they may reject/block cookies. This override adds SameSite=Lax
     * which allows cookies on normal navigation but prevents CSRF attacks.
     *
     * @param   string  $name       name of cookie
     * @param   string  $value      value of cookie
     * @param   integer $expiration lifetime in seconds
     * @return  boolean
     */
    public static function set($name, $value, $expiration = NULL)
    {
        if ($expiration === NULL) {
            $expiration = Cookie::$expiration;
        }

        if ($expiration !== 0) {
            $expiration += time();
        }

        // Add the salt to the cookie value
        $value = Cookie::salt($name, $value) . '~' . $value;

        // Use PHP 7.3+ array syntax for setcookie to support SameSite
        if (PHP_VERSION_ID >= 70300) {
            return setcookie($name, $value, [
                'expires' => $expiration,
                'path' => Cookie::$path,
                'domain' => Cookie::$domain,
                'secure' => Cookie::$secure,
                'httponly' => Cookie::$httponly,
                'samesite' => 'Lax'
            ]);
        }

        // Fallback for PHP < 7.3 (append SameSite to path as workaround)
        return setcookie($name, $value, $expiration, Cookie::$path . '; SameSite=Lax', Cookie::$domain, Cookie::$secure, Cookie::$httponly);
    }

    /**
     * Deletes a cookie with proper SameSite attribute.
     *
     * @param   string  $name   cookie name
     * @return  boolean
     */
    public static function delete($name)
    {
        unset($_COOKIE[$name]);

        if (PHP_VERSION_ID >= 70300) {
            return setcookie($name, '', [
                'expires' => time() - 86400,
                'path' => Cookie::$path,
                'domain' => Cookie::$domain,
                'secure' => Cookie::$secure,
                'httponly' => Cookie::$httponly,
                'samesite' => 'Lax'
            ]);
        }

        return setcookie($name, NULL, -86400, Cookie::$path . '; SameSite=Lax', Cookie::$domain, Cookie::$secure, Cookie::$httponly);
    }

}
