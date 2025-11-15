<?php

defined('SYSPATH') or die('No direct access allowed.');

class Model_Admin extends Model {
    /*
     * Check admin login/password
     * @param (string) $login: user login
     * @param (string) $pass: user password
     * @return (bool)
     */

    public static function check_login($login, $pass) {
        if (!empty($login) && !empty($pass)) {
            $res = DB::sql_row('SELECT users.*, user_group.access
                FROM users
                LEFT JOIN user_group ON user_group.id=users.group_id
                WHERE login = :login AND password = MD5(:pass) AND group_id>=2', array(':login' => $login, ':pass' => $pass));
            if (!empty($res)) {
                Session::instance()->set('user', $res['login']);
                Cookie::set('admin_user', serialize($res));
                return true;
            }
        }
        return false;
    }

    /*
     * Calculate FOUND_ROWS in DB
     * @return (int)
     */

    public function calcPages() {
        $rez = DB::sql_row('SELECT FOUND_ROWS() counts');
        if (!empty($rez) && isset($rez['counts'])) {
            return (int) $rez['counts'];
        }
        return 0;
    }

    // OLD VERSION - REPLACED
    public function calcPages_old() {
        $rez = DB::sql_row('SELECT FOUND_ROWS() counts');
        if (!empty($rez)) {
            return $rez['counts'];
        }
    }

    /*
     * Get my tasks
     * @param (int) $uid: current user id
     * @return (int) count
     */

    public function getMyTasks($uid) {
        $rez = DB::sql_row('SELECT COUNT(1) counts FROM request_note_required 
            LEFT JOIN request_notes ON request_notes.id=request_note_required.note_id
            WHERE for_uid=:uid AND request_notes.removed=0 AND status=0', array(':uid' => $uid));
        return $rez['counts'];
    }
    
    public function getAssignTasks($uid) {
        $rez = DB::sql_row('SELECT COUNT(1) counts FROM request_note_required 
            LEFT JOIN request_notes ON request_notes.id=request_note_required.note_id
            WHERE from_uid=:uid AND request_notes.removed=0 AND status=0', array(':uid' => $uid));
        return $rez['counts'];
    }

    public function parse_serialize_js($data) {
        $result_data = array();
        $arr = explode('&', $data);
        if (!empty($arr)) {
            foreach ($arr as $val) {
                $values = explode('=', $val);
                $result_data[$values[0]] = urldecode($values[1]);
            }
        }
        return $result_data;
    }
    
    public function getActiveCustomers(){
        $all_eye = DB::sql_row('SELECT COUNT(*) counts FROM eye_user_company');
        return $all_eye['counts'];
    }

}

?>
