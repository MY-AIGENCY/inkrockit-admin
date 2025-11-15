<?php

class Model_Admin_Print extends Model_Print {
    /*
     * Get count all requests
     * @return (int): count requests 
     */

    public function get_count_req() {
        return DB::query(Database::SELECT, 'SELECT COUNT(1) counts FROM requests')->get('counts');
    }

    /*
     * Save print request data and user info
     * @param (int) $id: request id
     */

    public function save_print_req($id) {
        $upd_array = array();
        $post = Request::initial()->post();
        $fields = array('operating_sys', 'graphics_app', 'ref_source', 'other_source', 'offers', 'conversations', 'search_id', 'search_keyword');
        foreach ($fields as $val) {
            $upd_array[$val] = $post['item'][$val];
        }

        $upd_array['complete_address'] = $post['user']['company'] . ' 
' . $post['user']['first_name'] . ' ' . $post['user']['last_name'] . ' 
' . $post['user']['street'] . ' 
' . $post['user']['city'] . ', ' . $post['user']['state'] . ' ' . $post['user']['zipcode'] . ' 
' . $post['user']['phone'];
        if (!empty($post['user']['phone_ext'])) {
            $upd_array['complete_address'].=' ext ' . $post['user']['phone_ext'];
        }

        $rez_update = DB::update('requests')->set($upd_array)->where('id', '=', $id)->execute();
        if (!empty($rez_update)) {
            //add event
            Model::factory('Admin_Event')->add_event('update_request', $id, "");
        }

        //update main user data
        $fields = array('first_name', 'last_name', 'position', 'street', 'street2', 'city', 'state', 'zipcode', 'phone', 'phone_ext', 'fax', 'email', 'industry', 'country');
        foreach ($fields as $val) {
            $user_upd_array[$val] = $post['user'][$val];
        }
        $rez_update = DB::update('users')->set($user_upd_array)->where('id', '=', $post['user']['id'])->execute();
        if (!empty($rez_update)) {
            //add event
            Model::factory('Admin_Event')->add_event('user_updated', $post['user']['id'], "");
        }
        //UPDATE user company
        DB::sql('UPDATE users_company SET company=:company, duplicate=:duplicate WHERE users_company.id=(SELECT company_id FROM users WHERE id=:id)', array(':id' => $post['user']['id'], ':company' => $post['user']['company'], ':duplicate' => $post['duplicate']));

        //update additional phone/email
        DB::sql('DELETE FROM user_additional_info WHERE uid=:uid', array(':uid' => $post['user']['id']));
        if (!empty($post['phone_alt'])) {
            foreach ($post['phone_alt'] as $key => $val) {
                DB::sql('INSERT INTO user_additional_info (uid,`type`,`value`,ext, content_type) VALUES (:uid,"phone",:value,:ext, :content_type)', array(':uid' => $post['user']['id'], ':value' => $val, ':ext' => $post['phone_ext_alt'][$key], ':content_type' => $post['addtional_phone_type'][$key]));
            }
        }
        if (!empty($post['email_alt'])) {
            foreach ($post['email_alt'] as $val) {
                DB::sql('INSERT INTO user_additional_info (uid,`type`,`value`) VALUES (:uid,"email",:value)', array(':uid' => $post['user']['id'], ':value' => $val));
            }
        }

        //user abbr
//        $user_data = DB::sql_row('SELECT id,abbr FROM users_company WHERE id=(SELECT company_id FROM users WHERE id=:id)', array(':id' => $post['user']['id']));
//        if (!empty($post['abbr']) && $post['abbr'] != $user_data['abbr']) {
//            DB::sql('UPDATE users_company SET abbr=:user_abbr WHERE id=:id', array(':id' => $user_data['id'], ':user_abbr' => $post['abbr']));
//
//            //find and change user job/estimate id
//            $jobs = DB::sql('SELECT * FROM user_jobs WHERE company_id=:id', array(':id' => $user_data['id']));
//            if (!empty($jobs)) {
//                foreach ($jobs as $val) {
//                    $where = array();
//                    if (!empty($val['job_id'])) {
//                        $new_job = str_replace($user_data['abbr'], $post['abbr'], $val['job_id']);
//                        $where[] = ' job_id="' . $new_job . '" ';
//                    }
//                    if (!empty($val['estimate_id'])) {
//                        $new_estimate = str_replace($user_data['abbr'], $post['abbr'], $val['estimate_id']);
//                        $where[] = ' estimate_id="' . $new_estimate . '" ';
//                    }
//                    if (!empty($where)) {
//                        DB::sql('UPDATE user_jobs SET ' . implode(',', $where) . ' WHERE id=:id', array(':id' => $val['id']));
//                    }
//                }
//            }
//        }
    }

    /*
     * Send shipment to Fedex
     * @param (array) $result_data: all data from fedex form
     * @return (array) sent results
     */

    public function send_fedex_shipment($result_data) {
        $result_text = array();
        if (!empty($result_data)) {
            require 'Fedex.php';
            $fedex = new Fedex();
            $result_text = $fedex->run_ship($result_data);
        }
        return $result_text;
    }

    public function update_company_abbr($id, $abbr) {
        DB::sql('UPDATE users_company SET abbr = :abbr WHERE id=:id', array(':id' => $id, ':abbr' => $abbr));
    }

    /*
     * Send pickup to Fedex
     * @param (array) $result_data: all data from fedex form
     * @return (array) sent results
     */

    public function send_fedex_pickup($result_data) {
        $result_text = array();
        if (!empty($result_data)) {
            require 'Fedex.php';
            $fedex = new Fedex();
            $result_text = $fedex->run_pickup($result_data);
        }
        return $result_text;
    }

    /*
     * Get Alt phone/email for user
     * @param (int) $uid: user id
     */

    public function item_alt_info($uid) {
        $all = array();
        $email = DB::sql('SELECT `value` FROM user_additional_info WHERE uid = :uid AND `type`="email"', array(':uid' => $uid));
        if (!empty($email)) {
            foreach ($email as $val) {
                $all['email'][] = $val['value'];
            }
        }
        $phone = DB::sql('SELECT `value`,ext, content_type FROM user_additional_info WHERE uid = :uid AND `type`="phone"', array(':uid' => $uid));
        if (!empty($phone)) {
            foreach ($phone as $val) {
                $all['phone'][] = $val;
            }
        }
        return $all;
    }

    /*
     * Calculate Fedex rate for shipment
     * @param (string) $post: imploded to string all form data from Fedex form
     * @return (array) calculate result
     */

    public function calculate_fedex_rate($post) {
        if (!empty($post)) {
            require 'Fedex.php';
            $fedex = new Fedex();

            $arr = explode('&', $post);
            if (!empty($arr)) {
                foreach ($arr as $val) {
                    $values = explode('=', $val);
                    $result_data[$values[0]] = urldecode($values[1]);
                }
            }
            $result_text = $fedex->calculate_rate($result_data);
        }
        return $result_text;
    }

    /*
     * Search Fedex Location
     * @param (string) $post: imploded to string all form data from Fedex form
     * @return (array) finded locations
     */

    public function get_fedex_location($post) {
        if (!empty($post)) {
            require 'Fedex.php';
            $fedex = new Fedex();

            $arr = explode('&', $post);
            if (!empty($arr)) {
                foreach ($arr as $val) {
                    $values = explode('=', $val);
                    $result_data[$values[0]] = urldecode($values[1]);
                }
            }
            $result_text = $fedex->find_location($result_data);
        }
        return $result_text;
    }

    /*
     * Close Fedex shipment
     * @param (int) $id: request id
     * @param (bool) $more_ship_id: main(F) or additional(T) request
     * @return (array) close result
     */

    public function close_shipment($id, $more_ship_id = FALSE) {
        require 'Fedex.php';
        $fedex = new Fedex();
        if (empty($more_ship_id)) {
            $tracking_number = DB::sql_row('SELECT tracking_number FROM requests WHERE id="' . $id . '"');
            $track = $tracking_number['tracking_number'];
            if (!empty($track)) {
                $track_all = explode(',', $track);
                $track_id = substr($track_all[1], strpos($track_all[1], ":") + 1);
                $result = $fedex->close_ship($track_id);
                if (empty($result['err'])) {
                    //Update our DB
                    DB::update('requests')->set(array('tracking_number' => '', 'processed_date' => NULL))->where('id', '=', $id)->execute();
                }
                return $result;
            }
        } else {
            $tracking_number = DB::sql_row('SELECT tracking_number FROM requests_more_sent WHERE req_id="' . $id . '" AND id = :more_ship_id', array(':more_ship_id' => $more_ship_id));
            $track = $tracking_number['tracking_number'];
            if (!empty($track)) {
                $track_all = explode(',', $track);
                $track_id = substr($track_all[1], strpos($track_all[1], ":") + 1);
                $result = $fedex->close_ship($track_id);
                if (empty($result['err'])) {
                    //Update our DB
                    DB::sql_row('DELETE FROM requests_more_sent WHERE req_id="' . $id . '" AND id = :more_ship_id', array(':more_ship_id' => $more_ship_id));
                }
                return $result;
            }
        }
    }

    /*
     * Save template for autofill Fedex form
     * @return (int) inserted id
     */

