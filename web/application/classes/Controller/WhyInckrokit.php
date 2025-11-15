<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_WhyInckrokit extends Main {

    public function action_index() {
        $this->template->scripts[] = 'home.init';
        $this->template->content = View::factory('pages/why_inkrockit', $this->data);
        $this->template->active_menu = $this->request->param('param');
    }

}