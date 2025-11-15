<?php

defined('SYSPATH') or die('No direct access allowed.');

class Controller_Admin_Index extends Admin {

    public function action_index() {
        if (empty($this->admin)) {
            $this->redirect('admin/login');
        } else {
            //First page
            $this->redirect('/admin/sales/all');
        }
    }

    public function action_login() {
        if (!empty($this->admin)) {
            $this->redirect('admin/index');
        } else {
            if ($this->admin_model->check_login($this->request->post('login'), $this->request->post('pass'))) {
                $this->redirect('admin');
            }
            $this->template->content = View::factory('admin/login');
        }
    }

    public function action_logout() {
        if (!empty($this->admin)) {
            Session::instance()->destroy();
            Cookie::delete('admin_user');
        }
        $this->redirect('admin');
    }

}