    public function save_fedex_template() {
        $post = Request::initial()->post();
        $result_data = array();

        if (!empty($post['data'])) {
            foreach ($post['data'] as $val) {
                if ($val['name'] != 'fedex_autofill') {
                    $result_data[$val['name']] = urldecode($val['val']);
                }
            }

            $uid = DB::sql_row('SELECT user_id FROM requests WHERE id=:id', array(':id' => $post['req_id']));
            $r = DB::sql('INSERT INTO fedex_autofill (user_id,title,`type`,`data`) 
                VALUES (:user_id,:title,:type,:data)', array(':user_id' => $uid['user_id'], ':title' => $post['title'], ':type' => $post['type'], ':data' => serialize($result_data)));
            return $r[0];
        }
    }

    /*
     * Get autofill data for fedex form
     * @return (array) saved form data
     */

    public function get_fedex_template($id) {
        $rez = DB::sql_row('SELECT data FROM fedex_autofill WHERE id=:id', array(':id' => $id));
        return unserialize($rez['data']);
    }

    /*
     * Get all autofill templates for Fedex form
     * @param (int) $req_id: request id
     * @return (array) autofill templates for user
     */

    public function fedex_autofill($req_id) {
        $all = array();
        $rez = DB::sql('SELECT title,id,`type` FROM fedex_autofill WHERE user_id=(SELECT user_id FROM requests WHERE id=:id)', array(':id' => $req_id));
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['type']][] = $val;
            }
        }
        return $all;
    }

    /*
     * Remove print request
     * @param $id: request id
     */

    public function remove_print_req($id) {
        $request = DB::sql_row('SELECT * FROM requests WHERE id="' . intval($id) . '"');
        DB::sql('DELETE FROM requests WHERE id="' . intval($id) . '"');

        //add event
        Model::factory('Admin_Event')->add_event('request_removed', $id, serialize($request));
    }

    /* --- Category  --- */

    /*
     * Add print category
     * @return true
     */

    public function add_cat() {
        $post = Request::initial()->post();
        DB::sql('INSERT INTO `' . $this->tb_category . '`(title,abbr,active) VALUES(:title,:abbr,:active)', array(':title' => $post['title'], ':active' => $post['active'], ':abbr' => $post['abbr']));
        return true;
    }

    /*
     * Update print category
     */

    public function save_cat() {
        $id = Request::initial()->param('param2');
        $post = Request::initial()->post();
        DB::sql('UPDATE `' . $this->tb_category . '` SET title=:title, abbr=:abbr, active=:active WHERE id=:id', array(':id' => $id, ':title' => $post['title'], ':active' => $post['active'], ':abbr' => $post['abbr']));
    }

    /*
     * Delete print category
     */

    public function del_cat() {
        $id = Request::initial()->param('param2');
        DB::sql('DELETE FROM `' . $this->tb_category . '` WHERE id = :id', array(':id' => $id));
    }

    /* --- Pockets  --- */

    /*
     * Add print pockets
     * @return true
     */

    public function add_pocket() {
        $post = Request::initial()->post();
        DB::sql('INSERT INTO `' . $this->tb_pocket . '`(title,`type`,active) VALUES(:title,:type,:active)', array(':title' => $post['title'], ':active' => $post['active'], ':type' => $post['type']));
        return true;
    }

    /*
     * Update print pockets
     */

    public function save_pocket() {
        $id = Request::initial()->param('param2');
        $post = Request::initial()->post();
        DB::sql('UPDATE `' . $this->tb_pocket . '` SET title=:title, `type`=:type, active=:active WHERE id=:id', array(':id' => $id, ':title' => $post['title'], ':active' => $post['active'], ':type' => $post['type']));
    }

    /*
     * Delete print pockets
     */

    public function del_pocket() {
        $id = Request::initial()->param('param2');
        DB::sql('DELETE FROM `' . $this->tb_pocket . '` WHERE id = :id', array(':id' => $id));
    }

    /* --- Folding  --- */

    /*
     * Add print folding
     * @return true
     */

    public function add_folding() {
        $post = Request::initial()->post();
        $r = DB::sql('INSERT INTO `' . $this->tb_folding . '`(title, active) VALUES(:title, :active)', array(':title' => $post['title'], ':active' => $post['active']));
        $id = $r[0];
        $this->change_folding_types($id, $post['aval_type']);
        return true;
    }

    /*
     * Update print folding
     */

    public function save_folding() {
        $id = Request::initial()->param('param2');
        $post = Request::initial()->post();
        DB::sql('UPDATE `' . $this->tb_folding . '` SET title=:title, active=:active WHERE id=:id', array(':id' => $id, ':title' => $post['title'], ':active' => $post['active']));
        $this->change_folding_types($id, $post['aval_type']);
    }

    /*
     * Save folding types
     */

    private function change_folding_types($fold_id, $aval_type) {
        DB::sql('DELETE FROM print_folding_types WHERE folding=:id', array(':id' => $fold_id));
        if (!empty($aval_type)) {
            foreach ($aval_type as $v) {
                DB::sql('INSERT INTO print_folding_types (folding, `type`) VALUES (:folding, :type)', array(':folding' => $fold_id, ':type' => $v));
            }
        }
    }

    /*
     * Get folding types
     */

    public function get_folding_types() {
        $id = Request::initial()->param('param2');
        $all = DB::sql('SELECT * FROM print_folding_types WHERE folding=:id', array(':id' => $id));
        $rez = array();
        if (!empty($all)) {
            foreach ($all as $v) {
                $rez[] = $v['type'];
            }
        }
        return $rez;
    }

    /*
     * Delete print folding
     */

    public function del_folding() {
        $id = Request::initial()->param('param2');
        DB::sql('DELETE FROM `' . $this->tb_folding . '` WHERE id = :id', array(':id' => $id));
    }

    /* --- Coatings --- */

    /*
     * Add print coating/finishes
     * @param (string) $table: table name
     */

    public function add_coat($table) {
        $post = Request::initial()->post();
        $r = DB::sql('INSERT INTO `' . $table . '`(title,active,days,abbr) VALUES(:title,:active,:days,:abbr)', array(':title' => $post['title'], ':active' => $post['active'], ':days' => $post['days'], ':abbr' => $post['abbr']));
        $id = $r[0];
        $this->update_coat_prices($id, $table);
        return array('id' => $id);
    }

    /*
     * Edit print coating/finishes
     * @param (int) $id: item id for editing
     * @param (string) $table: table name (use for coating/finishes)
     */

    public function save_coat($id, $table) {
        $post = Request::initial()->post();
        DB::sql('UPDATE `' . $table . '` SET title=:title, active=:active, days=:days, abbr=:abbr WHERE id=:id', array(':id' => $id, ':title' => $post['title'], ':active' => $post['active'], ':days' => $post['days'], ':abbr' => $post['abbr']));
        $this->update_coat_prices($id, $table);
    }

    /*
     * Edit title for print coating (fast editing)
     * @param (int) $id: item id for editing
     */

    public function saveAjaxCoat($id) {
        $title = Request::initial()->post('title');
        DB::sql('UPDATE `' . $this->tb_coat . '` SET title=:title WHERE id=:id', array(':id' => $id, ':title' => $title));
    }

    /*
     * Edit title for print finishes (fast editing)
     * @param (int) $id: item id for editing
     */

    public function saveAjaxFinish($id) {
        $title = Request::initial()->post('title');
        DB::sql('UPDATE `' . $this->tb_finish . '` SET title=:title WHERE id=:id', array(':id' => $id, ':title' => $title));
    }

    /*
     * Edit title for print proof (fast editing)
     * @param (int) $id: item id for editing
     */

    public function saveAjaxProof($id) {
        $title = Request::initial()->post('title');
        DB::sql('UPDATE `print_proof` SET name=:title WHERE id=:id', array(':id' => $id, ':title' => $title));
    }

    /*
     * Update prices for coating/finishes
     * @param (int) $id: item id
     * @param (string) $table: table name (use for coating/finishes)
     */

    private function update_coat_prices($id, $table) {
        $prices = Request::initial()->post('price');
        $counts = Request::initial()->post('count');
        DB::sql('DELETE FROM ' . $table . '_price WHERE coat_id="' . $id . '"');
        if (!empty($counts)) {
            foreach ($counts as $key => $val) {
                $price = str_replace(',', '.', $prices[$key]);
                DB::sql('INSERT INTO ' . $table . '_price (coat_id, `count`, `price`) VALUES ("' . $id . '","' . intval($val) . '","' . $price . '")');
            }
        }
    }

    /*
     * Delete coating/finishes
     * @param (string) $table: table name (use for coating/finishes)
     */

    public function del_coat($table) {
        $id = Request::initial()->param('param2');
        DB::sql('DELETE FROM `' . $table . '` WHERE id = :id', array(':id' => $id));
        DB::sql('DELETE FROM `' . $table . '_price` WHERE coat_id = :id', array(':id' => $id));
    }

    /*
     * Get next coat/finish id
     * @param (int) $id: current id
     * @param (string) $table: table name (use for coating/finishes)
     * @return (int) $id
     */

    public function getNextCoat($id, $table) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM ' . $table . ' ORDER BY id) coat WHERE id>' . $id . ' LIMIT 1');
        return $rez['id'];
    }

    /*
     * Get prev coat/finish id
     * @param (int) $id: current id
     * @param (string) $table: table name (use for coating/finishes)
     * @return (int) $id
     */

    public function getPrevCoat($id, $table) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM ' . $table . ' ORDER BY id DESC) coat WHERE id<' . $id . ' LIMIT 1');
        return $rez['id'];
    }

    /* --- Items  --- */

    /*
     * Add/Edit print item
     * @param (int) $id: id for editing
     * @return true
     */

    //9" x 12" Pocket Folder with 2 pockets 4"
    //9 x 12 PF (w/ 2 of 4" pocket)

    public function save_item($id = NULL) {
        $post = Request::initial()->post();

        $paper_aval = (empty($post['use_paper'])) ? '' : implode(',', $post['use_paper']);
        $paper_def = $post['paper_default'];
        $inks1_aval = (empty($post['inks1_use'])) ? '' : implode(',', $post['inks1_use']);
        $inks1_def = $post['inks1_default'];
        $inks2_aval = (empty($post['inks2_use'])) ? '' : implode(',', $post['inks2_use']);
        $inks2_def = $post['inks2_default'];
        $finishes1_aval = (empty($post['finishes1_aval'])) ? '' : implode(',', $post['finishes1_aval']);
        $finish1_def = $post['finish1_def'];
        $finishes2_aval = (empty($post['finishes2_aval'])) ? '' : implode(',', $post['finishes2_aval']);
        $finish2_def = $post['finish2_def'];
        $coating1_aval = (empty($post['coating1_aval'])) ? '' : implode(',', $post['coating1_aval']);
        $coating1_def = $post['coating1_def'];
        $coating2_aval = (empty($post['coating2_aval'])) ? '' : implode(',', $post['coating2_aval']);
        $coating2_def = $post['coating2_def'];
        $slits_type = (empty($post['slit_type'])) ? '' : $post['slit_type'];
        $proof_aval = (empty($post['proof_aval'])) ? '' : implode(',', $post['proof_aval']);
        $proof_def = $post['proof_default'];
        $item_stick = (empty($post['sticked'])) ? 0 : 1;
        $folding_types = (empty($post['folding_type'])) ? '' : implode(',', $post['folding_type']);

        $panels_width = (empty($post['width'])) ? 0 : $post['width'];
        $panels_height = (empty($post['height'])) ? 0 : $post['height'];
        $have_pockets = (empty($post['have_pockets'])) ? 0 : 1;
        $have_slits = (empty($post['have_slits'])) ? 0 : 1;

        $title = $abbr = '';
        //Generate Product title + abbr
        if (!empty($panels_width) && !empty($panels_height)) {
            $title = $panels_width . '" x ' . $panels_height . '" ';
            $abbr = intval($panels_width) . ' x ' . intval($panels_height) . ' ';
        }
        $category = $this->get_cat($post['category']);
        if (!empty($category)) {
            if ($category['title'] == 'Folder') {
                $category['title'] = 'Pocket Folder';
            }
            $title.= $category['title'];
            $abbr .= $category['abbr'];
        }
        if (!empty($post['panels_count'])) {
            $title.= ' with ' . $post['panels_count'];
            $title.=($post['panels_count'] == 1) ? ' pocket ' : ' pockets ';

            $p_height = '';
            if (!empty($post['pack_height'])) {
                foreach ($post['pack_height'] as $v) {
                    foreach ($v as $h) {
                        if (!empty($h)) {
                            $p_height = $h . '"';
                        }
                    }
                }
                $title .= $p_height;
            }
            $abbr .= ' (w/ ' . $post['panels_count'] . ' of ' . $p_height . ' pocket)';
        }

        //add/update
        if (!empty($id)) {
            DB::sql('UPDATE `' . $this->tb_item . '`
            SET title=:title, abbr=:abbr, width=:width, height=:height, active=:active, category_id=:category, paper_aval=:paper_aval, paper_def=:paper_def, inks1_aval=:inks1_aval, inks1_def=:inks1_def, inks2_aval=:inks2_aval, inks2_def=:inks2_def, 
            coating1_aval=:coating1_aval, coating1_def=:coating1_def, coating2_aval=:coating2_aval, coating2_def=:coating2_def, finishes1_aval=:finishes1_aval, finishes1_def=:finish1_def, finishes2_aval=:finishes2_aval, finishes2_def=:finish2_def, min_price=:min_price, min_count=:min_count, stick=:stick, slits_type=:slits_type,
            proof_aval=:proof_aval, proof_def=:proof_def, folding_types=:folding_types, folding_orient=:folding_orient, panel_width=:panel_width, panel_height=:panel_height, panel_count=:panel_count
            WHERE id=:id', array(':id' => $id, ':title' => $title, ':active' => $post['active'], ':category' => $post['category'],
                ':paper_aval' => $paper_aval, ':paper_def' => $paper_def, ':inks1_aval' => $inks1_aval, ':inks1_def' => $inks1_def, ':inks2_aval' => $inks2_aval, ':inks2_def' => $inks2_def, ':finishes1_aval' => $finishes1_aval, ':finishes2_aval' => $finishes2_aval, ':coating1_aval' => $coating1_aval, ':coating2_aval' => $coating2_aval,
                ':finish1_def' => $finish1_def, ':finish2_def' => $finish2_def, ':coating1_def' => $coating1_def, ':coating2_def' => $coating2_def, ':min_price' => $post['min_price'], ':min_count' => $post['min_count'], ':abbr' => $abbr, ':stick' => $item_stick, ':slits_type' => $slits_type,
                ':proof_aval' => $proof_aval, ':proof_def' => $proof_def, ':folding_types' => $folding_types, ':folding_orient' => $post['fold_orient'], ':panel_width' => $panels_width, ':panel_height' => $panels_height,
                ':panel_count' => $post['panels_count'], ':width' => $post['width'], ':height' => $post['height']
            ));
        } else {
            $r = DB::sql('INSERT INTO `' . $this->tb_item . '` (width, height, title, abbr, active, category_id, paper_aval, paper_def, inks1_aval, inks1_def, inks2_aval, inks2_def, finishes1_aval, finishes1_def, finishes2_aval, finishes2_def, coating1_aval, coating1_def, coating2_aval, coating2_def, min_price, min_count, stick, slits_type, proof_aval, proof_def, folding_types, folding_orient, panel_width, panel_height, panel_count) 
                VALUES (:width, :height, :title, :abbr, :active, :category_id, :paper_aval, :paper_def, :inks1_aval, :inks1_def, :inks2_aval, :inks2_def, :finishes1_aval, :finishes1_def, :finishes2_aval, :finishes2_def, :coating1_aval, :coating1_def, :coating2_aval, :coating2_def, :min_price, :min_count, :stick, :slits_type, :proof_aval, :proof_def, :folding_types, :folding_orient, :panel_width, :panel_height, :panel_count)', array(
                        ':title' => $title, ':abbr' => $abbr, ':active' => $post['active'], ':category_id' => $post['category'], ':paper_aval' => $paper_aval,
                        ':paper_def' => $paper_def, ':inks1_aval' => $inks1_aval, ':inks1_def' => $inks1_def, ':inks2_aval' => $inks2_aval, ':inks2_def' => $inks2_def, ':finishes1_aval' => $finishes1_aval, ':finishes1_def' => $finish1_def, ':finishes2_aval' => $finishes2_aval, ':finishes2_def' => $finish2_def, ':coating1_aval' => $coating1_aval, ':coating2_aval' => $coating2_aval,
                        ':coating1_def' => $coating1_def, ':coating2_def' => $coating2_def, ':min_count' => $post['min_count'], ':min_price' => $post['min_price'], ':stick' => $item_stick, ':slits_type' => $slits_type,
                        ':proof_aval' => $proof_aval, ':proof_def' => $proof_def, ':folding_types' => $folding_types, ':folding_orient' => $post['fold_orient'], ':panel_width' => $panels_width, ':panel_height' => $panels_height,
                        ':panel_count' => $post['panels_count'], ':width' => $post['width'], ':height' => $post['height']
            ));
            $id = $r[0];
        }

        //Pockets
        DB::sql('DELETE FROM print_item_pockets WHERE item_id=:item_id', array(':item_id' => $id));
        if (!empty($have_pockets) && !empty($post['panels_count']) && !empty($post['pocket_count'])) {
            foreach ($post['pocket_count'] as $key => $val) {
                if (!empty($val)) {
                    $size = array('width' => $post['pocket_width'][$key], 'height' => $post['pocket_height'][$key]);
                    if ($post['pack_type'][$key] != 'standard') {
                        $size = array_merge($size, array('depth' => $post['pocket_depth'][$key]));
                    }
                    DB::sql('INSERT INTO print_item_pockets (item_id,`type`, page_num, counts, position, size, glue) 
                        VALUES (:item_id,:type,:page_num,:counts,:position,:size,:glue)', array(':item_id' => $id, ':type' => $post['pack_type'][$key], ':page_num' => $key, ':counts' => $val,
                        ':position' => $post['pocket_position'][$key], ':size' => serialize($size), ':glue' => $post['glue_type'][$key]));
                }
            }
        }

        //Slits
        DB::sql('DELETE FROM print_item_slits WHERE item_id=:item_id', array(':item_id' => $id));
        if (!empty($have_slits) && !empty($post['panels_count']) && !empty($post['slit_type'])) {

            if ($post['slit_type'] == 'single' && !empty($post['single_position'])) {
                //single
                $pos = explode('-', $post['single_position']);
                $slit_def = (empty($post['slits_default']['any'][1])) ? '' : $post['slits_default']['any'][1];
                $slit_aval = (empty($post['slits_aval']['any'][1])) ? '' : serialize($post['slits_aval']['any'][1]);
                DB::sql('INSERT INTO print_item_slits (`type`,item_id,slit_aval,slit_def,page_num) VALUES (:type, :item_id, :slit_aval, :slit_def, :page_num)', array(
                    ':type' => $pos[0], ':item_id' => $id, ':slit_aval' => $slit_aval, ':slit_def' => $slit_def, ':page_num' => $pos[1]
                ));
            } elseif ($post['slit_type'] == 'multiple' && !empty($post['multiple'])) {
                //multiple
                foreach ($post['multiple'] as $key => $val) {
                    if (!empty($val)) {
                        foreach ($val as $k => $v) {
                            $slit_def = (empty($post['slits_default'][$key][$k])) ? '' : $post['slits_default'][$key][$k];
                            $slit_aval = (empty($post['slits_aval'][$key][$k])) ? '' : serialize($post['slits_aval'][$key][$k]);
                            DB::sql('INSERT INTO print_item_slits (`type`,item_id,slit_aval,slit_def,page_num) VALUES (:type, :item_id, :slit_aval, :slit_def, :page_num)', array(
                                ':type' => $key, ':item_id' => $id, ':slit_aval' => $slit_aval, ':slit_def' => $slit_def, ':page_num' => $k
                            ));
                        }
                    }
                }
            }
        }

        //Item Stick
        if (!empty($item_stick)) {
            $stick_paper_aval = (empty($post['stick_use_paper'])) ? '' : implode(',', $post['stick_use_paper']);
            $stick_paper_def = $post['stick_paper_default'];
            $stick_inks_aval = (empty($post['stick_inks_use'])) ? '' : implode(',', $post['stick_inks_use']);
            $stick_inks_def = $post['stick_inks_default'];
            $stick_finish_aval = (empty($post['stick_finishes_aval'])) ? '' : implode(',', $post['stick_finishes_aval']);
            $stick_finish_def = $post['stick_finish_def'];

            $stick_rez = DB::sql('SELECT 1 FROM ' . $this->tb_stick . ' WHERE item_id=:item_id', array(':item_id' => $id));
            if (!empty($stick_rez)) {
                DB::sql('UPDATE ' . $this->tb_stick . ' SET 
                    paper_aval=:paper_aval, paper_def=:paper_def, inks_aval=:inks_aval, inks_def=:inks_def, finishes_aval=:finishes_aval, 
                    finishes_def=:finishes_def, flat_width=:flat_width, flat_height=:flat_height, finish_width=:finish_width, finish_height=:finish_height, 
                    flat_description=:flat_description, finish_description=:finish_description WHERE item_id=:item_id', array(
                    ':item_id' => $id, ':paper_aval' => $stick_paper_aval, ':paper_def' => $stick_paper_def, ':inks_aval' => $stick_inks_aval, ':inks_def' => $stick_inks_def,
                    ':finishes_aval' => $stick_finish_aval, ':finishes_def' => $stick_finish_def, ':flat_width' => $post['stick_flat_width'], ':flat_height' => $post['stick_flat_height'],
                    ':finish_width' => $post['stick_finish_width'], ':finish_height' => $post['stick_finish_height'], ':flat_description' => $post['stick_flat_description'], ':finish_description' => $post['stick_finish_description']
                ));
            } else {
                DB::sql('INSERT INTO ' . $this->tb_stick . ' (item_id, paper_aval, paper_def, inks_aval, inks_def, finishes_aval, finishes_def, flat_width, flat_height, finish_width, finish_height, flat_description, finish_description) 
                    VALUES (:item_id, :paper_aval, :paper_def, :inks_aval, :inks_def, :finishes_aval, :finishes_def, :flat_width, :flat_height, :finish_width, :finish_height, :flat_description, :finish_description)', array(
                    ':item_id' => $id, ':paper_aval' => $stick_paper_aval, ':paper_def' => $stick_paper_def, ':inks_aval' => $stick_inks_aval, ':inks_def' => $stick_inks_def,
                    ':finishes_aval' => $stick_finish_aval, ':finishes_def' => $stick_finish_def, ':flat_width' => $post['stick_flat_width'], ':flat_height' => $post['stick_flat_height'],
                    ':finish_width' => $post['stick_finish_width'], ':finish_height' => $post['stick_finish_height'], ':flat_description' => $post['stick_flat_description'], ':finish_description' => $post['stick_finish_description']
                ));
            }
        } else {
            DB::sql('DELETE FROM ' . $this->tb_stick . ' WHERE item_id=:item_id', array(':item_id' => $id));
        }

        $this->update_item_prices($id);
