<?php

defined('SYSPATH') or die('No direct access allowed.');

class Controller_Admin_Print extends Admin {

    public function before() {
        parent::before();
        $this->print_model = Model::factory('Admin_Print');
        $this->sales_model = Model::factory('Admin_Sales');
        $this->data['ajax'] = $this->request->is_ajax();
        $this->template->scripts[] = '/js/admin/print.init.js';
    }

    public function action_products() {
        if (empty($this->tpl)) {
            $this->action_shipping();
            $data['active'] = '/admin/print/shipping';
        } else {
            $data['active'] = $_SERVER['REQUEST_URI'];
        }
        $data['content'] = View::factory($this->tpl, $this->data);
        $this->template->content = View::factory('admin/print/product_properties', $data);
    }

    public function action_items() {
        $param = $this->request->param('param1');
        switch ($param) {
            case 'edit':
                $this->data['packs'] = $this->print_model->get_item_packs();
                $all_packs = $this->print_model->get_pockets();
                $this->data['all_packs'] = array();
                foreach ($all_packs as $val) {
                    $this->data['all_packs'][$val['type']][] = $val;
                }
                $this->data['item_slits'] = $this->print_model->get_item_slits();
            case 'add':
                $this->data['papers'] = $this->print_model->get_papers();
                $this->data['inks'] = $this->print_model->get_inks();
                $this->data['categories'] = $this->print_model->get_cats();
                $this->data['coating'] = $this->print_model->get_coating();
                $this->data['finishes'] = $this->print_model->get_coating('print_finishes');
                $this->data['sticked'] = $this->print_model->get_sticked();
                $this->data['slits'] = $this->print_model->getSlits();
                $this->data['proof'] = $this->print_model->getProofs();
                $this->data['foldings'] = $this->print_model->get_foldings();

                if ($param == 'add') {
                    $tpl = 'print/add_item';
                    if ($this->request->post('add_item')) {
                        if ($this->print_model->save_item()) {
                            $this->redirect('/admin/print/items/');
                        }
                    }
                } elseif ($param == 'edit') {
                    if ($this->request->post('add_item') || $this->request->post('save_next') || $this->request->post('save_prev')) {
                        $id = Request::initial()->param('param2');
                        $this->print_model->save_item($id);
                        $url = '';
                        if ($this->request->post('save_next')) {
                            $next = $this->print_model->getNextPrintItem($id);
                            if (!empty($next)) {
                                $url = 'edit/' . $next;
                            }
                        } elseif ($this->request->post('save_prev')) {
                            $prev = $this->print_model->getPrevPrintItem($id);
                            if (!empty($prev)) {
                                $url = 'edit/' . $prev;
                            }
                        }
                        $this->redirect('/admin/print/items/' . $url);
                    }
                    $this->data['item'] = $this->print_model->get_item();
                    $this->data['item_prices'] = $this->print_model->get_item_prices($this->data['item']['id']);
                    $this->data['item_dimentions'] = $this->print_model->get_item_dimentions($this->data['item']['id']);
                    $tpl = 'print/add_item';
                }
                break;
            case 'del':
                $this->print_model->del_item();
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            default :
                if (empty($param)) {
                    $this->data['category'] = $this->print_model->get_cats();
                    $tpl = 'print/items_category';
                } else {
                    $this->data['category'] = $this->print_model->get_cat($param);
                    $this->data['items'] = $this->print_model->get_items($param);
                    $tpl = 'print/items';
                }
        }
        $this->template->content = View::factory('admin/' . $tpl, $this->data);
    }

