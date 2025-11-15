<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_User extends Main {

    public $post, $message = null, $data;

    public function before() {
        parent::before();
        $this->post = $this->request->post();
    }

    public function action_index() {
        
    }

    public function action_registration() {
        $captcha = Captcha::instance();
        if ($this->post) {
            Model::factory('User')->check_registration_form($this->message);
            if (empty($this->message)) {
                Model::factory('User')->user_register();
                $this->data['reg_ok'] = 1;
            }
        }
        $this->template->content = View::factory('pages/registration', $this->data)->set('captcha', $captcha->render())
                        ->bind('message', $this->message)->bind('post', $this->post);
    }

    public function action_login() {
        if (!empty($this->post)) {
            $user = DB::sql_row('SELECT * FROM users WHERE login = "' . $this->post['login'] . '" AND password = "' . md5($this->post['password']) . '"');
            if (!empty($user)) {
                Session::instance()->set('user', $user['login']);
                if ($user['group_id'] >= 2) {
                    Cookie::set('admin_user',serialize($user));
                    $this->redirect('/admin');
                }
            } else {
                $this->template->content = 'Incorrect Login';
            }
        }
    }

    public function action_logout() {
        Session::instance()->destroy();
        Cookie::delete('admin_user');
        $this->redirect('/');
    }

}

?>
