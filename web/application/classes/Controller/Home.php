<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Main {

    public function action_index() {
        $this->template->scripts[] = 'home.init';
        $this->template->content = View::factory('pages/index', $this->data);
    }

}