<?php

defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Ukieapi {

    private static $config = array();

    static public function connect() {
        self::$config = Kohana::$config->load('ukieapi');
    }

    static public function post_bug($title, $description, $prior, $deadline) {
        return self::request('post_bug', array('title' => $title, 'description' => $description, 'priority' => $prior, 'deadline' => $deadline));
    }

    static public function get_bugs() {
        return self::request('get_bugs', array('page' => 0, 'limit' => 20));
    }

    static private function request($method, $method_data = array()) {
        if (empty(self::$config)) {
            self::connect();
        }
        $data = array(
            'project_id' => self::$config['project_id'],
            'key' => self::$config['key'],
        );
        $data = array_merge($data, $method_data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($ch, CURLOPT_URL, self::$config['api_url'] . $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        } else {
            curl_close($ch);
        }
        return json_decode($result);
    }

}