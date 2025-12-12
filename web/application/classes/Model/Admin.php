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
        $login = is_string($login) ? trim($login) : $login;

        if (!empty($login) && !empty($pass)) {
            // Allow login by username OR email. Admin access is determined by group_id >= 2.
            // Password storage has evolved over time (legacy MD5 -> modern bcrypt), so we
            // fetch the candidate user and verify the password in PHP.
            $res = DB::sql_row('SELECT users.*, user_group.access
                FROM users
                LEFT JOIN user_group ON user_group.id=users.group_id
                WHERE (login = :login OR email = :login)
                  AND group_id>=2', array(':login' => $login));

            if (!empty($res) && self::verify_password((string) $res['password'], (string) $pass)) {
                $session = Session::instance();
                // Prevent session fixation on privilege change.
                $session->regenerate();

                $session->set('user', $res['login']);
                $session->set('admin_user', $res);

                // Backward compatibility: some legacy code expects admin data in this cookie.
                Cookie::set('admin_user', serialize($res));
                return true;
            }
        }
        return false;
    }

    /**
     * Verify a user password against stored hash.
     *
     * Supports:
     * - bcrypt (`$2y$...`) used by newer systems
     * - legacy MD5 hex digests (32 chars) used by older systems
     */
    protected static function verify_password($stored_hash, $plain_password) {
        $stored_hash = is_string($stored_hash) ? trim($stored_hash) : '';
        if ($stored_hash === '') {
            return false;
        }

        // bcrypt / password_hash() formats
        if (strpos($stored_hash, '$2y$') === 0 || strpos($stored_hash, '$2a$') === 0 || strpos($stored_hash, '$2b$') === 0) {
            return password_verify($plain_password, $stored_hash);
        }

        // Legacy MD5 (32 hex chars)
        if (preg_match('/^[a-f0-9]{32}$/i', $stored_hash)) {
            return strcasecmp($stored_hash, md5($plain_password)) === 0;
        }

        // Unknown format
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