//        $this->update_item_dimentions($id);
        $this->update_template($id);
        return true;
    }

    /*
     * Update item images
     * @param (int) $id: item id
     */

    private function update_template($id) {
        if (is_file($_FILES['template_psd']['tmp_name'])) {
            copy($_FILES['template_psd']['tmp_name'], APPPATH . 'files/print/psd/' . $id . '.psd');
        }
        if (is_file($_FILES['template_preview']['tmp_name'])) {
            $img = Image::factory($_FILES['template_preview']['tmp_name']);
            $img->resize(132, '', Image::WIDTH)->save(APPPATH . 'files/print/preview/' . $id . '.jpg', 95);
        }
        if (is_file($_FILES['template_active_preview']['tmp_name'])) {
            $img = Image::factory($_FILES['template_active_preview']['tmp_name']);
            $img->resize(132, '', Image::WIDTH)->save(APPPATH . 'files/print/active_preview/' . $id . '.jpg', 95);
        }
        if (is_file($_FILES['template_view']['tmp_name'])) {
            $img = Image::factory($_FILES['template_view']['tmp_name']);
            $img->resize(600, '', Image::WIDTH)->save(APPPATH . 'files/print/view/' . $id . '.jpg', 95);
        }
    }

    /*
     * Delete print item
     */

    public function del_item() {
        $id = Request::initial()->param('param2');
        DB::sql('DELETE FROM `' . $this->tb_item . '` WHERE id = :id', array(':id' => $id));
        @unlink(APPPATH . '/files/print/view/' . $id . '.jpg');
        @unlink(APPPATH . '/files/print/active_preview/' . $id . '.jpg');
        @unlink(APPPATH . '/files/print/preview/' . $id . '.jpg');
    }

    /*
     * Get next print item id
     * @param (int) $id: current item id
     * @return (int) $id
     */

    public function getNextPrintItem($id) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM ' . $this->tb_item . ' ORDER BY id) cat WHERE id>' . $id . ' LIMIT 1');
        return $rez['id'];
    }

    /*
     * Get prev print item id
     * @param (int) $id: current item id
     * @return (int) $id
     */

    public function getPrevPrintItem($id) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM ' . $this->tb_item . ' ORDER BY id DESC) cat WHERE id<' . $id . ' LIMIT 1');
        return $rez['id'];
    }

    /*
     * Update print item price
     * @param (int) $id: item id
     */

    private function update_item_prices($id) {
        $prices = Request::initial()->post('price');
        $counts = Request::initial()->post('count');
        DB::sql('DELETE FROM print_item_price WHERE item_id="' . $id . '"');
        if (!empty($counts)) {
            foreach ($counts as $key => $val) {
                $price = str_replace(',', '.', $prices[$key]);
                DB::sql('INSERT INTO print_item_price (item_id, `count`, `price`) VALUES ("' . $id . '","' . intval($val) . '","' . $price . '")');
            }
        }
    }

    /*
     * Update print item dimentions
     * @param (int) $id: item id
     */

