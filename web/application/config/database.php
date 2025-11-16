<?php

defined('SYSPATH') OR die('No direct access allowed.');

// Check if running in Docker (MYSQL_HOST environment variable is set)
if (getenv('MYSQL_HOST')) {
    // Docker development environment
    $connection = array(
        'dsn' => 'mysql:host=' . getenv('MYSQL_HOST') . ';dbname=' . getenv('MYSQL_DATABASE'),
        'username' => getenv('MYSQL_USER'),
        'password' => getenv('MYSQL_PASSWORD'),
        'persistent' => FALSE,
    );
} elseif ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1') {
    // Local development (not used on production server)
    $connection = array(
        'dsn' => 'mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=preprod',
        'username' => 'preprod_user',
        'password' => '!1q2w3eZ',
        'persistent' => FALSE,
    );
} else {
    // Production
    $connection = array(
        'dsn' => 'mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=preprod',
        'username' => 'preprod_user',
        'password' => '!1q2w3eZ',
        'persistent' => FALSE,
    );
}

return array('default' => array(
    'type' => 'PDO',
    'connection' => $connection,
    'table_prefix' => '',
    'charset' => 'utf8',
    'caching' => FALSE,)
);
