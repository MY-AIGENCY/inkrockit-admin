<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Account extends Main {

    public function action_index() {

        $this->template->content = View::factory('pages/Account/index', $this->data);
    }

    public function action_orders() {
        $this->data['param'] = $this->request->param('id');
        if ($this->request->param('id') == 'active_orders') {
            $tpl = 'active_orders';
        } elseif ($this->request->param('id') == 'history_orders') {
            $tpl = 'history_orders';
        } elseif ($this->request->param('id') == 'payment_info') {
            $tpl = 'payment_info';
        } elseif ($this->request->param('id') == 'new_card') {
            if (!empty($_POST['cansel'])) {
                $this->redirect('/account/payment_info');
            }
             if (!empty($_POST['save'])) {
                $this->redirect('/account/payment_info');
            }
            $tpl = 'new_card';
        }
        $this->template->content = View::factory('pages/Account/' . $tpl, $this->data);
    }
}