<?php

defined('SYSPATH') or die('No direct access allowed.');

class Controller_Admin_Version extends Admin {

    public function action_index() {
        global $global_str;
        if (empty($global_str)) {
            $global_str = array();
        }

        $this->print_model = Model::factory('Admin_Print');
        $this->sales_model = Model::factory('Admin_Sales');
        $this->user_model = Model::factory('Admin_User');

        $post = $this->request->post();
        //ppt
        if (!empty($post['bug_add'])) {
            $this->data['add_rez'] = Ukieapi::post_bug($post['title'], $post['text'], $post['priority'], $post['deadline']);
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        $this->data['bugs'] = Ukieapi::get_bugs();
        $this->template->content = View::factory('admin/version/index', $this->data);
    }

    public function action_update() {

        $all = DB::sql('SELECT * FROM payment_history WHERE `type`!="failed" AND `type`!="order_confirmed" AND `type`!="change_total"');
        $jobs = array();
        foreach ($all as $v) {
            if (empty($jobs[$v['job_id']])) {
                $jobs[$v['job_id']] = 0;
            }

            switch ($v['type']) {
                case 'credit':
                case 'redistribute':
                    $jobs[$v['job_id']] -= $v['summ'];
                    break;
                default:
                    $jobs[$v['job_id']] += $v['summ'];
            }
        }


        foreach($jobs as $job_id=>$summ){
            DB::sql('UPDATE user_jobs SET payments=:p WHERE id=:id', array(':p'=>$summ, ':id'=>$job_id));
        }

        exit;
    }

}
