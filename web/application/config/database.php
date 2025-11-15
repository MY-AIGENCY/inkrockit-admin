<?php

defined('SYSPATH') OR die('No direct access allowed.');

// Match GoDaddy config but with Ubuntu socket path
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1') {
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
