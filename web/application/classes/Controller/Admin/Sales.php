<?php

defined('SYSPATH') or die('No direct access allowed.');

class Controller_Admin_Sales extends Admin {

    public function before() {
        parent::before();
        $this->print_model = Model::factory('Admin_Print');
        $this->sales_model = Model::factory('Admin_Sales');
        $this->user_model = Model::factory('Admin_User');
        $this->template->scripts[] = '/js/admin/sales.init.js';

        //generate user TMP abbr
        $other = DB::sql('SELECT id,company FROM users_company WHERE abbr=""');
        foreach ($other as $val) {
            $name = md5($val['id'] . $val['company'] . time());
            $rez_not_num = strtoupper(preg_replace('/[0-9]/', '', $name));
            $cut = 'x' . substr($rez_not_num, 0, 6);
            DB::sql('UPDATE users_company SET abbr=:abbr WHERE id=:id', array(':abbr' => $cut, ':id' => $val['id']));
        }
    }

    public function action_all($process = false, $tasks = false) {
        if ($tasks) {
            $this->data['hide_search'] = ($tasks == 1) ? 3 : 2;
        }
        $session = Session::instance();
        $this->data['err'] = NULL;
        $page = $this->request->param('param1');
        $id = $this->request->param('param2');
        $this->template->scripts[] = '/js/admin/fedex.init.js';
        switch ($page) {
            case 'edit':
                $this->data['edit_next'] = false;
                if (empty($id)) {
                    $items = $session->get('edit_print');
                    if (empty($items[0])) {
                        $this->redirect('/admin/sales/all');
                    } else {
                        $id = $items[0];
                        $this->data['edit_next'] = true;
                    }
                }

                if ($this->request->post('save') || $this->request->post('save_next') || $this->request->post('save_prev')) {
                    $this->data['fedex_content'] = $this->print_model->save_print_req($id);
                    $url = 'sales/all/';
                    if ($this->request->post('save_next')) {
                        if (!empty($items)) {
                            $shift = array_shift($items);
                            $session->set('edit_print', $items);

                            $all_shift = $session->get('edit_print_prev');
                            $all_shift[] = $shift;
                            $session->set('edit_print_prev', $all_shift);
                        }
                        if ($this->data['edit_next']) {
                            $this->redirect('/admin/sales/all/edit');
                        } else {
                            $next = $this->print_model->getNextItem($id);
                            $this->redirect('/admin/sales/all/edit/' . $next);
                        }
                    } elseif ($this->request->post('save_prev')) {
                        if (!empty($items)) {
                            $all_shift = $session->get('edit_print_prev');
                            if (!empty($all_shift)) {
                                $last = array_pop($all_shift);
                            } else {
                                $session->set('edit_print', NULL);
                                $session->set('edit_print_prev', NULL);
                                $this->redirect('/admin/' . $url);
                            }
                            array_unshift($items, $last);
                            $session->set('edit_print', $items);
                        }
                        if ($this->data['edit_next']) {
                            $this->redirect('/admin/sales/all/edit');
                        } else {
                            $prev = $this->print_model->getPrevItem($id);
                            $this->redirect('/admin/sales/all/edit/' . $prev);
                        }
                    } else {
                        $session->set('edit_print', NULL);
                        $session->set('edit_print_prev', NULL);
                        $this->redirect('/admin/' . $url);
                    }
                }
                $this->data['item'] = $this->print_model->item_info($id);
                $this->data['alt'] = $this->print_model->item_alt_info($this->data['item']['user_id']);
                $this->data['item_more_req'] = $this->print_model->item_more_req($id);
                $tpl = 'sales/edit_request';
                break;
            case 'remove':
                $this->print_model->remove_print_req($id);
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            default:
                if ($tasks) {
                    $this->data['results'] = $this->print_model->get_tasks_req($tasks);
                } else {
                    $this->data['search_word'] = $last_search = Session::instance()->get('print_search');

                    if (empty($last_search)) {
                        $this->data['results'] = $this->print_model->get_process_req();
//                        $this->data['proc'] = 1;
                    } else {
                        $last_search_fields = Session::instance()->get('print_search_fields');
                        $this->data['search_date'] = $print_search_date = Session::instance()->get('print_search_date');
                        if (!empty($last_search) || !empty($print_search_date)) {
                            $for_page = 50;
                            $page = intval(@$_GET['page']);
                            $this->data['results'] = $this->print_model->search_print($last_search, $for_page, $page, false, $last_search_fields, $print_search_date);
                            if (!empty($this->data['results'])) {
                                $all = $this->print_model->calcPages();
                                $this->data['pages'] = Pagination::factory(array('total_items' => $all, 'items_per_page' => $for_page))->render();
                            }
                        }
                    }
                }
                //get additional phone/email
                if (!empty($this->data['results'])) {
                    $this->data['alt'] = $this->print_model->get_additional_info($this->data['results']);
                }

                //get Industry from 
                $insp_model = Model::factory('Inspiration');
                $this->data['is_types'] = $insp_model->get_types();

                $this->template->scripts[] = '/js/admin/daterangepicker.jQuery.js';
                $this->template->scripts[] = '/js/admin/daterangepicker.init.js';
                $tpl = 'sales/all';
        }
        $this->template->content = View::factory('admin/' . $tpl, $this->data);
    }

