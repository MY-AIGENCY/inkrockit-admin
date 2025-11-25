<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Helcim Payment Processor Configuration
 * 
 * API Documentation: https://devdocs.helcim.com/reference
 * Admin Portal: https://inkrockitcom.myhelcim.com/admin/api-access/5699
 */

return array(
    // API Base URL
    'api_url' => 'https://api.helcim.com/v2',
    
    // API Token - loaded from environment variable
    'api_token' => getenv('HELCIM_API_TOKEN') ?: '',
    
    // Enable test mode (set to false for production)
    'test_mode' => false,
    
    // Currency
    'currency' => 'USD',
    
    // API Version
    'api_version' => '2.2',
);

