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
            $error = NULL;
            $trace_id = bin2hex(random_bytes(8));
            $this->response->headers('X-Login-Trace-Id', $trace_id);
            if ($this->request->method() === HTTP_Request::POST) {
                $login = $this->request->post('login');
                $pass = $this->request->post('pass');

                if (empty($login) || empty($pass)) {
                    $error = 'Please enter your username (or email) and password.';
                } elseif ($this->admin_model->check_login($login, $pass)) {
                    $this->redirect('admin');
                } else {
                    // Keep messaging generic for security; most common causes:
                    // - wrong username/email, wrong password
                    // - user is not in an admin-capable group (group_id must be >= 2)
                    $error = 'Invalid login, password, or insufficient permissions.';
                    error_log("admin_login_failed trace_id={$trace_id} login=" . substr((string) $login, 0, 3) . "…");
                }
            }

            $this->template->content = View::factory('admin/login')
                ->set('error', $error);
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