<?php

defined('SYSPATH') or die('No direct script access.');

class Main extends Controller_Template {

    public $template = 'main', $data;

    public function before() {
        parent::before();
        $this->template->scripts = array('plugins', 'main');
        $this->template->styles = array('normalize','style', 'design', 'print_it', 'order_confirmation', 'account', 'mail_it', 'print_it_home', 'sample_pack', 'why_inkrockit');
        $this->template->content = '';
        if (!empty($_POST['go_to_checkout'])) {
            $this->redirect('/print_it/checkout');
        }
        $print_model = Model::factory('Print');
        $data['active_menu'] = $this->request->controller();
        
        $session = Session::instance();
        $data['inspiration_menu'] = $session->get('inspiration_menu');
        $data['print_menu'] = $print_model->get_cats();
        
        $this->template->main_menu = View::factory('block/main_menu', $data);
        $this->template->basket = View::factory('block/basket');
        $this->data = array();
    }

}