    public function action_category() {
        $param = $this->request->param('param1');
        switch ($param) {
            case 'add':
                if ($this->request->post('add_cat')) {
                    if ($this->check(array('title'))) {
                        if ($this->print_model->add_cat()) {
                            $this->redirect('/admin/Print/category');
                        }
                    }
                }
                $tpl = 'print/add_cat';
                break;
            case 'del':
                $this->print_model->del_cat();
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('add_cat')) {
                    if ($this->check(array('title'))) {
                        $this->print_model->save_cat();
                        $this->redirect('/admin/Print/category');
                    }
                }
                $this->data['cat'] = $this->print_model->get_cat();
                $tpl = 'print/add_cat';
                break;
            default :
                $this->data['cats'] = $this->print_model->get_cats();
                $tpl = 'print/cats';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_folding() {
        $param = $this->request->param('param1');
        //types
        $this->data['folding_types'] = array();
        $this->data['cat'] = $this->print_model->get_cats();
        switch ($param) {
            case 'add':
                if ($this->request->post('add_folding')) {
                    if ($this->check(array('title'))) {
                        if ($this->print_model->add_folding()) {
                            $this->redirect('/admin/Print/folding');
                        }
                    }
                }
                $tpl = 'print/add_folding';
                break;
            case 'del':
                $this->print_model->del_folding();
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('add_folding')) {
                    if ($this->check(array('title'))) {
                        $this->print_model->save_folding();
                        $this->redirect('/admin/Print/folding');
                    }
                }
                $this->data['folding'] = $this->print_model->get_folding();
                $this->data['folding_types'] = $this->print_model->get_folding_types();
                $tpl = 'print/add_folding';
                break;
            default :
                $this->data['foldings'] = $this->print_model->get_foldings();
                $tpl = 'print/folding';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_pockets() {
        $param = $this->request->param('param1');
        switch ($param) {
            case 'add':
                if ($this->request->post('add_pocket')) {
                    if ($this->check(array('title'))) {
                        if ($this->print_model->add_pocket()) {
                            $this->redirect('/admin/Print/pockets');
                        }
                    }
                }
                $tpl = 'print/add_pocket';
                break;
            case 'del':
                $this->print_model->del_pocket();
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('add_pocket')) {
                    if ($this->check(array('title'))) {
                        $this->print_model->save_pocket();
                        $this->redirect('/admin/Print/pockets');
                    }
                }
                $this->data['pocket'] = $this->print_model->get_pocket();
                $tpl = 'print/add_pocket';
                break;
            default :
                $this->data['pockets'] = $this->print_model->get_pockets();
                $tpl = 'print/pockets';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_slits() {
        $param = $this->request->param('param1');
        $id = $this->request->param('param2');
        switch ($param) {
            case 'add':
                if ($this->request->post('save_slits')) {
                    if ($this->check(array('name'))) {
                        if ($this->print_model->addSlits()) {
                            $this->redirect('/admin/print/slits');
                        }
                    }
                }
                $tpl = 'print/add_slits';
                break;
            case 'del':
                $this->print_model->delSlit($id);
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('save_slits')) {
                    if ($this->check(array('name'))) {
                        $this->print_model->saveSlit($id);
                        $this->redirect('/admin/print/slits');
                    }
                }
                $this->data['slit'] = $this->print_model->getSlit($id);
                $tpl = 'print/add_slits';
                break;
            default :
                $this->data['slits'] = $this->print_model->getSlits();
                $tpl = 'print/slits';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_proof() {
        $param = $this->request->param('param1');
        $id = $this->request->param('param2');
        switch ($param) {
            case 'add':
                if ($this->request->post('save_proofs')) {
                    if ($this->check(array('name'))) {
                        if ($this->print_model->addProof()) {
                            $this->redirect('/admin/print/proof');
                        }
                    }
                }
                $tpl = 'print/add_proof';
                break;
            case 'del':
                $this->print_model->delProof($id);
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('save_proofs')) {
                    if ($this->check(array('name'))) {
                        $this->print_model->saveProof($id);
                        $this->redirect('/admin/print/proof');
                    }
                }
                $this->data['proof'] = $this->print_model->getProof($id);
                $tpl = 'print/add_proof';
                break;
            default :
                $this->data['proofs'] = $this->print_model->getProofs();
                $tpl = 'print/proof';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_papers() {
        $param = $this->request->param('param1');
        $id = $this->request->param('param2');
        switch ($param) {
            case 'add':
                if ($this->request->post('save_paper')) {
                    if ($this->check(array('name'))) {
                        if ($this->print_model->addPaper()) {
                            $this->redirect('/admin/print/papers');
                        }
                    }
                }
                $tpl = 'print/add_paper';
                break;
            case 'del':
                $this->print_model->delPaper($id);
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('save_paper')) {
                    if ($this->check(array('name'))) {
                        $this->print_model->savePaper($id);
                        $this->redirect('/admin/print/papers');
                    }
                }
                $this->data['paper'] = $this->print_model->getPaper($id);
                $this->data['paper_price'] = $this->print_model->get_paper_price($this->data['paper']['id']);
                $tpl = 'print/add_paper';
                break;
            default:
                $this->data['papers'] = $this->print_model->get_papers();
                $tpl = 'print/paper';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_inks() {
        $param = $this->request->param('param1');
        $id = $this->request->param('param2');
        switch ($param) {
            case 'add':
                if ($this->request->post('save_ink')) {
                    if ($this->check(array('name'))) {
                        if ($this->print_model->addInks()) {
                            $this->redirect('/admin/print/inks');
                        }
                    }
                }
                $tpl = 'print/add_ink';
                break;
            case 'del':
                $this->print_model->delInk($id);
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('save_ink')) {
                    if ($this->check(array('name'))) {
                        $this->print_model->saveInk($id);
                        $this->redirect('/admin/print/inks');
                    }
                }
                $this->data['ink'] = $this->print_model->getInk($id);
                $tpl = 'print/add_ink';
                break;
            default :
                $this->data['inks'] = $this->print_model->get_inks();
                $tpl = 'print/inks';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_coating($table = 'print_coating') {
        $this->data['page'] = (empty($this->data['page'])) ? 'coating' : $this->data['page'];
        $param = $this->request->param('param1');
        switch ($param) {
            case 'add':
                if ($this->request->post('add_coat')) {
                    if ($this->check(array('title'))) {
                        if ($this->print_model->add_coat($table)) {
                            $this->redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                }
                $tpl = 'print/add_coat';
                break;
            case 'del':
                $this->print_model->del_coat($table);
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('add_coat') || $this->request->post('save_next') || $this->request->post('save_prev')) {
                    if ($this->check(array('title'))) {
                        $id = Request::initial()->param('param2');
                        $this->print_model->save_coat($id, $table);
                        $url = '';
                        if ($this->request->post('save_next')) {
                            $next = $this->print_model->getNextCoat($id, $table);
                            if (!empty($next)) {
                                $url = 'edit/' . $next;
                            }
                        } elseif ($this->request->post('save_prev')) {
                            $prev = $this->print_model->getPrevCoat($id, $table);
                            if (!empty($prev)) {
                                $url = 'edit/' . $prev;
                            }
                        }
                        $this->redirect('/admin/print/' . $this->data['page'] . '/' . $url);
                    }
                }
                $this->data['cat'] = $this->print_model->get_coat($table);
                $this->data['coat_price'] = $this->print_model->get_coat_price($this->data['cat']['id'], $table);
                $tpl = 'print/add_coat';
                break;
            default :
                $this->data['cats'] = $this->print_model->get_coats($table);
                $tpl = 'print/coat';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_finishes() {
        $this->data['page'] = 'finishes';
        $this->action_coating('print_finishes');
    }

    public function action_shipping() {
        $param = $this->request->param('param1');
        switch ($param) {
            case 'add':
                if ($this->request->post('add_shipp')) {
                    if ($this->check(array('title'))) {
                        if ($this->print_model->add_shipp()) {
                            $this->redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                }
                $tpl = 'print/add_shipp';
                break;
            case 'del':
                $this->print_model->del_shipp();
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('add_shipp') || $this->request->post('save_next') || $this->request->post('save_prev')) {
                    if ($this->check(array('title'))) {
                        $id = Request::initial()->param('param2');
                        $this->print_model->save_shipp($id);
                        $url = '';
                        if ($this->request->post('save_next')) {
                            $next = $this->print_model->getNextShipp($id);
                            if (!empty($next)) {
                                $url = 'edit/' . $next;
                            }
                        } elseif ($this->request->post('save_prev')) {
                            $prev = $this->print_model->getPrevShipp($id);
                            if (!empty($prev)) {
                                $url = 'edit/' . $prev;
                            }
                        }
                        $this->redirect('/admin/print/shipping/' . $url);
                    }
                }
                $this->data['cat'] = $this->print_model->get_shipp();
                $this->data['shipp_price'] = $this->print_model->get_shipp_price($this->data['cat']['id']);
                $tpl = 'print/add_shipp';
                break;
            default :
                $this->data['cats'] = $this->print_model->get_shipps();
                $tpl = 'print/shipp';
        }
        $this->tpl = 'admin/' . $tpl;
        $this->action_products();
    }

    public function action_setting() {
        $this->data['settings'] = $this->print_model->get_settings();
        if ($this->request->post('save_setting')) {
            foreach ($this->data['settings'] as $val) {
                $this->print_model->save_setting($val);
            }
            $this->data['settings'] = $this->print_model->get_settings();
        }
        $this->template->content = View::factory('admin/print/settings', $this->data);
    }

    public function action_ajax() {
        $post = $this->request->post();
        switch ($post['func']) {
            case 'get_autofill_data':
                $rez['data'] = $this->print_model->get_fedex_template($post['id']);
                break;
            case 'save_fedex_template':
                $rez['id'] = $this->print_model->save_fedex_template();
                break;
            case 'reg_send_fedex':
                $items = $post['items'];
                $session = Session::instance();
                $session->set('fedex_send', $items);
                echo 'location';
                break;
            case 'calcTransit':
                $rez = $this->print_model->calculate_fedex_rate($post['data']);
                break;
            case 'fedexShipment':
                $result_data = array();
                $arr = explode('&', $post['data']);
                if (!empty($arr)) {
                    foreach ($arr as $val) {
                        $values = explode('=', $val);
                        $result_data[$values[0]] = urldecode($values[1]);
                    }
                }
                if ($result_data['type'] == 'ship') {
                    $rez = $this->print_model->send_fedex_shipment($result_data);
                } elseif ($result_data['type'] == 'pickup') {
                    $rez = $this->print_model->send_fedex_pickup($result_data);
                }
                break;
            case 'hold_location':
                $rez = $this->print_model->get_fedex_location($post['data']);
                break;
            case 'close_ship':
                $rez = $this->print_model->close_shipment($post['id'], $post['more_id']);
                break;
            case 'search_print':
                $for_page = 50;
                $alt = array();
                $new = (empty($post['val'])) ? true : false;
                $page = (!empty($_GET['page'])) ? $_GET['page'] - 1 : 0;
                $finded = $this->print_model->search_print(trim($post['val']), $for_page, $page, $new);
                $all = $this->print_model->calcPages();
                if (!empty($finded)) {
                    $alt = $this->print_model->get_additional_info($finded);
                }
                $pages = Pagination::factory(array('total_items' => $all, 'items_per_page' => $for_page, 'current_page' => array('source' => 'route', 'key' => '?page')))->render();
                $html_data = View::factory('block_admin/print_table', array('finded' => $finded, 'alt' => $alt, 'pages' => $pages, 'open_page' => $page, 'for_page' => $for_page, 'proc' => $new));

                //save query
                Session::instance()->set('print_search', trim($post['val']));
                Session::instance()->set('print_search_fields', $post['fields']);
                Session::instance()->set('print_search_date', $post['date']);
                break;
            case 'use_advanced_search':
                Session::instance()->set('disable_advanced_search', $post['val']);
                break;
            case 'add_paper':
                $rez = $this->print_model->addPaper();
                break;
            case 'add_finish':
                $rez = $this->print_model->add_coat('print_finishes');
                break;
            case 'add_coat':
                $rez = $this->print_model->add_coat('print_coating');
                break;
            case 'add_inks':
                $rez = $this->print_model->addInks();
                break;
            case 'send_edit_print':
                $items = $post['items'];
                $session = Session::instance();
                $session->set('edit_print', $items);
                $session->set('edit_print_prev', NULL);
                echo 'location';
                break;
            case 'add_category':
                $r = DB::sql('INSERT INTO `print_category` (title,active) VALUES(:title, 1)', array(':title' => $post['title']));
                $rez['ins_id'] = $r[0];
                break;
            case 'add_request_note':
                $user_mod = Model::factory('Admin_User');
                $id = $this->print_model->add_request_note($post['id']);
                $data['note'] = $this->print_model->item_one_note($id);
                $data['user'] = $user_mod->get_user_info($data['note']['user_id']);
                $html_data = View::factory('block_admin/one_note', array('val' => $data['note'], 'user' => $data['user']));
                break;
            case 'load_task_details':
                
                $user_mod = Model::factory('Admin_User');
                $data['current_job_id'] = (!empty($post['job_id'])) ? $post['job_id'] : 0;
                if (empty($data['current_job_id']) && empty($post['user_type'])) {
                    $last_job = DB::sql_row('SELECT MAX(id) id FROM user_jobs WHERE company_id=:cid', array(':cid' => $post['cid']));
                    $data['current_job_id'] = $last_job['id'];
                }
                $data['current_user_type'] = (!empty($post['user_type'])) ? $post['user_type'] : 'A';
                $data['order'] = $this->print_model->item_info($post['id'], $post['uid']);
                //eye to company
                $data['eye_company'] = $this->print_model->eye_companys($post['id']);

                //job id 
                $data['job_id'] = $user_mod->getCompanyJobs($post['cid'], $data['current_user_type']);
                $data['admin_users'] = $user_mod->getUsersInGroups(array(2, 3, 4, 5, 6));

                if (!empty($data['current_job_id']) && !empty($data['job_id'])) {
                    $data['user'] = $user_mod->get_user_info($data['job_id'][$data['current_job_id']]['user_id']);
                }else{
                    $data['user'] = $user_mod->get_user_info($post['uid']);
                }


                switch (@$post['show_block']) {
                    case 'send_email':
                        $data['tpl'] = 'task_details_email';
                        break;
                    case 'send_estimate':
                        $data['tpl'] = 'task_details_estimate';

                        break;
                    case 'send_ship_note':
                        $data['tpl'] = 'task_details_ship';

                        break;
                    case 'request_quote':
                        $data['tpl'] = 'task_details_quote';

                        break;
                    case 'request_proof':
                        $data['tpl'] = 'task_details_proof';

                        break;
                    case 'submit_order':
                        $data['tpl'] = 'task_details_order';

                        break;
                    case 'precess_cc':
                        $data['tpl'] = 'task_details_process_cc';
                        if (!empty($data['current_job_id']) && !empty($data['job_id'])) {
                            
                            //credit cards
                            $data['credit_cards'] = $this->sales_model->getCreditCards($data['user']['id']);
                            $data['payment_history'] = $this->print_model->get_payment_history($data['user']['id'], $data['current_job_id'], $data['current_user_type']);
                        } else {
                            
                            //credit cards
                            $data['credit_cards'] = $this->sales_model->getCompanyCreditCards($post['cid']);
                            $data['payment_history'] = $this->print_model->get_company_payment_history($post['cid'], $data['current_user_type']);
                        }
                        //get jobs from payment
                        if (!empty($data['credit_cards'])) {
                            $data['used_jobs'] = $this->sales_model->card_jobs_list($data['credit_cards']);
                        }
                        break;
                    default:
                        $data['tpl'] = 'task_details_notes';
                        $data['notes'] = $this->print_model->item_notes($post['cid'], $data['current_job_id'], $this->admin['group_id'], $data['current_user_type']);
                        $data['notes_required'] = $this->print_model->notes_required_info($data['notes']);
                }

                $html_data = View::factory('block_admin/task_details', $data);
                break;
            case 'save_paper':
                $this->print_model->savePaperAjax($post['id']);
                break;
            case 'save_ink':
                $this->print_model->saveAjaxInk($post['id']);
                break;
            case 'save_finish':
                $this->print_model->saveAjaxFinish($post['id']);
                break;
            case 'save_coating':
                $this->print_model->saveAjaxCoat($post['id']);
                break;
            case 'save_proof':
                $this->print_model->saveAjaxProof($post['id']);
                break;
            case 'remove_preview':
                $this->print_model->remove_preview($post['id']);
                break;
            case 'remove_act_preview':
                $this->print_model->remove_act_preview($post['id']);
                break;
            case 'remove_view':
                $this->print_model->remove_view($post['id']);
                break;
            case 'remove_psd':
                $this->print_model->remove_psd($post['id']);
                break;
            case 'print_dataentry_filter':
                $for_page = 50;
                $new = (!empty($post['new'])) ? true : false;
                $page = (!empty($_GET['page'])) ? $_GET['page'] - 1 : 0;
                $finded = $this->print_model->dataentry_filter($post['val'], $for_page, $page, $new);
                $all = $this->print_model->calcPages();
                $html_data = View::factory('block_admin/print_table', array('finded' => $finded, 'pages' => 0, 'open_page' => $page, 'for_page' => $for_page, 'proc' => $new));
                break;
            case 'load_pockets':
                $html_data = View::factory('block_admin/print_packets_table', array('data' => $post));
                break;
            case 'get_slits_table':
                $slits = $this->print_model->getSlits();
                $type = (empty($post['type'])) ? 'any' : $post['type'];
                $html_data = View::factory('block_admin/print_slits_table', array('slits' => $slits, 'num' => $post['num'], 'type' => $type));
                break;
            case 'confirm_without_req':
                $items = $post['items'];
                if (!empty($items)) {
                    foreach ($items as $val) {
                        DB::sql('UPDATE requests SET processed_date=NOW() WHERE id=:id', array(':id' => $val));
                    }
                }
                echo 'location';
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