//    private function update_item_dimentions($id) {
//        $post = Request::initial()->post();
//        DB::sql('DELETE FROM print_item_dimention WHERE item_id="' . $id . '"');
//        if (!empty($post['dim_name'])) {
//            foreach ($post['dim_name'] as $key => $val) {
//                DB::sql('INSERT INTO print_item_dimention (item_id, name, description, abbr, width, height) 
//                    VALUES (:item_id, :name, :description, :abbr, :width, :height)', array(':item_id' => $id, ':name' => $val, ':description' => $post['dim_descr'][$key], ':abbr' => $post['dim_abbr'][$key], ':width' => $post['dim_width'][$key], ':height' => $post['dim_height'][$key]));
//            }
//        }
//    }

    /* --- Shipping --- */

    /*
     * Add shipping method
     * @return true
     */

    public function add_shipp() {
        $title = Request::initial()->post('title');
        $active = Request::initial()->post('active');
        $r = DB::sql('INSERT INTO `' . $this->tb_shipp . '`(title,active) VALUES(:title,:active)', array(':title' => $title, ':active' => $active));
        $id = $r[0];
        $this->update_shipp_prices($id);
        return true;
    }

    /*
     * Update shipping data
     * @param (int) $id: shipping id
     */

    public function save_shipp($id) {
        $title = Request::initial()->post('title');
        $active = Request::initial()->post('active');
        DB::sql('UPDATE `' . $this->tb_shipp . '` SET title=:title, active=:active WHERE id=:id', array(':id' => $id, ':title' => $title, ':active' => $active));
        $this->update_shipp_prices($id);
    }

    /*
     * Update shipping prices data
     * @param (int) $id: shipping id
     */

    private function update_shipp_prices($id) {
        $prices = Request::initial()->post('price');
        $counts = Request::initial()->post('count');
        DB::sql('DELETE FROM ' . $this->tb_shipp_price . ' WHERE shipp_id="' . $id . '"');
        if (!empty($counts)) {
            foreach ($counts as $key => $val) {
                $price = str_replace(',', '.', $prices[$key]);
                DB::sql('INSERT INTO ' . $this->tb_shipp_price . ' (shipp_id, `count`, `price`) VALUES ("' . $id . '","' . intval($val) . '","' . $price . '")');
            }
        }
    }

    /*
     * Delete shipping
     */

    public function del_shipp() {
        $id = Request::initial()->param('param2');
        DB::sql('DELETE FROM `' . $this->tb_shipp . '` WHERE id = :id', array(':id' => $id));
        DB::sql('DELETE FROM `' . $this->tb_shipp_price . '` WHERE shipp_id = :id', array(':id' => $id));
    }

    /*
     * Get next shipping id
     * @param (int) $id: current shipping id
     * @return (int) shipping id
     */

    public function getNextShipp($id) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM `' . $this->tb_shipp . '` ORDER BY id) cat WHERE id>' . $id . ' LIMIT 1');
        return $rez['id'];
    }

    /*
     * Get prev shipping id
     * @param (int) $id: current shipping id
     * @return (int) shipping id
     */

    public function getPrevShipp($id) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM `' . $this->tb_shipp . '` ORDER BY id DESC) cat WHERE id<' . $id . ' LIMIT 1');
        return $rez['id'];
    }

    /* --- Settings --- */

    /*
     * Get print settings
     * @return (array) all settings
     */

    public function get_settings() {
        return DB::get($this->tb_setting);
    }

    /*
     * Save one print setting
     * @param (array) $elem: setting data
     */

    public function save_setting($elem) {
        $new_val = Request::initial()->post($elem['key']);
        DB::sql('UPDATE ' . $this->tb_setting . ' SET `val`="' . $new_val . '" WHERE `key`="' . $elem['key'] . '"');
    }

    /*
     * Get next request item 
     * @param (int) $id: current id
     * @param (int) $proc: process(1) or all(0)
     * @return (int) request id
     */

    public function getNextItem($id, $proc = 0) {
        $procced = (!empty($proc)) ? 'WHERE `processed_date` IS NULL' : '';
        $rez = DB::query(Database::SELECT, 'SELECT id FROM (SELECT id FROM requests ' . $procced . ' ORDER BY id DESC) requests WHERE id<' . $id . ' LIMIT 1', array(':id' => $id))->execute()->offsetGet(0);
        return $rez['id'];
    }

    /*
     * Get prev request item 
     * @param (int) $id: current id
     * @param (int) $proc: process(1) or all(0)
     * @return (int) request id
     */

    public function getPrevItem($id, $proc = 0) {
        $procced = (!empty($proc)) ? 'WHERE `processed_date` IS NULL' : '';
        $rez = DB::query(Database::SELECT, 'SELECT id FROM (SELECT id FROM requests ' . $procced . ' ORDER BY id) requests WHERE id>' . $id . ' LIMIT 1')->execute()->offsetGet(0);
        return $rez['id'];
    }

    /* --- INKS --- */

    /*
     * Add INK
     * @return (array) INK id
     */

    public function addInks() {
        $post = Request::initial()->post();
        $r = DB::sql('INSERT INTO print_inks (name,description,price) VALUES (:name,:description,:price)', array(
                    ':name' => $post['name'], ':description' => $post['description'], ':price' => $post['price']));
        $id = $r[0];
        return array('id' => $id);
    }

    /*
     * Update INK
     * @param (int) $id: ink id
     */

    public function saveInk($id) {
        $post = Request::initial()->post();
        DB::sql('UPDATE print_inks SET name=:name,description=:description,price=:price WHERE id=:id', array(
            ':name' => $post['name'], ':description' => $post['description'], ':price' => $post['price'], ':id' => $id));
    }

    /*
     * Update INK title (fast edit)
     * @param (int) $id: ink id
     */

    public function saveAjaxInk($id) {
        $post = Request::initial()->post();
        DB::sql('UPDATE print_inks SET name=:name WHERE id=:id', array(
            ':name' => $post['name'], ':id' => $id));
    }

    /*
     * Delete INK
     * @param (int) $id: ink id
     */

    public function delInk($id) {
        DB::sql('DELETE FROM print_inks WHERE id=:id', array(':id' => $id));
    }

    /* --- Slits --- */

    /*
     * Add Slits
     * @return (array) slit id
     */

    public function addSlits() {
        $post = Request::initial()->post();
        $r = DB::sql('INSERT INTO print_slits (name,description,type) VALUES (:name,:description,:type)', array(
                    ':name' => $post['name'], ':description' => $post['description'], ':type' => $post['type']));
        $id = $r[0];
        $this->uploadSlitPreview($id);
        return array('id' => $id);
    }

    /*
     * Update Slits
     * @param (int) $id: slit id
     */

    public function saveSlit($id) {
        $post = Request::initial()->post();
        DB::sql('UPDATE print_slits SET name=:name,description=:description,type=:type WHERE id=:id', array(
            ':name' => $post['name'], ':description' => $post['description'], ':id' => $id, ':type' => $post['type']));
        $this->uploadSlitPreview($id);
    }

    /*
     * Delete Slits
     * @param (int) $id: slit id
     */

    public function delSlit($id) {
        @unlink(APPPATH . 'files/print/slits/' . $id . '.jpg');
        DB::sql('DELETE FROM print_slits WHERE id=:id', array(':id' => $id));
    }

    /*
     * Update Slits image
     * @param (int) $id: slit id
     */

    public function uploadSlitPreview($id) {
        if (is_file($_FILES['preview']['tmp_name'])) {
            $img = Image::factory($_FILES['preview']['tmp_name']);
            $img->resize(244, 198, Image::INVERSE)->crop(244, 198)->save(APPPATH . 'files/print/slits/' . $id . '.jpg', 95);
        }
    }

    /* --- Proof --- */

    /*
     * Add Proof
     * @return (array) slit id
     */

    public function addProof() {
        $post = Request::initial()->post();
        $r = DB::sql('INSERT INTO print_proof (name,description,price) VALUES (:name,:description,:price)', array(
                    ':name' => $post['name'], ':description' => $post['description'], ':price' => $post['price']));
        $id = $r[0];
        $this->uploadProofPreview($id);
        return array('id' => $id);
    }

    /*
     * Update Proof
     * @param (int) $id: proof id
     */

    public function saveProof($id) {
        $post = Request::initial()->post();
        DB::sql('UPDATE print_proof SET name=:name,description=:description, price=:price WHERE id=:id', array(
            ':name' => $post['name'], ':description' => $post['description'], ':id' => $id, ':price' => $post['price']));
        $this->uploadSlitPreview($id);
    }

    /*
     * Delete Proof
     * @param (int) $id: proof id
     */

    public function delProof($id) {
        @unlink(APPPATH . 'files/print/proof/' . $id . '.jpg');
        DB::sql('DELETE FROM print_proof WHERE id=:id', array(':id' => $id));
    }

    /*
     * Update Proof image
     * @param (int) $id: proof id
     */

    public function uploadProofPreview($id) {
        if (is_file($_FILES['preview']['tmp_name'])) {
            $img = Image::factory($_FILES['preview']['tmp_name']);
            $img->resize(244, 198, Image::INVERSE)->crop(244, 198)->save(APPPATH . 'files/print/proof/' . $id . '.jpg', 95);
        }
    }

    /* --- Paper --- */

    /*
     * Add Print paper
     * @return (array) paper id
     */

    public function addPaper() {
        $post = Request::initial()->post();
        $r = DB::sql('INSERT INTO print_papers (name,description) VALUES (:name,:description)', array(
                    ':name' => $post['name'], ':description' => $post['description']));
        $id = $r[0];
        $this->updatePaperPrices($id);
        return array('id' => $id);
    }

    /*
     * Update Print paper
     * @param (int) $id: paper id
     */

    public function savePaper($id) {
        $post = Request::initial()->post();
        DB::sql('UPDATE print_papers SET name=:name,description=:description WHERE id=:id', array(
            ':name' => $post['name'], ':description' => $post['description'], ':id' => $id));
        $this->updatePaperPrices($id);
    }

    /*
     * Update Print paper price
     * @param (int) $id: paper id
     */

    private function updatePaperPrices($id) {
        $prices = Request::initial()->post('price');
        $counts = Request::initial()->post('count');
        DB::sql('DELETE FROM print_papers_price WHERE paper_id="' . $id . '"');
        if (!empty($counts)) {
            foreach ($counts as $key => $val) {
                $price = str_replace(',', '.', $prices[$key]);
                DB::sql('INSERT INTO print_papers_price (paper_id, `count`, `price`) VALUES ("' . $id . '","' . intval($val) . '","' . $price . '")');
            }
        }
    }

    /*
     * Update Print paper title (fast edit)
     * @param (int) $id: paper id
     */

    public function savePaperAjax($id) {
        $post = Request::initial()->post();
        DB::sql('UPDATE print_papers SET name=:name WHERE id=:id', array(':name' => $post['name'], ':id' => $id));
    }

    /*
     * Delete Print paper
     * @param (int) $id: paper id
     */

    public function delPaper($id) {
        DB::sql('DELETE FROM print_papers WHERE id=:id', array(':id' => $id));
    }

    /* ------------ Requests ------------ */

    /*
     * Get processed Print requests
     * @return (array) all requests
     */

    public function get_process_req() {
        $rez = DB::query(Database::SELECT, 'SELECT requests.*, requests.industry req_industry, DATE_FORMAT(requests.request_date, "%m-%d-%Y") request_date, users.first_name, users.last_name, users_company.company, users_company.duplicate, users.email, users.phone_ext, users.phone, "-" processed_date, industry_send, complete_address
            FROM requests 
            LEFT JOIN users ON users.id=requests.user_id
            LEFT JOIN users_company ON users_company.id=requests.company_id
            WHERE requests.`processed_date` IS NULL GROUP BY company_id ORDER BY requests.id DESC')->execute()->as_array();
        return $rez;
    }

    /*
     * Get requests with tasks for me
     * @param (int) $type: 1=my tasks, 2=assigned tasks
     * @return (array) request data
     */

    public function get_tasks_req($type) {
        $rez = array();
        $cookies = new Cookie();
        $this->admin = $cookies->get('admin_user');
        if (!empty($this->admin)) {
            $admin = unserialize($this->admin);
            $admin_id = $admin['id'];
        }
        if ($type == 2) {
            $all_req = DB::sql('SELECT request_notes.request_id
            FROM request_note_required 
            LEFT JOIN request_notes ON request_notes.id=request_note_required.note_id
            WHERE from_uid=:uid AND request_notes.removed=0 AND status=0', array(':uid' => $admin_id));
        } else {
            $all_req = DB::sql('SELECT request_note_required.*, request_notes.request_id
            FROM request_note_required 
            LEFT JOIN request_notes ON request_notes.id=request_note_required.note_id
            WHERE request_note_required.for_uid=:uid AND request_notes.removed=0 AND request_note_required.status=0', array(':uid' => $admin_id));
        }

        if (!empty($all_req)) {
            foreach ($all_req as $val) {
                $req_arr[$val['request_id']] = $val['request_id'];
            }
            $rez = DB::sql('SELECT requests.*, requests.industry req_industry, DATE_FORMAT(requests.request_date, "%m-%d-%Y") request_date, users.first_name, users.last_name, users_company.company, users_company.duplicate, users.email, users.phone, users.phone_ext, "-" processed_date, industry_send, complete_address
            FROM requests 
            LEFT JOIN users ON users.id=requests.user_id
            LEFT JOIN users_company ON users_company.id=requests.company_id
            WHERE requests.id IN (' . implode(',', $req_arr) . ') GROUP BY company_id ORDER BY requests.id DESC');
        }
        return $rez;
    }

    /*
     * Search print request
     * @param (string) $word: search words
     * @param (int) $for_page: items for page
     * @param (int) $page: page view
     * @param (bool) $new: return processed(T) or all(F) requests
     * @param (string) $fields: imploded fields for search
     * @param (date) $date: date for search
     * @return (array) all requests
     */

    public function search_print($word, $for_page, $page, $new, $fields = NULL, $date = NULL) {
        $post = Request::initial()->post();

        $date_search = '';
        $search = array();
        if (!empty($fields)) {
            $post['fields'] = $fields;
        }
        if (!empty($date)) {
            $post['date'] = $date;
        }

        $disable_advanced_search = Session::instance()->get('disable_advanced_search');
        if (!empty($post['fields']) || !empty($disable_advanced_search)) {

            //disable advanced search
            if (!empty($disable_advanced_search)) {
                //all fields string... sorry.. gavnokod =(
                $post['fields'] = 'print_field2=company&print_field3=users.name&print_field4=users.phone&print_field5=users.email&print_field6=users.position&print_field7=users.street&print_field8=users.city&print_field9=users.state&print_field10=users.zipcode&print_field12=requests.industry&print_field13=conversations&print_field16=search_keyword&print_field17=user_ip&print_field18=operating_sys&print_field19=graphics_app&print_field20=ref_source&print_field21=other_source&print_field22=tracking_number&print_field23=job_id&print_field24=notes&print_field25=pay_info';
                //remove date criteria
                $post['date'] = '';
            }

            $arr = explode('&', $post['fields']);
            foreach ($arr as $val) {
                $values = explode('=', $val);
                $field = urldecode($values[1]);
                if ($field == 'users.name') {
                    $search[] = ' users.first_name LIKE "%' . $word . '%" OR users.last_name LIKE "%' . $word . '%" ';
                    $search[] = ' users.first_name LIKE "%' . $word . '%" OR users.last_name LIKE "%' . $word . '%" ';
                    $search[] = ' CONCAT(users.first_name," ",users.last_name) LIKE "%' . $word . '%" ';
                } elseif ($field == 'users.phone') {
                    $search[] = ' REPLACE(users.phone,"-","") LIKE "%' . str_replace('-', '', $word) . '%"';
                    $search[] = ' REPLACE(users.phone_ext,"-","") LIKE "%' . str_replace('-', '', $word) . '%"';

                    //search by additional phones
                    $additional = DB::sql('SELECT uid FROM user_additional_info WHERE REPLACE(`value`,"-","") LIKE "%' . str_replace('-', '', $word) . '%" AND type="phone"');
                    if (!empty($additional)) {
                        foreach ($additional as $val) {
                            $all_uid[] = $val['uid'];
                        }
                        $search[] = ' requests.user_id IN (' . implode(',', $all_uid) . ') ';
                    }
                } elseif ($field == 'users.email') {
                    $search[] = ' ' . $field . ' LIKE "%' . (string) $word . '%" OR ' . $field . ' LIKE "%' . (string) $word . '%" ';

                    //search by additional email
                    $additional = DB::sql('SELECT uid FROM user_additional_info WHERE `value` LIKE "%' . $word . '%" AND type="email"');
                    if (!empty($additional)) {
                        foreach ($additional as $val) {
                            $all_uid[] = $val['uid'];
                        }
                        $search[] = ' requests.user_id IN (' . implode(',', $all_uid) . ') ';
                    }
                } elseif ($field == 'notes' && !empty($word)) {
                    $rezult = DB::sql('SELECT * FROM request_notes WHERE `text` LIKE "%' . $word . '%" AND removed=0');
                    if (!empty($rezult)) {
                        $note_id = array();
                        foreach ($rezult as $v) {
                            $note_id[] = $v['request_id'];
                        }
                        $search[] = ' requests.id IN (' . implode(',', $note_id) . ') ';
                    } else {
                        $search[] = ' 1=0 ';
                    }
                } elseif ($field == 'job_id' && !empty($word)) {
                    $rezult = DB::sql('SELECT user_id FROM user_jobs WHERE job_id LIKE "%' . $word . '%" OR estimate_id LIKE "%' . $word . '%"');
                    if (!empty($rezult)) {
                        $users_id = array();
                        foreach ($rezult as $v) {
                            $users_id[] = $v['user_id'];
                        }
                        $search[] = ' requests.user_id IN (' . implode(',', $users_id) . ') ';
                    } else {
                        $search[] = ' 1=0 ';
                    }
                } elseif ($field == 'pay_info' && !empty($word)) {
                    //shipp credit card data
                    $rezult = DB::sql('SELECT user_id FROM credit_card WHERE title LIKE "%' . $word . '%" OR full_card_name LIKE "%' . $word . '%"
                         OR full_user_name LIKE "%' . $word . '%"');
                    if (!empty($rezult)) {
                        $users_id = array();
                        foreach ($rezult as $v) {
                            $users_id[] = $v['user_id'];
                        }
                        $search[] = ' requests.user_id IN (' . implode(',', $users_id) . ') ';
                    } else {
                        $search[] = ' 1=0 ';
                    }
                    //billing credit card data
                    $rezult = DB::sql('SELECT user_id FROM credit_card_billing WHERE title LIKE "%' . $word . '%" OR first_name LIKE "%' . $word . '%"
                         OR last_name LIKE "%' . $word . '%" OR address LIKE "%' . $word . '%" OR email LIKE "%' . $word . '%" OR company LIKE "%' . $word . '%"');
                    if (!empty($rezult)) {
                        $users_id = array();
                        foreach ($rezult as $v) {
                            $users_id[] = $v['user_id'];
                        }
                        $search[] = ' requests.user_id IN (' . implode(',', $users_id) . ') ';
                    } else {
                        $search[] = ' 1=0 ';
                    }
                } elseif ($field == 'company') {

                    $rezult = DB::sql('SELECT id FROM users_company WHERE company LIKE "%' . $word . '%"');
                    if (!empty($rezult)) {
                        $comp_id = array();
                        foreach ($rezult as $v) {
                            $comp_id[] = $v['id'];
                        }
                        $search[] = ' requests.company_id IN (' . implode(',', $comp_id) . ') ';
                    }
                } else {
                    $search[] = ' ' . $field . ' LIKE "%' . (string) $word . '%" OR ' . $field . ' LIKE "%' . (string) $word . '%" ';
                }
            }
            if (!empty($post['date'])) {
                $date = explode('-', $post['date']);
                if (!empty($date[1])) {
                    $date_search = ' AND UNIX_TIMESTAMP(request_date) >= UNIX_TIMESTAMP(STR_TO_DATE("' . trim($date[0]) . '","%m/%d/%Y")) AND UNIX_TIMESTAMP(request_date) <= UNIX_TIMESTAMP(STR_TO_DATE("' . trim($date[1]) . '","%m/%d/%Y")) ';
                }
            }

            $where = '';
            if (!empty($search)) {
                $where = ' WHERE (' . implode('OR', $search) . ') ';
            }

            if ($new) {
                if (!empty($word)) {
                    if (!empty($where)) {
                        $where.=' AND ';
                    }
                    return DB::query(Database::SELECT, 'SELECT SQL_CALC_FOUND_ROWS requests.id, requests.order_data, requests.user_id, requests.company_id, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, industry_send, requests.industry req_industry,
                        company, users.industry, first_name, last_name, phone, phone_ext, email, complete_address, "-" processed_date, tracking_number, users_company.company, users_company.duplicate,
                        FROM requests 
                        LEFT JOIN users ON users.id=requests.user_id
                        LEFT JOIN users_company ON users_company.id=requests.company_id
                        ' . $where . '  `processed_date` IS NULL ' . $date_search . '
                        GROUP BY company_id ORDER BY requests.id DESC LIMIT 0, 500')->execute()->as_array();
                } else {
                    return DB::query(Database::SELECT, 'SELECT SQL_CALC_FOUND_ROWS requests.id, requests.user_id, requests.company_id, requests.order_data, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, users_company.company, users_company.duplicate, first_name, industry_send, requests.industry req_industry,
                        last_name, phone, phone_ext, email, "-" processed_date, tracking_number, complete_address
                        FROM requests 
                        LEFT JOIN users ON users.id=requests.user_id
                        LEFT JOIN users_company ON users_company.id=requests.company_id
                        WHERE `processed_date` IS NULL ' . $date_search . ' 
                        GROUP BY company_id ORDER BY requests.id DESC')->execute()->as_array();
                }
            } else {

                $begin = $page * $for_page;
                if (!empty($word)) {
                    $word = trim(htmlspecialchars($word));
                    return DB::sql('SELECT SQL_CALC_FOUND_ROWS requests.id, requests.order_data, requests.user_id, requests.company_id, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, company, 
                        requests.industry req_industry, first_name, last_name, phone, phone_ext, email, industry_send, DATE_FORMAT(processed_date, "%m-%d-%Y") processed_date, tracking_number, complete_address, users_company.duplicate
                FROM requests 
                LEFT JOIN users ON users.id=requests.user_id
                LEFT JOIN users_company ON users_company.id=requests.company_id
                ' . $where . ' ' . $date_search . '
                GROUP BY company_id ORDER BY id DESC LIMIT ' . $begin . ', ' . $for_page);
                } elseif (!empty($date_search)) {
                    return DB::query(Database::SELECT, 'SELECT SQL_CALC_FOUND_ROWS requests.id, requests.order_data, requests.user_id, requests.company_id, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, company, 
                        requests.industry req_industry, first_name, last_name, phone, phone_ext, email, industry_send, DATE_FORMAT(processed_date, "%m-%d-%Y") processed_date, tracking_number, complete_address, users_company.duplicate
                FROM requests 
                LEFT JOIN users ON users.id=requests.user_id
                LEFT JOIN users_company ON users_company.id=requests.company_id
                WHERE 1=1 ' . $date_search . ' GROUP BY company_id ORDER BY requests.id DESC LIMIT ' . $begin . ', ' . $for_page)->execute()->as_array();
                }
            }
        }
    }

    /*
     * Search for Data entry
     */

    public function search_print_dataentry($for_page, $page = 0) {
        $search = array();
        $date_search = '';
        $begin = $page * $for_page;
        $type = Session::instance()->get('print_entry_type');
        $date = Session::instance()->get('print_entry_date');

        if (!empty($date)) {
            $date = explode('-', $date);
            if (!empty($date[1])) {
                $date_search = ' AND UNIX_TIMESTAMP(request_date) >= UNIX_TIMESTAMP(STR_TO_DATE("' . trim($date[0]) . '","%m/%d/%Y")) AND UNIX_TIMESTAMP(request_date) <= UNIX_TIMESTAMP(STR_TO_DATE("' . trim($date[1]) . '","%m/%d/%Y")) ';
            }
        }

        if (empty($type)) {
            return DB::sql('SELECT SQL_CALC_FOUND_ROWS requests.id, requests.user_id, requests.order_data, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, users_company.company, first_name, industry_send, requests.industry req_industry,
                        last_name, phone, phone_ext, email, "-" processed_date, tracking_number, complete_address
                        FROM requests 
                        LEFT JOIN users ON users.id=requests.user_id
                        LEFT JOIN users_company ON users_company.id=requests.company_id
                        WHERE users.admin_comment="" ' . $date_search . '
                        ORDER BY requests.id DESC LIMIT ' . $begin . ',' . $for_page);
        } else {
            if (in_array('have_notes', $type)) {
                $notes = DB::sql('SELECT request_id FROM request_notes GROUP BY request_id');
                if (!empty($notes)) {
                    $ids = array();
                    foreach ($notes as $val) {
                        $ids[] = $val['request_id'];
                    }
                    $search[] = ' requests.id IN (' . implode(',', $ids) . ') ';
                }
            }
            if (in_array('processed', $type)) {
                $search[] = 'users.admin_comment LIKE "%processed\";s:1:\"1%"';
            }
            if (in_array('revision', $type)) {
                $search[] = 'users.admin_comment LIKE "%revision\";s:1:\"1%"';
            }
            return DB::sql('SELECT SQL_CALC_FOUND_ROWS requests.id, requests.user_id, requests.order_data, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, users_company.company, first_name, industry_send, requests.industry req_industry,
                        last_name, phone, phone_ext, email, "-" processed_date, tracking_number, complete_address
                        FROM requests 
                        LEFT JOIN users ON users.id=requests.user_id
                        LEFT JOIN users_company ON users_company.id=requests.company_id
                        WHERE 1=1 ' . $date_search . ' AND ' . implode(' AND ', $search) . '
                        ORDER BY requests.id DESC LIMIT ' . $begin . ',' . $for_page);
        }
    }

    /*
     * Get all found rows by SQL_CALC_FOUND_ROWS
     * @return (int) count
     */

    public function calcPages() {
        $rez = DB::sql_row('SELECT FOUND_ROWS() counts');
        if (!empty($rez)) {
            return $rez['counts'];
        }
    }

    /* Item notes */

    /*
     * Get user notes
     * @param (int) $id: request id
     * @param (string) $job_id: filter by job id 
     * @param (string) $user_type: type of users
     * @return (array) all notes
     */

    public function item_notes($cid, $job_id, $group_id, $user_type = 'A') {
        $current_job = '';
        $current_usertype = '';
        if ($user_type != 'A') {
            if (!empty($job_id)) {
                $current_usertype = ' AND type_user="' . $user_type . '"';
            }
        }
        if (!empty($job_id)) {
            $current_job = ' AND request_notes.job_id="' . $job_id . '" ';
        }
        $admin_group_filter = ($group_id == 6) ? '' : 'AND request_notes.removed=0 ';
        return DB::sql('SELECT request_notes.*, DATE_FORMAT(`date`, "%b %D, %Y: %l:%i %p") `date`, `date` date_orig,
            user_jobs.id user_jobs_id, user_jobs.job_id, user_jobs.estimate_id, users.first_name, users.last_name, CONCAT(us_req.first_name, " ", us_req.last_name) required_username
            FROM request_notes 
            LEFT JOIN user_jobs ON user_jobs.id=request_notes.job_id
            LEFT JOIN users ON users.id=request_notes.author_id
            LEFT JOIN users us_req ON us_req.id=request_notes.required_uid
            WHERE request_notes.company_id="' . $cid . '" ' . $current_job . $current_usertype . $admin_group_filter . ' ORDER BY date_orig DESC');
    }

    /*
     * Get required info for notes
     * @param (array) $notes: notes from db
     * @return (array) required array sorted by note_id
     */

    public function notes_required_info($notes) {
        $all = $get_required = array();
        if (!empty($notes)) {
            foreach ($notes as $val) {
                if (!empty($val['required_uid'])) {
                    $get_required[] = $val['id'];
                }
            }
            if (!empty($get_required)) {
                $req_data = DB::sql('SELECT request_note_required.*, 
                    CONCAT(ufrom.first_name," ",ufrom.last_name) from_username, 
                    CONCAT(ufor.first_name," ",ufor.last_name) for_username
                    FROM request_note_required 
                    LEFT JOIN users ufrom ON request_note_required.from_uid = ufrom.id
                    LEFT JOIN users ufor ON request_note_required.for_uid = ufor.id
                    WHERE note_id IN (' . implode(',', $get_required) . ') ORDER BY date');
                if (!empty($req_data)) {
                    foreach ($req_data as $val) {
                        $all[$val['note_id']][] = $val;
                    }
                }
            }
        }
        return $all;
    }

    /*
     * Get one note
     * @param (int) $id: Note id
     * @return (array) note data
     */

    public function item_one_note($id) {
        $rez = DB::sql_row('SELECT request_notes.*, DATE_FORMAT(`date`, "%b %D, %Y: %l:%i %p") `date`, `date` date_orig,
            user_jobs.id user_jobs_id, user_jobs.job_id, requests.user_id, user_jobs.estimate_id, users.first_name, users.last_name, CONCAT(us_req.first_name, " ", us_req.last_name) required_username
            FROM request_notes 
            LEFT JOIN user_jobs ON user_jobs.id=request_notes.job_id
            LEFT JOIN users ON users.id=request_notes.author_id
            LEFT JOIN users us_req ON us_req.id=request_notes.required_uid
            LEFT JOIN requests ON requests.id=request_notes.request_id
            WHERE request_notes.id=' . $id);
        return $rez;
    }

    /*
     * Add notes
     * @param (int) $id: request id
     * @return (int) inserted id
     */

    public function add_request_note($id) {
        $post = Request::initial()->post();
        $cookies = new Cookie();
        $admin = $cookies->get('admin_user');
        if (!empty($admin)) {
            $admin = unserialize($admin);
        }

        $required = (!empty($post['required'])) ? 1 : 0;
        $assign = (!empty($post['assign'])) ? (int)$post['assign'] : 0;
        $date = (!empty($post['date'])) ? $post['date'] : date('Y-m-d H:m:s');

        $job_id = (!empty($post['job_id'])) ? $post['job_id'] : 0;
        $comp = DB::sql_row('SELECT company_id FROM requests WHERE id=:id', array(':id' => $post['id']));
        $r = DB::sql('INSERT INTO request_notes (request_id, `text`, `date`, `type`, author_id, job_id, type_user, required_uid, company_id, removed) VALUES (:id, :text, :date,:type, :author_id, :job_id, :access, :required_uid, :company_id, :removed)', array(':id' => $id, ':text' => $post['note_text'], ':type' => $post['type'], ':date' => $date, ':author_id' => $admin['id'], ':job_id' => $job_id, ':access' => $post['user_type'], ':required_uid' => $assign, ':company_id' => $comp['company_id'], ':removed' => 0));
        $id = $r[0];

        if (!empty($required) && !empty($assign)) {
            DB::sql('INSERT INTO request_note_required (note_id,from_uid,for_uid,`date`) VALUES (:note_id,:from_uid,:for_uid,NOW())', array(
                ':note_id' => $id, ':from_uid' => $admin['id'], ':for_uid' => $assign
            ));
            //Keep eye on it
            DB::sql('DELETE FROM eye_user_company WHERE company_id=:company_id', array(':company_id' => $comp['company_id']));
            DB::sql('INSERT INTO eye_user_company (uid, company_id) VALUES (:uid, :company_id)', array(':uid' => $admin['id'], ':company_id' => $comp['company_id']));
        }

        if ($post['type'] == 'payment') {
            DB::sql('INSERT INTO payment_history (job_id,`type`,user_type,`date`,client_id,summ,description,card_id,edg, procent) 
                VALUES (:job_id,:type,:user_type,:date,:client_id,:summ,:description,:card_id,0,:procent)', array(
                ':job_id' => $job_id, ':type' => 'payment', ':user_type' => $post['user_type'], ':date' => $post['pay_note_date'],
                ':client_id' => $post['uid'], ':summ' => $post['pay_note_summ'], ':description' => $post['note_text'], ':card_id' => $post['pay_note_card'], ':procent' => ''
            ));
            DB::sql('UPDATE user_jobs SET order_counts=order_counts+1, payments+'.$post['pay_note_summ'].' WHERE id=:id', array(':id' => $job_id));
            if (!empty($post['pay_note_date'])) {
                DB::sql('UPDATE request_notes SET date=:date WHERE id=:id', array(':date' => $post['pay_note_date'], ':id' => $id));
            }
        }


        return $id;
    }

    /*
     * Update note
     * @param (int) $id: note id
     */

    public function saveRequestNotes($id) {
        $post = Request::initial()->post();
        $job_id = (!empty($post['job'])) ? $post['job'] : 0;
        DB::query(Database::UPDATE, 'UPDATE request_notes SET `text`=:text, `date`=:date, job_id=:job, `type`=:type WHERE id=:id')
                ->parameters(array(':id' => $id, ':text' => $post['text'], ':date' => $post['date'], ':job' => $job_id, ':type' => $post['type']))->execute();
    }

    /*
     * Restore removed note
     * @param (int) $id: note id
     * @return 1
     */

    public function restore_note($id) {
        DB::sql('UPDATE request_notes SET removed=0 WHERE id=:id', array(':id' => $id));
        return 1;
    }

    /*
     * Get job/estimate id by title
     * @param (int) $name: job/estimate title
     * @return (int) job id
     */

    public function getJob($id) {
        return DB::sql_row('SELECT * FROM user_jobs WHERE id=:id', array(':id' => $id));
    }

    public function updateJob($data) {
        $job = $data['job_abbr'] . '-' . $data['type'] . $data['num_job'];
        DB::sql('UPDATE user_jobs SET job_id=:job, user_id=:user_id WHERE id=:id', array(':job' => $job, ':user_id' => $data['job_user'], ':id' => $data['job_id']));
    }

    /*
     * Delete note
     * @param (int) $id: note id
     * @param (int) $user_group: admin group id
     */

    public function delRequestNotes($id, $user_group) {
        if ($user_group == 6) {
            //superadmin
            DB::sql('DELETE FROM request_notes WHERE id=:id', array(':id' => $id));
        } else {
            DB::sql('UPDATE request_notes SET removed=1 WHERE id=:id', array(':id' => $id));
        }
    }

    /*
     * Delete preview image
     * @param (int) $id: print id
     */

    public function remove_preview($id) {
        unlink(APPPATH . 'files/print/preview/' . $id . '.jpg');
    }

    /*
     * Delete active preview image
     * @param (int) $id: print id
     */

    public function remove_act_preview($id) {
        unlink(APPPATH . 'files/print/active_preview/' . $id . '.jpg');
    }

    /*
     * Delete view image
     * @param (int) $id: print id
     */

    public function remove_view($id) {
        unlink(APPPATH . 'files/print/view/' . $id . '.jpg');
    }

    /*
     * Delete psd image
     * @param (int) $id: print psd
     */

    public function remove_psd($id) {
        unlink(APPPATH . 'files/print/psd/' . $id . '.psd');
    }

    /* --- Job --- */

    /*
     * Update jobs
     * @param (int) $old_title: current title
     * @param (int) $title: new title
     */

    public function updateNote($id, $title) {
        DB::sql('UPDATE user_jobs SET job_id=:title WHERE id=:job_id', array(':title' => $title, ':job_id' => $id));
    }

    /*
     * Add new job
     * @param (int) $cid: company id
     * @return (array)
     */

    public function add_new_job($data) {
        $job = $data['job_abbr'] . '-' . $data['type'] . $data['num_job'] . $data['prefix'];
        $r = DB::sql('INSERT INTO user_jobs (user_id, company_id, job_id) VALUES (:user_id, :company_id, :job_id)', array(
                    ':user_id' => $data['job_user'], ':company_id' => $data['comp_id'], ':job_id' => $job
        ));
        $job = $r[0];
        DB::sql('UPDATE users_company SET main_uid = :uid WHERE id=:cid', array(':uid' => $data['job_user'], ':cid' => $data['comp_id']));
        DB::sql('UPDATE requests SET user_id = :uid WHERE company_id=:cid', array(':uid' => $data['job_user'], ':cid' => $data['comp_id']));
        return $job;
    }

    public function generate_new_job($cid, $check_abbr = '') {
        $new = 0;
        $this->user_model = Model::factory('Admin_User');
        if (empty($check_abbr)) {
            $company_abbr = DB::sql_row('SELECT * FROM users_company WHERE id = :cid', array(':cid' => $cid));
        } else {
            $company_abbr['company'] = $check_abbr;
        }

        if (empty($company_abbr['abbr']) || strpos($company_abbr['abbr'], 'x') === 0) {
            $global_str = array();
            $abbr = DB::sql('SELECT abbr FROM users_company WHERE abbr NOT LIKE "x%"');
            if (!empty($abbr)) {
                foreach ($abbr as $val) {
                    $global_str[$val['abbr']] = 1;
                }
            }
            //exec
            $abbr = DB::sql('SELECT abbr FROM users_company_exec');
            if (!empty($abbr)) {
                foreach ($abbr as $val) {
                    $global_str[$val['abbr']] = 1;
                }
            }

            $rez = $this->user_model->generate_user_abbr($company_abbr['company'], $global_str);

            if (empty($rez) || strlen($rez) < 3) {
                $count = DB::sql_row('SELECT COUNT(1) cc FROM users_company WHERE abbr LIKE "ZZZ%"');
                $num = $count['cc'] + 2;
                $rez = 'ZZZ' . $num;
            } else {
                if (strlen($rez) > 3) {
                    $cut = substr($rez, 0, 3);
                    $next = DB::sql('SELECT * FROM users_company WHERE abbr LIKE "' . $cut . '%"');

                    if (!empty($next)) {
                        $exists_nums = array();
                        foreach ($next as $val) {
                            $exists_nums[] = $val['abbr'];
                        }

                        for ($x = 2; $x < 999; $x++) {
                            if (!in_array($cut . $x, $exists_nums)) {
                                $rez = $cut . $x;
                                break;
                            }
                        }
                    } else {
                        $rez = $cut . '1';
                    }
                } else {
                    $rez_not_num = preg_replace('/[0-9]/', '', $rez);
                    if (empty($rez_not_num)) {
                        $count = DB::sql_row('SELECT COUNT(1) cc FROM users_company WHERE abbr LIKE "ZZZ%"');
                        $num = $count['cc'] + 2;
                        $rez = 'ZZZ' . $num;
                    }
                }
            }
            $company_abbr['abbr'] = $rez;
            $new = 1;
        }

        $company_jobs = DB::sql('SELECT job_id FROM user_jobs WHERE company_id=:id AND job_id!=""', array(':id' => $cid));
        if (!empty($company_abbr['abbr'])) {
            if (!empty($company_jobs)) {
                $all_num = array();
                foreach ($company_jobs as $v) {
                    $one = explode('-', $v['job_id']);
//                    $first_letter = substr($one[1], 0, 1);
//                    if(!in_array($first_letter, array('A','S','W'))){
                    $all_num[] = preg_replace('/[A-Z]/i', '', $one[1]);
//                    }
                }
                $max = max($all_num);
                $num = str_pad(($max + 1), 3, "0", STR_PAD_LEFT);
                $num_name = $num;
            } else {
                $num_name = '001';
            }
        } else {
            return array('err' => 'User don\'t have ABBR!');
        }
        return array('abbr' => $company_abbr['abbr'], 'num' => $num_name, 'new' => $new);
    }

    public function check_job_abbr($cid, $abbr) {
        $job = DB::sql_row('SELECT * FROM users_company WHERE abbr=:job', array(':job' => $abbr));
        if (empty($job)) {
            $job = DB::sql_row('SELECT users_company_exec.abbr, company FROM users_company_exec 
            LEFT JOIN users_company ON users_company.id=users_company_exec.company_id
            WHERE users_company_exec.abbr=:job', array(':job' => $abbr));
        }

        if (empty($job)) {
            return array('ok' => 1);
        } else {
            //generate alternative
            $rez = $this->generate_new_job($cid, $abbr);
            //check
//            $result = DB::sql_row('SELECT * FROM users_company WHERE abbr=:abbr', array(':abbr'=>$rez));
//            if(empty($result)){
            return array('err_comp' => $job['company'], 'alt' => $rez);
//            }else{
//                $rez2 = $this->generate_new_job($cid, $rez[0]);
//                return array('err_comp' => $job['company'], 'alt' => $rez2);
//            }
        }
    }

    /*
     * Add new estimete
     * @param (int) $uid: user id
     * @return (array)
     */

//    public function add_new_estimate($uid) {
//        $user = DB::sql_row('SELECT abbr FROM users_company WHERE id=(SELECT company_id FROM users WHERE id=:id)', array(':id' => $uid));
//        $date = date('ymd');
//        $user_jobs = DB::sql('SELECT estimate_id FROM user_jobs WHERE user_id=:id AND estimate_id LIKE "' . $user['abbr'] . '-' . $date . '-%"', array(':id' => $uid));
//        if (!empty($user['abbr'])) {
//            if (!empty($user_jobs)) {
//                $all_num = array();
//                foreach ($user_jobs as $v) {
//                    $one = explode('-', $v['estimate_id']);
//                    $all_num[] = intval($one[2]);
//                }
//                $max = max($all_num);
//                $num = str_pad(($max + 1), 3, "0", STR_PAD_LEFT);
//                $name = $user['abbr'] . '-' . $date . '-' . $num;
//            } else {
//                $name = $user['abbr'] . '-' . $date . '-001';
//            }
//            $us = DB::sql_row('SELECT company_id FROM users WHERE id=:id', array(':id' => $uid));
//            DB::sql('INSERT INTO user_jobs (user_id, estimate_id, company_id) VALUES (:user_id, :name, :company_id)', array(':user_id' => $uid, ':name' => $name, 'company_id' => $us['company_id']));
//            $id = mysql_insert_id();
//            Model::factory('Admin_Event')->add_event('new_job', $id, 'New Estimate ' . $name);
//        } else {
//            return array('err' => 'User don\'t have ABBR!');
//        }
//        return array('name' => $name);
//    }

    /*
     * Get additional sent print requests by Fedex
     * @return (array) list of requests
     */

    public function item_more_req($id) {
        return DB::sql('SELECT * FROM requests_more_sent WHERE req_id=:req_id', array(':req_id' => $id));
    }

    /*
     * Delete Job
     * @param (int) $id: job/estimate title
     * @return 1
     */

    public function delete_job($id) {
        $job_data = DB::sql_row('SELECT * FROM user_jobs WHERE id=:job_id', array(':job_id' => $id));

        $exist_payments = DB::sql_row('SELECT * FROM payment_history WHERE job_id=:job_id', array(':job_id' => $id));
        if (empty($exist_payments)) {
            DB::sql('DELETE FROM user_jobs WHERE id=:job_id', array(':job_id' => $id));
            Model::factory('Admin_Event')->add_event('job_removed', $id, serialize($job_data));

            //check count of JOB#, if 0 - change ABBR to temp
            $all = DB::sql('SELECT 1 FROM user_jobs WHERE company_id=:cid', array(':cid' => $job_data['company_id']));
            if (empty($all)) {
                DB::sql('UPDATE users_company SET abbr="xQWERTY" WHERE id=:cid', array(':cid' => $job_data['company_id']));
            }
            return 'ok';
        }
        return 'exist_payment';
    }

    /*
     * Get User payment history
     * @param (int) $uid: user id
     * @param (string) $job_name: filter job
     * @param (string) $user_type: filter by user type
     * @return (array) payment history list
     */

    public function get_payment_history($uid, $job_id, $user_type) {
        $current_job = '';
        if (!empty($job_id)) {
            $current_job = ' payment_history.job_id="' . $job_id . '" ';
        } else {
            $current_job = 'client_id=' . $uid;
        }
        if ($user_type == 'A') {
            $current_usertype = '';
        } else {
            $current_usertype = ' AND payment_history.user_type="' . $user_type . '"';
        }
        return DB::sql('SELECT payment_history.*, `date` date_sort, DATE_FORMAT(`date`, "%a, %b %D, %Y: %l:%i %p (EST)") `date`, user_jobs.job_id, credit_card.card_number
            FROM payment_history 
            LEFT JOIN user_jobs ON user_jobs.id=payment_history.job_id
            LEFT JOIN credit_card ON credit_card.id=payment_history.card_id
            WHERE ' . $current_job . $current_usertype . ' ORDER BY date_sort, id');
    }

    /*
     * Add/Update request samples
     * @param (array) $industry_items: items array
     * @param (array) $industry_collection: collections array
     * @return (string) imploded titles
     */

    public function save_industry_items($industry_items, $industry_collection) {
        $post = Request::initial()->post();
        $rez = array();
        DB::sql_row('DELETE FROM request_samples WHERE request_id=:request_id', array(':request_id' => $post['req_id']));
        if (!empty($industry_items)) {
            foreach ($industry_items as $key => $val) {
                if (!empty($val)) {
                    DB::sql('INSERT INTO request_samples (request_id, industry_id, industry_samples) VALUES (:request_id, :industry_id, :industry_samples)', array(
                        ':request_id' => $post['req_id'], ':industry_id' => $key, ':industry_samples' => serialize($val)
                    ));
                    //get industry title
                    $industry = DB::sql_row('SELECT title FROM is_types WHERE `index`=:index', array(':index' => $key));
                    $rez['samples_string'][] = $industry['title'] . '(' . count($val) . ')';
                }
            }
        } elseif (!empty($industry_collection)) {
            foreach ($industry_collection as $key => $val) {
                DB::sql('INSERT INTO request_samples (request_id, industry_id, industry_samples) VALUES (:request_id, :industry_id, :industry_samples)', array(
                    ':request_id' => $post['req_id'], ':industry_id' => $key, ':industry_samples' => serialize($val)
                ));
                $industry = DB::sql_row('SELECT title FROM is_types WHERE `index`=:index', array(':index' => $key));
                $rez['samples_string'][] = $industry['title'] . '(' . count($val) . ')';
            }
        }
        $rez['samples_string'] = (!empty($rez['samples_string'])) ? implode(' + ', $rez['samples_string']) : '';
        //update request table
        DB::sql('UPDATE requests SET industry_send=:industry WHERE id=:id', array(':id' => $post['req_id'], ':industry' => $rez['samples_string']));
        return $rez;
    }

    /*
     * Print Data entry filter
     * @param (string) $type: filter type
     */

    public function dataentry_filter($type, $for_page, $page) {
        $begin = $page * $for_page;
        switch ($type) {
            case 'notes':
                return DB::sql('SELECT SQL_CALC_FOUND_ROWS requests.id, requests.user_id, requests.order_data, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, users_company.company, first_name, industry_send, requests.industry req_industry,
                        last_name, phone, email, phone_ext, processed_date, tracking_number, complete_address
                        FROM request_notes 
                        LEFT JOIN requests ON requests.id=request_notes.request_id
                        LEFT JOIN users ON users.id=requests.user_id
                        LEFT JOIN users_company ON users_company.id=requests.company_id
                        WHERE users.admin_comment NOT LIKE "%processed%"
                        GROUP BY request_notes.request_id
                        ORDER BY requests.id DESC LIMIT ' . $begin . ', ' . $for_page);
                break;
            case 'processed':
                return DB::sql('SELECT SQL_CALC_FOUND_ROWS requests.id, requests.user_id, requests.order_data, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, users_company.company, first_name, industry_send, requests.industry req_industry,
                        last_name, phone, email, phone_ext, processed_date, tracking_number, complete_address
                        FROM requests 
                        LEFT JOIN users ON users.id=requests.user_id
                        LEFT JOIN users_company ON users_company.id=requests.company_id
                        WHERE users.admin_comment LIKE "%processed%"
                        ORDER BY requests.id DESC LIMIT ' . $begin . ', ' . $for_page);
                break;
            case 'revision':
                return DB::sql('SELECT SQL_CALC_FOUND_ROWS requests.id, requests.user_id, requests.order_data, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, users_company.company, first_name, industry_send, requests.industry req_industry,
                        last_name, phone, email, phone_ext, processed_date, tracking_number, complete_address
                        FROM requests 
                        LEFT JOIN users ON users.id=requests.user_id
                        LEFT JOIN users_company ON users_company.id=requests.company_id
                        WHERE users.admin_comment LIKE "%revision%"
                        ORDER BY requests.id DESC LIMIT ' . $begin . ', ' . $for_page);
                break;
        }
    }

    /*
     * Add note required request responce
     * @param (array) $admin: current user data
     * @return 1
     */

    public function add_required_message($admin) {
        $post = Request::initial()->post();
        DB::sql('UPDATE request_note_required SET status=1 WHERE id=:id', array(':id' => $post['id']));

        $current = DB::sql_row('SELECT * FROM request_note_required WHERE id=:id', array(':id' => $post['id']));
        $status = ($post['type'] == 'clarify') ? 0 : 1;
        $for_id = (empty($post['uid'])) ? 0 : $post['uid'];
//        if (!empty($for_id)) {
        DB::sql('INSERT INTO request_note_required (note_id,from_uid,for_uid,text,date,status) 
                VALUES (:note_id,:from_uid,:for_uid,:text,NOW(),:status)', array(':note_id' => $current['note_id'], ':from_uid' => $admin['id'], ':for_uid' => $for_id, ':text' => $post['text'], ':status' => $status));
//        }
        return 1;
    }

    /*
     * Get additional phone/email by requests
     * @param (array) $requests: all finded request
     */

    public function get_additional_info($requests) {
        $users = $rez_all = array();
        if (!empty($requests)) {
            foreach ($requests as $val) {
                $users[] = $val['user_id'];
            }
            if (!empty($users)) {
                $rez = DB::sql('SELECT * FROM user_additional_info WHERE uid IN (' . implode(',', $users) . ')');
                if (!empty($rez)) {
                    foreach ($rez as $val) {
                        $rez_all[$val['uid']][$val['type']][] = $val;
                    }
                }
            }
        }
        return $rez_all;
    }

    /*
     * Show same users/request when we add new user
     */

    public function check_add_user($data) {
        $rez = array();
        $select_fields = 'requests.id, requests.user_id, requests.order_data, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, requests.company_id,
            users_company.company, first_name, industry_send, requests.industry req_industry, last_name, phone, phone_ext, email, processed_date, tracking_number, complete_address';
        if (!empty($data['email'])) {
            $email = DB::sql('SELECT ' . $select_fields . ' FROM requests 
                LEFT JOIN users ON users.id=requests.user_id 
                LEFT JOIN users_company ON users_company.id=requests.company_id
                WHERE users.email LIKE "%' . $data['email'] . '%"
            ORDER BY requests.id DESC');
            if (!empty($email)) {
                foreach ($email as $val) {
                    $rez[$val['company_id']] = $val;
                }
            }
        }
        if (!empty($data['first_name']) || !empty($data['last_name'])) {
            $username = DB::sql('SELECT ' . $select_fields . ' FROM requests 
                LEFT JOIN users ON users.id=requests.user_id 
                LEFT JOIN users_company ON users_company.id=users.company_id
                WHERE users.first_name=:first_name AND users.last_name=:last_name
            ORDER BY requests.id DESC', array(':first_name' => $data['first_name'], ':last_name' => $data['last_name']));
            if (!empty($username)) {
                foreach ($username as $val) {
                    $rez[$val['company_id']] = $val;
                }
            }
        }
        if (!empty($data['company'])) {
            $company = DB::sql('SELECT ' . $select_fields . ' FROM requests 
                LEFT JOIN users ON users.id=requests.user_id 
                LEFT JOIN users_company ON users_company.id=users.company_id
                WHERE users_company.company LIKE "%' . $data['company'] . '%" 
                    ORDER BY requests.id DESC');
            if (!empty($company)) {
                foreach ($company as $val) {
                    $rez[$val['company_id']] = $val;
                }
            }
        }
        if (!empty($data['phone'])) {
            $phone = DB::sql('SELECT ' . $select_fields . ' FROM requests 
                LEFT JOIN users ON users.id=requests.user_id 
                LEFT JOIN users_company ON users_company.id=users.company_id
                WHERE REPLACE(users.phone,"-","") LIKE "%' . str_replace('-', '', $data['phone']) . '%" ORDER BY requests.id DESC');
            if (!empty($phone)) {
                foreach ($phone as $val) {
                    $rez[$val['company_id']] = $val;
                }
            }
        }
        return $rez;
    }

    /*
     * Add validation
     */

    public function add_user_validation(&$err, $data) {
        $arr = array('company' => 'Company', 'first_name' => 'First Name', 'last_name' => 'Last Name', 'email' => 'Email');
        foreach ($arr as $key => $val) {
            if (empty($data[$key])) {
                $err = 'Field ' . $val . ' is empty!';
                return false;
            }
            if ($key == 'email' && !filter_var($data[$key], FILTER_VALIDATE_EMAIL)) {
                $err = 'Invalid ' . $val . ' field!';
                return false;
            }
        }
        return true;
    }

    /*
     * Add new user
     * @param (array) $data: all data
     * @return 1
     */

    public function add_new_user($data) {
        // Check Company
        $company = DB::sql_row('SELECT * FROM users_company WHERE company=:company', array(':company' => $data['company']));
        $r = DB::sql('INSERT INTO users_company (company, main_uid, abbr, duplicate) VALUES (:company, :main_uid, :abbr, :duplicate)', array(
            ':company' => $data['company'],
            ':main_uid' => 0,
            ':abbr' => '',
            ':duplicate' => 0
        ));
        $comp_id = $r[0];

        //add user
        $user['id'] = $this->add_user($comp_id, $data);

        $duplicate = (empty($company))? 0 : 1 ;
        
        //update company main user
        DB::sql('UPDATE users_company SET main_uid=:uid, duplicate=:duplicate WHERE id=:id', array(':id' => $comp_id, ':uid' => $user['id'], ':duplicate'=>$duplicate));

        $order_data = array();
        foreach ($data as $key => $val) {
            if (strpos($key, 'product_') !== FALSE) {
                $type = explode('_', $key);
                $order_data[$type[1]] = $val;
            }
        }
        if (!empty($data['product_other'])) {
            $order_data['other'] = $data['product_other'];
        }

        $address = $data['company'] . '
' . $data['first_name'] . ' ' . $data['last_name'] . '
' . $data['address'] . '
' . $data['city'] . ', ' . $data['state'] . ' ' . $data['zip'] . '
' . $data['phone'];
        if (!empty($data['phone_ext'])) {
            $address.= ' ext ' . $data['phone_ext'];
        }
        $r = DB::sql('INSERT INTO requests (company_id, user_id, job_id, request_date, industry, industry_send, complete_address, order_data, tracking_number, operating_sys, graphics_app, ref_source, other_source, conversations, search_id, offers, user_ip, status) 
            VALUES (:company_id, :user_id, 0, NOW(), :industry, :industry_send, :complete_address, :order_data, "", "", "", "Admin CP", "", "", "", "", "", 1)', array(
                    ':company_id' => $comp_id, ':user_id' => $user['id'], ':industry' => $data['industry'], ':industry_send' => $data['industry'], ':complete_address' => $address, ':order_data' => serialize($order_data)
        ));
        $id = $r[0];
        //add event
        Model::factory('Admin_Event')->add_event('new_request', $id);
        return 1;
    }

    /*
     * Add user/company
     */

    private function add_user($comp_id, $data) {
        $r = DB::sql('INSERT INTO users (login,password,email,email_alt,first_name,last_name,group_id,company_id,user_abbr,country,street,street2,city,state,zipcode,phone,phone_ext,phone_type,position,industry,fax,admin_comment) 
                    VALUES (:login,"",:email,:email_alt,:first_name,:last_name,1,:company_id,:user_abbr,:country,:street,:street2,:city,:state,:zip,:phone,:phone_ext,:phone_type,:position,:industry,:fax,"")', array(
                        ':login' => $data['email'],
                        ':email' => $data['email'],
                        ':email_alt' => '',
                        ':first_name' => $data['first_name'],
                        ':last_name' => $data['last_name'],
                        ':company_id' => $comp_id,
                        ':user_abbr' => '',
                        ':country' => $data['country'],
                        ':street' => $data['address'],
                        ':street2' => isset($data['address2']) ? $data['address2'] : '',
                        ':city' => $data['city'],
                        ':state' => $data['state'],
                        ':zip' => $data['zip'],
                        ':phone' => $data['phone'],
                        ':phone_ext' => $data['phone_ext'],
                        ':phone_type' => '',
                        ':position' => $data['position'],
                        ':industry' => $data['industry'],
                        ':fax' => $data['fax']
                    ));
        //add event
        $uid = $r[0];

        $comp = DB::sql_row('SELECT company FROM users_company WHERE id=:id', array(':id' => $comp_id));
        //add def shipping info
        DB::sql('INSERT INTO credit_card_shipping (user_id,company_id,title,first_name,last_name,company,address,address2,suite,city,state,zip,country,phone,phone_ext,email,public) 
                VALUES (:user_id,:company_id,:title,:first_name,:last_name,:company,:address,:address2,:suite,:city,:state,:zip,:country,:phone,:phone_ext,:email,:public)', array(
            ':user_id' => $uid, ':company_id' => $comp_id, ':title' => 'default', ':first_name' => $data['first_name'], ':last_name' => $data['last_name'], ':company' => $comp['company'], ':address' => $data['address'],
            ':address2' => isset($data['address2']) ? $data['address2'] : '', ':suite' => '', ':city' => $data['city'], ':state' => $data['state'], ':zip' => $data['zip'], ':country' => @$data['country'], ':phone' => $data['phone'], ':phone_ext' => $data['phone_ext'], ':email' => $data['email'], ':public' => 0
        ));
        //add def billing info
        DB::sql('INSERT INTO credit_card_billing (user_id,title,first_name,last_name,company,address,address2,suite,city,state,zip,country,phone,phone_ext,email,`default`,visible,full_name) 
                VALUES (:user_id,:title,:first_name,:last_name,:company,:address,:address2,:suite,:city,:state,:zip,:country,:phone,:phone_ext,:email,1,1,:full_name)', array(
            ':user_id' => $uid, ':title' => 'default', ':first_name' => $data['first_name'], ':last_name' => $data['last_name'], ':company' => $comp['company'], ':address' => $data['address'],
            ':address2' => isset($data['address2']) ? $data['address2'] : '', ':suite' => '', ':city' => $data['city'], ':state' => $data['state'], ':zip' => $data['zip'], ':country' => @$data['country'], ':phone' => $data['phone'], ':phone_ext' => $data['phone_ext'], ':email' => $data['email'], ':full_name' => trim($data['first_name'].' '.$data['last_name'])
        ));
        Model::factory('Admin_Event')->add_event('new_user', $uid);
        return $uid;
    }

    /*
     * Fast update request compleate address
     * @return (string) complete address
     */

    public function fast_save_user_address() {
        $post = Request::initial()->post();
        $complete_address = $post['company'] . ' 
' . $post['first_name'] . ' ' . $post['last_name'] . ' 
' . $post['street'] . ' 
' . $post['city'] . ', ' . $post['state'] . ' ' . $post['zipcode'] . ' 
' . $post['phone'];
        if (!empty($post['phone_ext'])) {
            $complete_address .= ' x' . $post['phone_ext'];
        }
        DB::sql('UPDATE requests SET complete_address=:complete_address WHERE id=:id', array(':id' => $post['req_id'], ':complete_address' => $complete_address));
        DB::sql('UPDATE users_company SET company=:company WHERE users_company.id = (SELECT company_id FROM users WHERE users.id=:id)', array(':id' => $post['id'], ':company' => $post['company']));
        DB::sql('UPDATE users SET street=:street, city=:city, state=:state, zipcode=:zipcode, phone=:phone, phone_ext=:phone_ext, first_name=:first_name, last_name=:last_name 
            WHERE id=:id', array(':id' => $post['id'], ':company' => $post['company'], ':street' => $post['street'], ':city' => $post['city'], ':phone_ext' => $post['phone_ext'],
            ':state' => $post['state'], ':zipcode' => $post['zipcode'], ':phone' => $post['phone'], ':first_name' => $post['first_name'], ':last_name' => $post['last_name']));
        return nl2br($complete_address);
    }

    public function eye_companys($req_id) {
        $rez = DB::sql('SELECT 1 FROM eye_user_company WHERE company_id=(SELECT company_id FROM requests WHERE id=:req_id)', array(':req_id' => $req_id));
        return (empty($rez)) ? false : true;
    }

    public function get_company_payment_history($cid, $user_type) {
        if ($user_type == 'A') {
            $current_usertype = '';
        } else {
            $current_usertype = ' AND payment_history.user_type="' . $user_type . '"';
        }
        return DB::sql('SELECT payment_history.*, date date_sort,DATE_FORMAT(`date`, "%b %D, %Y: %l:%i %p (EST)") `date`, user_jobs.job_id, credit_card.card_number
            FROM payment_history 
            LEFT JOIN user_jobs ON user_jobs.id=payment_history.job_id
            LEFT JOIN credit_card ON credit_card.id=payment_history.card_id
            WHERE client_id IN (SELECT id FROM users WHERE company_id=:cid) ' . $current_usertype . ' AND removed=0 ORDER BY date_sort,id', array(':cid' => $cid));
    }

}
