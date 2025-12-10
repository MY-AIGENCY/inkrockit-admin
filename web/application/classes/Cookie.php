<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Cookie override class
 *
 * Overrides the default Kohana Cookie salt method to remove User-Agent
 * from the hash calculation. The User-Agent can change between requests
 * due to proxies, CDNs (Cloudflare), or load balancers, causing cookie
 * signature validation to fail and cookies to be deleted.
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

}
