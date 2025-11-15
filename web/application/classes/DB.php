<?php

defined('SYSPATH') OR die('No direct access allowed.');

class DB extends Kohana_DB {

    static public function get($table) {
        return self::sql('SELECT * FROM ' . $table);
    }

    static public function get_row($table) {
        return self::sql_row('SELECT * FROM ' . $table);
    }

    /*
     * DB::sql('SELECT * FROM config'); - array results
     * DB::sql('SELECT * FROM config', 'count'); - where, count rows
     * DB::sql('SELECT * FROM config WHERE `key`=:index', array(':index'=>'index')); - all results array, parameters
     */

    static public function sql($sql, $params = array(), $result = 'as_array') {
        $DB = Database::instance();
        $type = self::_FindQuery($sql);

        if (!empty($params) && is_array($params)) {
            $sql = self::_filterQuery($sql, $params, $DB);
        }
        $rez = $DB->query($type, $sql);
        if ($type === Database::SELECT) {
            return $rez->$result();
        } else {
            return $rez;
        }
    }

    /*
     * DB::sql_row('SELECT * FROM config'); - first row result
     * DB::sql_row('SELECT * FROM config WHERE `key`=:index',array(':index'=>'index')); - first row result, parameters
     */

    static public function sql_row($sql, $params = array()) {
        $DB = Database::instance();
        $type = self::_FindQuery($sql);

        if (!empty($params) && is_array($params)) {
            $sql = self::_filterQuery($sql, $params, $DB);
        }
        $rez = $DB->query($type, $sql);
        if ($type === Database::SELECT) {
            return $rez->offsetGet(0);
        } else {
            return $rez;
        }
    }

    static protected function _FindQuery($sql) {
        $sql = trim($sql);
        if (stripos($sql, 'SELECT ') == 0 && stripos($sql, 'SELECT ') !== FALSE) {
            return Database::SELECT;
        } elseif (stripos($sql, 'INSERT ') == 0 && stripos($sql, 'INSERT ') !== FALSE) {
            return Database::INSERT;
        } elseif (stripos($sql, 'UPDATE ') == 0 && stripos($sql, 'UPDATE ') !== FALSE) {
            return Database::UPDATE;
        } elseif (stripos($sql, 'DELETE ') == 0 && stripos($sql, 'DELETE ') !== FALSE) {
            return Database::DELETE;
        }
    }

    static protected function _filterQuery($sql, $params, $DB) {
        $re = array();
        foreach ($params as $key => $val) {
            $re[strlen($key)][$key] = $val;
        }
        ksort($re);
        $re = array_reverse($re);
        foreach ($re as $val) {
            foreach ($val as $k => $v) {
                $sql = str_replace($k, $DB->escape($v), $sql);
            }
        }
        return $sql;
    }

}
