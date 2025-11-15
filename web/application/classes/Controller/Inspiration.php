<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Inspiration extends Main {

    public function action_index() {
        $id = $this->request->param('id');
        $this->template->scripts[] = 'inspiration.init';

        if (!empty($id)) {

//            chdir('application/files/items/pics/');
//            foreach (glob("*.png") as $filename) {
//                $files[] = $filename;
//            }
//            sort($files,SORT_NUMERIC);
//            foreach($files as $val){
//                $this->data['gallery'][intval($val)][] = $val;
//            }
            
            $this->data['gallery'] = Model::factory('Inspiration')->get_items(0);
            
            $this->data['active'] = $id;
            $this->template->content = View::factory('pages/inspiration_gallery', $this->data);
        } else {
            $this->template->content = View::factory('pages/inspiration_home');
        }
    }
    
    

}