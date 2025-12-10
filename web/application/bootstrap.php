<?php

defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------
// Load the core Kohana class
require SYSPATH . 'classes/Kohana/Core' . EXT;

if (is_file(APPPATH . 'classes/Kohana' . EXT)) {
    // Application extends the core
    require APPPATH . 'classes/Kohana' . EXT;
} else {
    // Load empty core extension
    require SYSPATH . 'classes/Kohana' . EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('America/Chicago');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV'])) {
    Kohana::$environment = constant('Kohana::' . strtoupper($_SERVER['KOHANA_ENV']));
}

Cookie::$salt = '34249erhjrfX401209vdsdfRnHS4Jdy4UJJP9';
Cookie::$expiration = Date::MONTH;
Cookie::$path = '/';
Cookie::$domain = '.inkrockit.com'; // Allow cookies across subdomains

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
    'base_url' => '/',
    'index_file' => FALSE
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
//Kohana::$log->attach(new Log_File(APPPATH . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    // 'auth'       => MODPATH.'auth',       // Basic authentication
    // 'cache'      => MODPATH.'cache',      // Caching with multiple backends
    // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    'database' => MODPATH . 'database', // Database access
    'image' => MODPATH . 'image', // Image manipulation
    // 'orm'        => MODPATH.'orm',        // Object Relationship Mapping
    // 'unittest'   => MODPATH.'unittest',   // Unit testing
    'captcha' => MODPATH . 'captcha',
    'pagination' => MODPATH . 'pagination', // 
    'ukieapi' => MODPATH . 'ukieapi', // PPT Ukiepro Api
//    'userguide' => MODPATH . 'userguide', // User guide and API documentation
));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('admin', 'admin(/<action>)', array('action' => '(login|logout)'))
        ->defaults(array(
            'directory' => 'admin',
            'controller' => 'index',
        ));
Route::set('admin_all', 'admin(/<controller>(/<action>(/<param1>(/<param2>))))')
        ->defaults(array(
            'directory' => 'admin'
        ));

Route::set('ajax', 'ajax(/<action>)')
        ->defaults(array(
            'controller' => 'ajax',
            'action' => 'index'
        ));
Route::set('upload', 'upload(/<action>(/<id>))')
        ->defaults(array(
            'controller' => 'upload',
            'action' => 'index'
        ));

Route::set('inspiration_station', 'inspiration_station(/<id>)')
        ->defaults(array(
            'controller' => 'inspiration',
            'action' => 'index',
        ));
Route::set('print_it', 'print_it(/<id>)')
        ->defaults(array(
            'controller' => 'print',
            'action' => 'index',
        ));
Route::set('designlab', 'designlab(/<id>)')
        ->defaults(array(
            'controller' => 'design',
            'action' => 'index',
        ));
Route::set('default', '(<controller>(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'home',
            'action' => 'index',
        ));
