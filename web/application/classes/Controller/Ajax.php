<?php
class Controller_Ajax extends Controller{
    
    public $post;
    private $table_is = array('items' => 'is_items', 'items_img' => 'is_items_images');
    
    public function before() {
        parent::before();
        $this->post = Request::initial()->post();
        $is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
        if(!$is_ajax) exit;
    }
    
    public function action_index(){
        $fn = $this->post['fn'];
        if(method_exists($this, $fn)){
            $this->$fn();
        }else{
            exit;
        }
    }
    
    public function delete_item_img(){
        $img = $this->post['img'];
        DB::sql('DELETE FROM '.$this->table_is['items_img'].' WHERE img="'.$img.'"');
        $pics_path = APPPATH.'files/items/pics/';
        $thumb_path = APPPATH.'files/items/thumbnails/';
        @unlink($pics_path.$img.'.png');
        @unlink($thumb_path.$img.'.png');
    }
    
    public function inspMenuShowBy(){
        $by = $this->post['by'];
        $session = Session::instance();
        $session->set('inspiration_menu', $by);
        
        $inspMenu = Model::factory('Inspiration')->get_items_by($by);
        echo View::factory('block/inspMenu')->set('inspMenu', $inspMenu);
        
    }
    
    public function getLeftItemInfo(){
        $item_id = $this->post['item_id'];
        $this->data['type_title'] = Model::factory('Inspiration')->getTypeTitle($item_id);
        $this->data['blocks'] = array();
        echo View::factory('block/leftItemInfo',$this->data);
    }
    
    public function saveItemChildBlocks(){
        Model::factory('Inspiration')->saveItemChildBlocks($this->post['block_id'],  $this->post['child_blocks']);
    }
    
    public function deleteItemBlock(){
        if(!empty($this->post['block_id'])){
            Model::factory('Inspiration')->deleteItemBlock($this->post['block_id']);
        }
    }
    public function saveItemBlock(){
        Model::factory('Inspiration')->saveItemBlock($this->post['item_id'],$this->post['block_id'],$this->post['title'],$this->post['text']);
    }
    public function deleteItemChild(){
        if(!empty($this->post['block_id'])){
            Model::factory('Inspiration')->deleteItemChild($this->post['block_id']);
        }
    }
    
}
?>
