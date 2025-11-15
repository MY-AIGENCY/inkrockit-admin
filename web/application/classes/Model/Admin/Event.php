<?php

class Model_Admin_Event extends Model {

    //Model::factory('Admin_Event')->add_event('test','id','test2');

    public function __construct() {
        $cookies = new Cookie();
        $this->admin = $cookies->get('admin_user');
        if (!empty($this->admin)) {
            $this->admin = unserialize($this->admin);
        }
    }

    public function add_event($type, $type_id, $text = '') {
        DB::sql('INSERT INTO events (`date`, `type`, `text`, uid, type_id) 
            VALUES (NOW(), :type, :text, :uid, :type_id)', array(':type' => $type, ':text' => $text, ':uid' => $this->admin['id'], ':type_id' => $type_id));
    }

    /*
     * fedex_ship ->request id
     * fedex_pickup ->request id
     * close_ship - request id
     * card_payment ->payment history id
     * new_request -> request id
     * new_user -> user id
     * payment_removed -> paument id
     * creditcard_removed -> card id
     * request_removed - request id
     * add_creditcard - card id
     * user_updated - user id
     * update_request - request id
     * new_job - job id
     * job_removed - job removed
     * order_confirmed ->payment history id
     * order_modif ->payment history id
     * cash_payment ->payment history id
     * check_payment ->payment history id
     * misc_payment ->payment history id
     * redistr_payment ->payment history id
     * edit_transaction ->payment history id
     * order_failed
     */

    public function get_events($page, $for_page) {
        $begin = $page * $for_page;
        return DB::sql('SELECT SQL_CALC_FOUND_ROWS events.id, events.*, CONCAT(users.first_name," ",users.last_name) username, 
            DATE_FORMAT(events.date, "%m-%d-%Y %H:%i:%s") date FROM events
            LEFT JOIN users ON users.id=events.uid ORDER BY id DESC LIMIT '.$begin.','.$for_page);
    }
    
    /*
     * Event details
     * @param (int) $id
     */
    public function event_details($id){
        return DB::sql_row('SELECT * FROM events WHERE id=:id', array(':id'=>$id));
    }


}