    public function action_dataentry() {
        $this->template->scripts[] = '/js/admin/daterangepicker.jQuery.js';
        $this->template->scripts[] = '/js/admin/daterangepicker.init.js';
        $this->data['hide_search'] = 1;
        $this->data['show_data_entry'] = 1;

        $for_page = 50;
        $page = (empty($_GET['page'])) ? 0 : intval(@$_GET['page']) - 1;

        $this->data['results'] = $this->print_model->search_print_dataentry($for_page, $page);
        if (!empty($this->data['results'])) {
            $all = $this->print_model->calcPages();
            $this->data['pages'] = Pagination::factory(array('total_items' => $all, 'items_per_page' => $for_page))->render();
            $this->data['alt'] = $this->print_model->get_additional_info($this->data['results']);
        }
        $this->template->content = View::factory('admin/sales/all', $this->data);
    }

    public function action_fedex_labelslist() {
        $session = Session::instance();
        $fedex_labels_list = $session->get('fedex_labels_list');
//        $fedex_labels_list = array('13844', '13889', '13850', '13831');
        if (!empty($fedex_labels_list)) {
            $this->data['requests'] = DB::sql('SELECT requests.id,complete_address,requests.industry,requests.industry_send, CONCAT(first_name," ",last_name) username, 
                    company,DATE_FORMAT(requests.request_date, "%m-%d-%Y") request_date, requests.industry, requests.processed_date FROM requests 
                    LEFT JOIN users ON users.id=requests.user_id
                    LEFT JOIN users_company ON users_company.id=requests.company_id
                    WHERE requests.id IN (' . implode(',', $fedex_labels_list) . ')');
        }
        $this->template->content = View::factory('admin/sales/fedex_labels_list', $this->data);
    }

    public function action_fedex_sentlist() {
        $session = Session::instance();
        $item_details = array();
        $fedex_labels_list = $session->get('fedex_labels_list');
        $this->data['printed'] = $session->get('printed_labels');
        if (empty($this->data['printed'])) {
            $this->data['printed'] = array();
        }
        $fedex_labels_list = array('13844', '13889', '13850', '13831');
        if (!empty($fedex_labels_list)) {
            foreach ($fedex_labels_list as $val) {
                $this->data['samples'][$val] = $this->get_request_industry($val);
                if (!empty($this->data['samples'][$val])) {
                    foreach ($this->data['samples'][$val] as $v) {
                        $item_details[] = implode(',', $v);
                    }
                }
            }
            if (!empty($item_details)) {
                $sample_details = DB::sql('SELECT item_id, title, type_index FROM is_items WHERE item_id IN (' . implode(',', $item_details) . ')');
                if (!empty($sample_details)) {
                    foreach ($sample_details as $val) {
                        $this->data['sample_details'][$val['type_index']][$val['item_id']] = $val;
                    }
                }
            }
            if (!empty($fedex_labels_list)) {
                $this->data['requests'] = DB::sql('SELECT requests.id,complete_address,requests.industry,requests.industry_send, CONCAT(first_name," ",last_name) username, 
                    users.company_id,DATE_FORMAT(requests.request_date, "%m-%d-%Y") request_date, requests.industry, requests.processed_date FROM requests 
                    LEFT JOIN users ON users.id=requests.user_id
                    WHERE requests.id IN (' . implode(',', $fedex_labels_list) . ')');
            }
        }
        $this->template = View::factory('admin/sales/fedex_sent_list', $this->data);
    }

    public function action_fedex() {
        $session = Session::instance();
        $fedex_send = $session->get('fedex_send');
        $this->template->scripts[] = '/js/admin/fedex.init.js';
        if (empty($fedex_send[0])) {
            $this->redirect('/admin/Sales/all');
        } else {
            $this->data['packWeight'] = $this->data['maxWidth'] = $this->data['maxHeight'] = 0;
            $samples = $this->get_request_industry($fedex_send[0]);
            if (!empty($samples)) {
                //calculate total weight/size
                foreach ($samples as $val) {
                    foreach ($val as $v) {
                        $avaliable_items[] = $v;
                    }
                }
                $items = DB::sql('SELECT is_items.*, iis.width, iis.height FROM is_items 
                    LEFT JOIN is_item_size iis ON (iis.item_id=is_items.id AND iis.type="flat")
                    WHERE id IN (' . implode(',', $avaliable_items) . ')');
                if (!empty($items)) {
                    foreach ($items as $val) {
                        $this->data['packWeight']+=$val['weight'];
                        $this->data['maxWidth'] = ($this->data['maxWidth'] < $val['weight']) ? $val['width'] : $this->data['maxWidth'];
                        $this->data['maxHeight'] = ($this->data['maxHeight'] < $val['height']) ? $val['height'] : $this->data['maxHeight'];
                    }
                }
            }
            //fedex autofill fields
            $this->data['autofill'] = $this->print_model->fedex_autofill($fedex_send[0]);
            $this->data['item'] = $this->print_model->item_info($fedex_send[0]);

            $this->template->content = View::factory('admin/sales/fedex_send', $this->data);
        }
    }

    public function action_label() {
        $req = array('', '');
        $id = $this->request->param('param1');
        //add printed data to session
        if (strpos($id, '_') !== FALSE) {
            $req = explode('_', $id);
            $id = $req[0];
        }

        $session = Session::instance();
        $printed = $session->get('printed_labels');
        $printed[$id] = $id;
        $session->set('printed_labels', $printed);
        $this->template = View::factory('printed_tempalte');
        if (empty($req[1])) {
            $this->template->content = $id;
        } else {
            $this->template->content = $id . '_' . $req[1];
        }
    }

    public function action_tasks() {
        $all_eye = DB::sql('SELECT company_id FROM eye_user_company');
        if (!empty($all_eye)) {
            foreach ($all_eye as $val) {
                $eye_list[] = $val['company_id'];
            }

            $this->data['results'] = DB::sql('SELECT SQL_CALC_FOUND_ROWS requests.id, requests.order_data, requests.user_id, requests.company_id, DATE_FORMAT(request_date, "%m-%d-%Y") request_date, industry_send, requests.industry req_industry,
                        company, users.industry, first_name, last_name, phone, phone_ext, email, complete_address, "-" processed_date, tracking_number, duplicate
                        FROM requests 
                        LEFT JOIN users ON users.id=requests.user_id
                        LEFT JOIN users_company ON users_company.id=requests.company_id
                        WHERE requests.company_id IN (' . implode(',', $eye_list) . ')
                        GROUP BY company_id ORDER BY requests.id DESC');
            //get additional phone/email
            if (!empty($this->data['results'])) {
                $this->data['alt'] = $this->print_model->get_additional_info($this->data['results']);
            }
            $this->data['hide_search'] = 1;
        }
        $this->template->content = View::factory('admin/sales/all', $this->data);
    }

    public function action_current_jobs() {
        $this->action_all(false, 1);
    }

    public function action_assigned_tasks() {
        $this->action_all(false, 2);
    }

    public function get_request_industry($id) {
        $all = array();
        $rez = DB::sql('SELECT * FROM request_samples WHERE request_id=:request_id', array(':request_id' => $id));
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[$val['industry_id']] = unserialize($val['industry_samples']);
            }
        }
        return $all;
    }

    public function get_all_collections() {
        return DB::sql('SELECT * FROM request_samples_collection');
    }

    public function get_one_collection($id) {
        return DB::sql_row('SELECT * FROM request_samples_collection WHERE id=:id', array(':id' => $id));
    }

    public function action_ajax() {
        $post = $this->request->post();
        $session = Session::instance();
        $rez = array();
        switch ($post['func']) {
            case 'send_email':
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=utf-8" . "\r\n";
                $headers .= "From: info@" . $_SERVER['SERVER_NAME'] . "\r\n";
                $rez = mail($_POST['email'], $_POST['subj'], $_POST['text'], $headers);

                //add note
                DB::sql('INSERT INTO request_notes (request_id, `text`, `date`, `type`, author_id, job_id, type_user, required_uid, company_id, removed) '
                        . 'VALUES (:id, :text, NOW(), "email_out", :author_id, :job_id, :access, :required_uid, :company_id, :removed)', 
                        array(':id' => $post['req_id'], ':text' => 'Subject: '.$post['subj'].'; Text: '.$post['text'], ':author_id' => $this->admin['id'], 
                            ':job_id' => $post['job_id'], ':access' => 'A', ':required_uid' => 0, ':company_id' => $post['comp_id'], ':removed' => 0));


                break;
            case 'save_note':
                $this->print_model->saveRequestNotes($post['id']);
                $rez['text'] = nl2br($post['text']);
                break;
            case 'not_save_note':
                $rez = DB::sql_row('SELECT *, DATE_FORMAT(`date`, "%M %D, %Y: %l:%i %p") `date`, `date` date_orig FROM request_notes WHERE id=:id', array(':id' => $post['id']));
                $rez['text'] = nl2br($rez['text']);
                break;
            case 'del_note':
                $this->print_model->delRequestNotes($post['id'], $this->admin['group_id']);
                $rez['ok'] = 1;
                break;
            case 'add_credit_card':
                $rez = $this->sales_model->addCreditCard();
                break;
            case 'delete_credit_card':
                $rez['ok'] = $this->sales_model->deleteCreditCard();
                break;
            case 'run_card_payment':
                $result_data = $this->admin_model->parse_serialize_js($post['data']);
                $rez = $this->sales_model->cardPayment($result_data);
                break;
            case 'load_industry_variables':
                $session->delete('industry_items');
                $session->delete('industry_collection');

                $insp_model = Model::factory('Inspiration');
                $avaliable_items = $insp_model->get_types();
                $checked_industry = $this->get_request_industry($post['id']);
                if (!empty($checked_industry)) {
                    $session->set('industry_items', $checked_industry);
                }
                $collections = $this->get_all_collections();
                $item = array('checked_items' => array_keys($checked_industry), 'all_checked' => $checked_industry, 'avaliable' => $avaliable_items, 'collections' => $collections);

                $item_info = $this->print_model->item_info($post['id']);
                if (!empty($item_info)) {
                    $html_data = View::factory('block_admin/industry_edit', $item);
                } else {
                    $html_data = 'Request not found';
                }
                break;
            case 'get_industry_products':
                $insp_model = Model::factory('Inspiration');
                $items = $insp_model->get_item_by_category($post['cat_id']);
                $checked_items = $insp_model->get_default_item($post['cat_id']);
                $session->set('industry_collection', array());
                $industry_items = $session->get('industry_items');
                if (!empty($checked_items)) {
                    foreach ($checked_items as $val) {
                        $industry_items[$post['cat_id']][$val] = $val;
                    }
                }
                $session->set('industry_items', $industry_items);
                $session->set('industry_collection', array()); //clear collection
                $html_data = View::factory('block_admin/industry_products', array('items' => $items, 'checked_items' => $checked_items));
                break;
            case 'change_need_status':
                $industry_items = $session->get('industry_items');
                if (empty($post['type'])) {
                    //remove
                    unset($industry_items[$post['type_index']][$post['id']]);
                    if (empty($industry_items[$post['type_index']])) {
                        unset($industry_items[$post['type_index']]);
                    }
                } else {
                    //add
                    $industry_items[$post['type_index']][$post['id']] = $post['id'];
                }
                $session->set('industry_items', $industry_items);
                //update default checked
                if (empty($industry_items[$post['type_index']])) {
                    $industry_items[$post['type_index']] = array();
                }
                DB::sql('UPDATE is_types SET sample_checked =:sample_checked WHERE `index`=:index', array(':index' => $post['type_index'], ':sample_checked' => serialize(array_keys($industry_items[$post['type_index']]))));
                break;
            case 'delete_industry_products':
                $industry_items = $session->get('industry_items');
                unset($industry_items[$post['cat_id']]);
                $session->set('industry_items', $industry_items);
                break;
            case 'save_industry_items':
                $rez = array('samples_string' => array());
                $industry_items = $session->get('industry_items');
                $industry_collection = $session->get('industry_collection');
                if (!empty($post['req_id'])) {
                    $rez = $this->print_model->save_industry_items($industry_items, $industry_collection);
                }
                break;
            case 'create_content_block':
                $insp_model = Model::factory('Inspiration');
                $session->set('inspiration_menu', 'types');
                $data_items = $insp_model->get_items(0, 1);
                $html_data = View::factory('block_admin/industry_create_content', array('items' => $data_items));
                break;
            case 'save_new_content':
                $result_data = $this->admin_model->parse_serialize_js($post['checked']);
                $r = DB::sql('INSERT INTO request_samples_collection (title,elements) VALUES (:title,:elements)', array(':title' => $post['name'], ':elements' => serialize($result_data)));
                $rez['id'] = $r[0];
                break;
            case 'get_sample_collection':
                $collection = $this->get_one_collection($post['id']);
                $insp_model = Model::factory('Inspiration');
                $session->set('inspiration_menu', 'types');
                $data_items = $insp_model->get_items(0);
                $elements = unserialize($collection['elements']);
                $sorted_elements = array();
                foreach ($elements as $key => $val) {
                    $type_id = floor($key / 100) * 100;
                    foreach ($val as $v) {
                        $sorted_elements[$type_id][$v] = $v;
                    }
                }
                $session->set('industry_collection', $sorted_elements);
                $session->set('industry_items', array());
                $html_data = View::factory('block_admin/industry_create_content', array('items' => $data_items, 'checked' => $elements));
                break;
            case 'change_need_collection':
                $collection = $session->get('industry_collection');
                if ($post['type'] == 1) {
                    $collection[$post['type_index']][$post['id']] = $post['id'];
                } else {
                    unset($collection[$post['type_index']][$post['id']]);
                    if (empty($collection[$post['type_index']])) {
                        unset($collection[$post['type_index']]);
                    }
                }
                $session->set('industry_collection', $collection);
                break;
            case 'remove_sample_collection':
                DB::sql('DELETE FROM request_samples_collection WHERE id=:id', array(':id' => $post['id']));
                break;
            case 'save_additional_phone':
                //clear
                DB::sql('UPDATE users SET phone="", phone_ext="", phone_type="" WHERE id=:id', array(':id' => $post['uid']));
                DB::sql('DELETE FROM user_additional_info WHERE uid=:uid AND `type`="phone"', array(':uid' => $post['uid']));
                $rez['text_rez'] = ' ';

                if (!empty($post['values'])) {
                    $data_arr = array();
                    foreach ($post['values'] as $v) {
                        if (!empty($v['num'])) {
                            $data_arr[] = $v;
                        }
                    }
                    if (!empty($data_arr)) {
                        foreach ($data_arr as $k => $v) {
                            if ($k == 0) {
                                DB::sql('UPDATE users SET phone=:phone, phone_ext=:phone_ext, phone_type=:phone_type WHERE id=:id', array(':id' => $post['uid'],
                                    ':phone' => $v['num'], ':phone_ext' => $v['ext'], ':phone_type' => $v['type']));
                            } else {
                                DB::sql('INSERT INTO user_additional_info (uid,`type`,`value`, ext, content_type) VALUES (:uid, "phone", :values, :ext, :content_type)', array(
                                    ':uid' => $post['uid'], ':values' => $v['num'], ':ext' => $v['ext'], ':content_type' => $v['type']
                                ));
                            }
                            $rez['text_rez'] .= $v['num'];
                            if ($v['ext']) {
                                $rez['text_rez'] .= ' ext ' . $v['ext'];
                            }
                            $rez['text_rez'] .= '<br>';
                        }
                    }
                }
                break;
            case 'save_additional_email':

                //clear
                DB::sql('UPDATE users SET email="" WHERE id=:id', array(':id' => $post['uid']));
                DB::sql('DELETE FROM user_additional_info WHERE uid=:uid AND `type`="email"', array(':uid' => $post['uid']));
                $rez['text_rez'] = ' ';

                if (!empty($post['values'])) {
                    $data_arr = array();
                    foreach ($post['values'] as $v) {
                        if (!empty($v['email'])) {
                            $data_arr[] = $v;
                        }
                    }
                    if (!empty($data_arr)) {
                        foreach ($data_arr as $k => $v) {
                            if ($k == 0) {
                                DB::sql('UPDATE users SET email=:email WHERE id=:id', array(':id' => $post['uid'], ':email' => $v['email']));
                            } else {
                                DB::sql('INSERT INTO user_additional_info (uid,`type`,`value`) VALUES (:uid, "email", :values)', array(
                                    ':uid' => $post['uid'], ':values' => $v['email']
                                ));
                            }
                            $rez['text_rez'] .= '<a href="mailto:' . $v['email'] . '">' . $v['email'] . '</a><br>';
                        }
                    }
                }
                break;
            case 'add_card_billing':
                $rez = $this->sales_model->addCardBilling();
                break;
            case 'get_autofill_billing':
                $rez['all'] = $this->sales_model->getCardBilling($post['job_id']);
                break;
            case 'get_card_billing':
                $rez['info'] = $this->sales_model->getCardBillingDetails($post['id']);
                break;
//            case 'get_cards_list':
//                $rez['info'] = $this->sales_model->getCreditCardsJob($post['job_id']);
//                break;
            case 'get_card_details':
                $rez['info'] = $this->sales_model->getOneCreditCard($post['id']);
                break;
            case 'remove_payment_note':
                $rez['ok'] = $this->sales_model->removePaymentNote($post['id'], $this->admin['group_id']);
                break;
            case 'save_changed_info':
                $rez['ok'] = $this->sales_model->saveCardChanges();
                break;
            case 'add_new_job':
                if (!empty($post['alt_num'])) {
                    $post['job_abbr'].= $post['alt_num'];
                }
                $rez['id'] = $this->print_model->add_new_job($post);
                if (!empty($post['new'])) {
                    //update company ABBR
                    $this->print_model->update_company_abbr($post['comp_id'], $post['job_abbr']);
                }
                break;
            case 'add_new_estimate':
                $rez = $this->print_model->add_new_estimate($post['user_id']);
                break;
            case 'update_job_id':
                $this->print_model->updateNote($post['id'], $post['title']);
                break;
            case 'delete_job':
                $rez['ok'] = $this->print_model->delete_job($post['id']);
                break;
            case 'restore_note':
                $rez['ok'] = $this->print_model->restore_note($post['id']);
                break;
            case 'add_required_message':
                $rez['ok'] = $this->print_model->add_required_message($this->admin);
                break;
            case 'get_count_my_tasks':
                $rez['count'] = $this->admin_model->getMyTasks($this->admin['id']);
                $rez['count_assign'] = $this->admin_model->getAssignTasks($this->admin['id']);
                break;
            case 'check_add_user':
                $result_data = $this->admin_model->parse_serialize_js($post['data']);
                if (!empty($result_data)) {
                    $rez['counts'] = $this->print_model->check_add_user($result_data);
//                    $html_data = View::factory('block_admin/print_table', array('finded' => $finded, 'pages' => 0, 'open_page' => 0, 'for_page' => 0, 'proc' => 0));
                }
                break;
            case 'add_new_user':
                $result_data = $this->admin_model->parse_serialize_js($post['data']);
                if (!empty($result_data) && $this->print_model->add_user_validation($rez['err'], $result_data)) {
                    $rez['ok'] = $this->print_model->add_new_user($result_data);
                }
                break;
            case 'save_user_addr':
                $rez['address'] = $this->print_model->fast_save_user_address();
                break;
            case 'change_card_billing_default':
                $rez['ok'] = $this->sales_model->change_card_billing($post['id'], $post['card']);
                break;
            case 'set_data_entry_session':
                $type = array();
                if (!empty($post['type'])) {
                    if (is_array($post['type'])) {
                        foreach ($post['type'] as $v) {
                            $type[] = $v;
                        }
                    } else {
                        $type[] = $post['type'];
                    }
                }
                Session::instance()->set('print_entry_type', $type);
                Session::instance()->set('print_entry_date', $post['date']);
                break;
            case 'get_job_form':
                //generate job#
                $abbr = $this->print_model->generate_new_job($post['comp_id']);
                //company users
                $users = $this->user_model->get_company_users($post['comp_id']);
                $html_data = View::factory('block_admin/new_job_form', array('abbr' => $abbr, 'users' => $users, 'cid' => $post['comp_id']));
                break;
            case 'check_manual_job':
                $rez = $this->print_model->check_job_abbr($post['cid'], $post['val']);
                break;
            case 'get_jobedit_form':
                $job = $this->print_model->getJob($post['job_id']);
                //company users
                $users = $this->user_model->get_company_users($post['comp_id']);
                $html_data = View::factory('block_admin/jobedit_form', array('job' => $job, 'users' => $users, 'cid' => $post['comp_id']));
                break;
            case 'edit_one_job':
                $rez['update_user'] = 0;
                $this->print_model->updateJob($post);
                $last = DB::sql_row('SELECT * FROM user_jobs WHERE company_id=:id ORDER BY id DESC LIMIT 1', array(':id' => $post['comp_id']));
                if ($post['job_id'] == $last['id']) {
                    DB::sql('UPDATE requests SET user_id=:uid WHERE company_id=:cid', array(':cid' => $last['company_id'], ':uid' => $post['job_user']));
                    $rez['update_user'] = 1;
                }
                break;
            case 'get_fast_editform':
                $user = $this->user_model->user_additional_fields($post['uid'], $post['type']);
                $html_data = View::factory('block_admin/get_fast_editform', array('type' => $post['type'], 'user' => $user));
                break;
            case 'get_correct_job_number':
                $company = DB::sql_row('SELECT * FROM users_company WHERE id=:id', array(':id' => $post['cid']));
                if (empty($post['type'])) {
                    $post['type'] = '0';
                }
                $result = DB::sql('SELECT * FROM user_jobs WHERE company_id=:id AND job_id!="" AND job_id LIKE "' . $company['abbr'] . '-' . $post['type'] . '%"', array(':id' => $post['cid']));
                if (!empty($result)) {
                    $all_num = array();
                    foreach ($result as $v) {
                        $one = explode('-', $v['job_id']);
                        $all_num[] = preg_replace('/[A-Z]/i', '', $one[1]);
                    }
                    $max = max($all_num);
                    $num = $max + 1;
                } else {
                    $num = 1;
                }
                $rez = array('count' => $num = str_pad($num, 3, "0", STR_PAD_LEFT));
                break;
//            case 'remove_user_billing':
//                $rez = $this->user_model->remove_user_billing($post['val']);
//                break;
//            case 'save_current_billing':
//                $this->sales_model->updateCardBilling();
//                break;
            case 'same_as_shipping':
                $rez = $this->sales_model->getCardBillingDetails(0);
                break;
            case 'validate_CC':
                $rez = $this->sales_model->validCreditcard($post['number']);
                break;
            case 'get_card_shipping':
                $rez['all'] = $this->sales_model->get_card_shipping($post['id'], $post['cid']);
                break;
            case 'get_shipping_details':
                $rez['shipp'] = DB::sql_row('SELECT * FROM credit_card_shipping WHERE id=:id', array(':id' => $post['id']));
                break;
            case 'shipp_add_contact':
                $rez['id'] = $this->sales_model->shipp_add_contact();
                break;
            case 'shipp_update_contact':
                $this->sales_model->shipp_update_contact();
                $rez['ok'] = 1;
                break;
            case 'set_card_default':
                $this->sales_model->set_card_default($post['id']);
                break;
            case 'change_order_total':
                $this->sales_model->change_order_total($this->admin['id']);
                break;
            case 'eye_company_change':
                $rez['ok'] = $this->sales_model->eye_company_change($post['id'], $post['type'], $this->admin['id'], $post['req_id']);
                break;
            case 'get_credit_form':
                $pay = DB::sql_row('SELECT * FROM payment_history WHERE id=:id', array(':id' => $post['pay_id']));
                $html_data = View::factory('block_admin/credit_form', array('pay' => $pay));
                break;
            case 'run_credit':
                $rez = $this->sales_model->cardReturnPayment();
                break;
            case 'get_add_trans_form':
                $data['credit_cards'] = $this->sales_model->getCreditCards($post['uid']);
                $data['jobs'] = $this->user_model->getCompanyJobs($post['cid']);
                $data['active'] = $post['active'];
                if (!empty($data['credit_cards'])) {
                    $data['card_jobs'] = $this->sales_model->card_jobs_list($data['credit_cards']);
                }
                $data['job_info'] = $this->sales_model->getJobInfo($data['active']);
                echo View::factory('block_admin/add_transaction', $data);
                break;
            case 'add_card_transaction':
                $this->sales_model->addCardTransaction($this->admin['id']);
                break;
            case 'save_view_as':
                $rez = $this->sales_model->updateViewAs($post['id'], $post['val']);
                break;
            case 'getJobCC':
                $rez = $this->sales_model->getCreditCardsJob($post['card']);
                break;
            case 'add_cash_transaction':
                $this->sales_model->addCashTransaction($this->admin['id']);
                break;
            case 'add_misc_transaction':
                $this->sales_model->addMiscTransaction($this->admin['id']);
                break;
            case 'add_check_transaction':
                $this->sales_model->addCheckTransaction($this->admin['id']);
                break;
            case 'add_confirm_transaction':
                $this->sales_model->addConfirmTransaction($this->admin['id']);
                break;
            case 'add_failed_transaction':
                $this->sales_model->addFailedTransaction($this->admin['id']);
                break;
            case 'add_credit_transaction':
                $this->sales_model->addCreditTransaction($this->admin['id']);
                break;
            case 'edit_transaction':
                $data['credit_cards'] = $this->sales_model->getCreditCards($post['uid']);
                $data['trans'] = $this->sales_model->getTransaction($post['id']);
                $data['jobs'] = $this->user_model->getCompanyJobs($post['cid']);
                $html_data = View::factory('block_admin/edit_transaction', $data);
                break;
            case 'edit_save_transaction':
                $this->sales_model->updateCardTransaction();
                break;
            case 'remove_shipp_address':
                $this->sales_model->removeShippAddress($post['id']);
                $rez = 1;
                break;
            case 'remove_bill_address':
                $this->sales_model->removeBillAddress($post['id']);
                $rez = 1;
                break;
            case 'redistribute_payment':
                $data['payment'] = DB::sql_row('SELECT * FROM payment_history WHERE id=:id', array(':id' => $post['id']));
                if ($data['payment']) {
                    $data['jobs'] = $this->user_model->getCompanyJobs($post['cid']);
                    $data['job'] = $this->print_model->getJob($data['payment']['job_id']);
                    $data['trans'] = $post['trans'];
                    $html_data = View::factory('block_admin/redistribute_payment', $data);
                }
                break;
            case 'run_redistribute_payment':
                $this->sales_model->redestributePayment();
                break;
            case 'check_exist_job':
                $rez = $this->sales_model->check_exist_job($post['job']);
                break;
            case 'get_company_name':
                $rez = $this->sales_model->get_company_name($post['id']);
                break;
            case 'bill_save_continue':
                $rez['ok'] = 1;

                break;
            case 'bill_save_add':
                $result_data = $this->admin_model->parse_serialize_js($post['data']);
                if (!empty($result_data)) {
                    $r = DB::sql('INSERT INTO credit_card_billing (`title`, user_id, first_name, last_name, address, city, state, `zip`, country, email, phone, phone_ext, company, visible, `default`, suite, address2, full_name) 
                        VALUES (:title, :user_id, :first_name, :last_name, :address, :city, :state, :zip, :country, :email, :phone, :billing_phone_ext, :company, 0, 0, :suite, :address2, :full_name)', array(
                                ':title' => "", ':user_id' => $post['uid'], ':first_name' => $result_data['billing_fname'], ':last_name' => $result_data['billing_lname'], ':address' => $result_data['billing_address'],
                                ':city' => $result_data['billing_city'], ':state' => $result_data['billing_state'], ':zip' => $result_data['billing_zip'],
                                ':country' => $result_data['billing_country'], ':email' => $result_data['billing_email'], ':phone' => $result_data['billing_phone'], ':billing_phone_ext' => $result_data['billing_phone_ext'],
                                ':company' => $result_data['billing_company'], ':suite' => $result_data['billing_suite'], ':address2' => $result_data['billing_address2'], ':full_name' => $result_data['full_card_name']
                    ));
                }
                $result_data['id'] = $r[0];
                $rez['ok'] = $result_data;

                break;
            case 'bill_save_update':
                $result_data = $this->admin_model->parse_serialize_js($post['data']);
                if (!empty($result_data)) {
                    $where_id = $post['id'];
                    DB::sql('UPDATE credit_card_billing SET first_name=:first_name, last_name=:last_name, address=:address, address2=:address2, city=:city, state=:state, zip=:zip, country=:country, email=:email, phone=:phone, phone_ext=:phone_ext, company=:company, full_name=:full_name, suite=:suite WHERE id=:id', array(
                        ':first_name' => $result_data['billing_fname'], ':last_name' => $result_data['billing_lname'], ':address' => $result_data['billing_address'], ':address2' => $result_data['billing_address2'],
                        ':city' => $result_data['billing_city'], ':state' => $result_data['billing_state'], ':zip' => $result_data['billing_zip'], ':full_name' => $result_data['full_card_name'],
                        ':country' => $result_data['billing_country'], ':email' => $result_data['billing_email'], ':phone' => $result_data['billing_phone'], ':company' => $result_data['billing_company'],
                        ':id' => $where_id, ':suite' => $result_data['billing_suite'], ':phone_ext' => $result_data['billing_phone_ext']
                    ));
                    $rez['ok'] = $result_data;
                }
                break;
            case 'get_new_customer_form':
                $data['is_types'] = Model::factory('Inspiration')->get_types();
                $html_data = View::factory('block_admin/add_new_customer', $data);
                break;
        }
        if (!empty($rez)) {
            echo json_encode($rez);
        } elseif (!empty($html_data)) {
            echo $html_data;
        }
        exit;
    }

}
