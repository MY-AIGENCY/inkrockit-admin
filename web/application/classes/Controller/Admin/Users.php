<?php

defined('SYSPATH') or die('No direct access allowed.');

class Controller_Admin_Users extends Admin {

    private $user_model;

    public function before() {
        parent::before();
        $this->user_model = Model::factory('Admin_User');
        $this->template->scripts[] = '/js/admin/user_init.js';

    }

    public function action_index() {
        $this->data['search_val'] = $search_val = $search_group = $search_page = '';
        $session = Session::instance();
        $session_data = $session->get('user_search_last');
        if (!empty($session_data)) {
            $this->data['search_val'] = $search_val = $session_data['val'];
            $this->data['search_group'] = $search_group = $session_data['group'];
            $search_page = $session_data['page'];
        }
        $this->data['users'] = $this->user_model->get_users($search_val, $search_group, $search_page);
        $this->data['total_items'] = $this->admin_model->calcPages();
        $this->data['paginator'] = Pagination::factory(array('total_items' => $this->data['total_items'], 'items_per_page' => 100))->render();
        $this->data['user_groups'] = $this->user_model->get_user_groups();
        $this->template->content = View::factory('admin/user/all', $this->data);
    }

    public function action_del() {
        $id = $this->request->param('param1');
        $this->user_model->remove_user($id);
        $this->redirect('/admin/users');
    }

    public function action_edit() {
        $this->data['user'] = array();
        $id = $this->request->param('param1');
        $post = $this->request->post();
        $this->data['groups'] = $this->user_model->get_user_groups();
        if (!empty($post['save']) && !empty($post['login'])) {
            $this->user_model->save_user($id);
            $this->redirect('/admin/users');
        }
        if (!empty($id)) {
            $this->data['user'] = $this->user_model->get_user_info($id);
        }
        $this->template->content = View::factory('admin/user/edit', $this->data);
    }

    public function action_events() {
        $for_page = 50;
        $page = (!empty($_GET['page'])) ? $_GET['page'] - 1 : 0;
        $event_model = Model::factory('Admin_Event');
        $print_model = Model::factory('Admin_Print');
        $this->data['events'] = $event_model->get_events($page, $for_page);
        $all = $print_model->calcPages();
        $this->data['pages'] = Pagination::factory(array('total_items' => $all, 'items_per_page' => $for_page))->render();

        $this->template->content = View::factory('admin/user/events', $this->data);
    }

    public function action_email(){
        $post = $this->request->post();
        if(!empty($post)){
            DB::sql('UPDATE settings SET val=:v WHERE `key`="request_email_template"', array(':v'=>$post['request_email_template']));
        }
        $this->data['email_template'] = DB::sql_row('SELECT val FROM settings WHERE `key`="request_email_template"');
        $this->template->content = View::factory('admin/user/email_tempalte', $this->data);
    }

    public function action_ajax() {
        $post = $this->request->post();
        switch ($post['func']) {
            case 'change_user_status':
                $this->user_model->change_comment_status($post['uid'], $post['type'], $post['val']);
                break;
            case 'event_details':
                $event_model = Model::factory('Admin_Event');
                $data = $event_model->event_details($post['id']);
                $html_data = View::factory('admin/user/event_details', array('data' => $data));
                break;
            case 'search_user':
                $session = Session::instance();

                $this->data['users'] = $this->user_model->get_users($post['val'], $post['group'], $post['page']);
                $this->data['total_items'] = $this->admin_model->calcPages();
                $this->data['paginator'] = Pagination::factory(array('total_items' => $this->data['total_items'], 'items_per_page' => 100))->render();
                $this->data['user_groups'] = $this->user_model->get_user_groups();
                $html_data = View::factory('block_admin/users_table', $this->data);

                $session->set('user_search_last', array(
                    'val' => $post['val'],
                    'group' => $post['group'],
                    'page' => $post['page']
                ));
                break;
            case 'get_company_users':
                $print_model = Model::factory('Print');
                $req = $print_model->item_info($post['req_id']);
                if (!empty($req)) {
                    $users = $this->user_model->get_company_users($req['company_id']);
                    $job_data = array();
                    if(!empty($post['job'])){
                        $job_data = DB::sql_row('SELECT * FROM user_jobs WHERE id=:id', array(':id'=>$post['job']));
                        $checked_user = $job_data['user_id'];
                    }else{
                        $checked_user = $req['user_id'];
                    }
                    $html_data = View::factory('block_admin/company_users', array('users' => $users, 'checked'=> $checked_user, 'job'=>$job_data));
                }
                break;
            case 'get_userdata':
                if(!empty($post['jobinfo_id'])){
                    if($post['jobinfo_id'] == 'main'){
                        $user = DB::sql_row('SELECT user_id FROM requests WHERE id=:req_id', array(':req_id'=>$post['req_id']));
                        $post['user_id'] = $user['user_id'];
                    }else{
                        $user = DB::sql_row('SELECT user_id FROM user_jobs WHERE id=:id', array(':id'=>$post['jobinfo_id']));
                        $post['user_id'] = $user['user_id'];
                    }
                }
                
                $rez = $this->user_model->get_user_info($post['user_id']);
                if(!empty($post['set_main'])){
                    $print_model = Model::factory('Print');
                    $req = $print_model->item_info($post['req_id']);
                    if(!empty($post['job_id'])){
                        $this->user_model->job_main_user($post['job_id'], $post['user_id']);
                        //if last job - update main user
                        $last = DB::sql_row('SELECT * FROM user_jobs WHERE company_id=(SELECT company_id FROM requests WHERE id="'.$post['req_id'].'") ORDER BY id DESC LIMIT 1');
                        if($last['id'] == $post['job_id']){
                            $this->user_model->request_main_user($post['req_id'], $post['user_id']);
                        }  
                    }else{
                        $this->user_model->request_main_user($post['req_id'], $post['user_id']);
                    }
                }
                break;
            case 'add_company_user':
                $rez['id'] = $this->user_model->add_company_user();
                break;
            case 'remove_user':
                $this->user_model->remove_user($post['id']);
                break;
            case 'edit_user_form':
                $user = $this->user_model->get_user_info($post['id']);
                $html_data = View::factory('block_admin/edit_user_form', array('user'=>$user));
                break;
            case 'edit_user_update':
                $this->user_model->user_fast_update();
                break;
            case 'get_user_data':
                $uid = $this->user_model->get_user_job($post['job_id']);
                $rez = $this->user_model->get_user_info($uid);
                break;
            case 'save_shipping_info':
                $uid = $this->user_model->get_user_job($post['job_id']);
                $this->user_model->user_fastship_update($uid);
                
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