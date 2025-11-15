<?php

defined('SYSPATH') or die('No direct access allowed.');

class Model_Inspiration extends Model {

    private $table_is = array('types' => 'is_types', 'cats' => 'is_cats', 'items' => 'is_items', 'items_img' => 'is_items_images',
        'size' => 'is_item_size', 'coat' => 'is_item_coat', 'paper' => 'is_item_paper', 'finish' => 'is_item_finish');

    /*
     * Add inspiration category
     * @return true
     */
    public function add_cat() {
        $post = Request::initial()->post();
        DB::sql('INSERT INTO `' . $this->table_is['cats'] . '`(title,abbr,active) VALUES(:title,:abbr,:active)', array(':title' => $post['title'], ':active' => $post['active'], ':abbr' => $post['abbr']));
        return true;
    }

    /*
     * Add inspiration type
     * @return true
     */
    public function add_type() {
        $post = Request::initial()->post();
        DB::sql('INSERT INTO `' . $this->table_is['types'] . '`(`index`,title,active) VALUES(:index,:title,:active)', array(':index' => $post['index'], ':title' => $post['title'], ':active' => $post['active']));
        return true;
    }

    /*
     * Update inspiration category
     */
    public function save_cat($id) {
        $post = Request::initial()->post();
        DB::sql('UPDATE `' . $this->table_is['cats'] . '` SET title=:title, abbr=:abbr, active=:active WHERE id=:id', array(':id' => $id, ':title' => $post['title'], ':active' => $post['active'], ':abbr' => $post['abbr']));
    }

    /*
     * Update inspiration type
     */
    public function save_type($id) {
        $post = Request::initial()->post();
        DB::sql('UPDATE `' . $this->table_is['types'] . '` SET title=:title,`index`=:index, active=:active WHERE id=:id', array(':index' => $post['index'], ':id' => $id, ':title' => $post['title'], ':active' => $post['active']));
    }

     /*
     * Get all inspiration category
     */
    public function get_cats() {
        return DB::sql('SELECT * FROM '.$this->table_is['cats']);
    }

    /*
     * Get all inspiration types
     */
    public function get_types() {
        return DB::sql('SELECT types.*, (SELECT COUNT(1) FROM '.$this->table_is['items'].' items WHERE items.type_index = types.`index`) items FROM '.$this->table_is['types'].' types');
    }

    /*
     * Delete inspiration type
     */
    public function del_type() {
        $id = Request::initial()->param('param2');
        DB::sql('DELETE FROM `' . $this->table_is['types'] . '` WHERE id = :id', array(':id' => $id));
    }

    /*
     * Delete inspiration category
     */
    public function del_cat() {
        $id = Request::initial()->param('param2');
        DB::sql('DELETE FROM `' . $this->table_is['cats'] . '` WHERE id = :id', array(':id' => $id));
    }

    /*
     * Get one inspiration category info
     * @return (array)
     */
    public function get_cat() {
        $id = Request::initial()->param('param2');
        return DB::get_row($this->table_is['cats'], 'id=' . $id);
    }

    /*
     * Get one inspiration type
     * @return (array)
     */
    public function get_type() {
        $id = Request::initial()->param('param2');
        return DB::get_row($this->table_is['types'], 'id=' . $id);
    }

    /*
     * Get inspiration items
     * @param (int) $admin: for backend(1) or frontend(0)
     * @param (int) $all: return all(0) or active items(1)
     * @return (array)
     */
    public function get_items($admin = 1, $all = 0) {
        $rez = array();
        if ($admin) {
            $_GET['page'] = (empty($_GET['page'])) ? 1 : $_GET['page'];
            $begin = intval(@$_GET['page'] - 1) * 30;

            $sql = 'SELECT SQL_CALC_FOUND_ROWS ' . $this->table_is['items'] . '.*, ' . $this->table_is['cats'] . '.title cat_title,' . $this->table_is['cats'] . '.abbr cat_abbr, '.$this->table_is['types'].'.title type_title
                FROM ' . $this->table_is['items'] . ' 
                    LEFT JOIN ' . $this->table_is['types'] . ' ON ' . $this->table_is['types'] . '.index = ' . $this->table_is['items'] . '.type_index 
                        LEFT JOIN ' . $this->table_is['cats'] . ' ON ' . $this->table_is['cats'] . '.id = ' . $this->table_is['items'] . '.cat_id 
                WHERE ' . $this->table_is['items'] . '.cat_id IS NOT NULL  ORDER BY item_id LIMIT ' . $begin . ', 30';
            $rez['items'] = DB::sql($sql);

            $sizes = DB::sql('SELECT item_id, abbr FROM ' . $this->table_is['size'] . ' WHERE `type`="flat"');
            if (!empty($sizes)) {
                foreach ($sizes as $v) {
                    $rez['sizes'][$v['item_id']] = $v['abbr'];
                }
            }
        } else {
            $session = Session::instance();
            $inspiration_menu = $session->get('inspiration_menu');
            if (empty($inspiration_menu) || $inspiration_menu == 'types') {
                $inspiration_menu = 'type_index';
            } else {
                $inspiration_menu = 'cat_id';
            }
            $active = (empty($all))? 'WHERE ' . $this->table_is['items'] . '.active = 1' : '' ;
            $sql = 'SELECT ' . $this->table_is['items'] . '.id,' . $this->table_is['items'] . '.title, ' . $this->table_is['items'] . '.item_id, ' . $this->table_is['items_img'] . '.img,  ' . $this->table_is['types'] . '.title type_title
                FROM ' . $this->table_is['items'] . ' 
                    LEFT JOIN ' . $this->table_is['types'] . ' ON ' . $this->table_is['types'] . '.index = ' . $this->table_is['items'] . '.type_index 
                    LEFT JOIN ' . $this->table_is['items_img'] . ' ON ' . $this->table_is['items_img'] . '.item_id = ' . $this->table_is['items'] . '.item_id 
                 ORDER BY is_items.' . $inspiration_menu . ', is_items_images.img';
            $items = DB::sql($sql);
            foreach ($items as $item) {
                $rez[$item['item_id']][] = $item;
            }
        }
        return $rez;
    }

    /*
     * Get type title
     * @param (int) $item_id: item id
     * @return (string) title
     */
    public function getTypeTitle($item_id) {
        $item_index = floor($item_id / 100) * 100;
        $type = DB::sql_row('SELECT title FROM ' . $this->table_is['types'] . ' WHERE `index` = ' . $item_index);
        return $type['title'];
    }

    /*
     * Get items in category
     * @param (int) $type_id: type
     */
    public function get_item_by_category($type_id){
        return DB::sql('SELECT * FROM ' . $this->table_is['items'] . ' WHERE type_index =:type_id', array(':type_id'=>$type_id));
    }
    
    /*
     * Get one item details
     * @param (int) $item_id: item id
     * @return (array)
     */
    public function get_item($item_id) {
        $item = DB::sql_row('SELECT ' . $this->table_is['items'] . '.*, `' . $this->table_is['types'] . '`.title type_title FROM `' . $this->table_is['items'] . '` LEFT JOIN ' . $this->table_is['types'] . ' ON ' . $this->table_is['items'] . '.type_index = ' . $this->table_is['types'] . '.index WHERE `' . $this->table_is['items'] . '`.item_id = :item_id', array(':item_id' => $item_id));
        if (!empty($item)) {
            $item['imgs'] = DB::sql('SELECT img FROM `' . $this->table_is['items_img'] . '` WHERE item_id = ' . $item['item_id'] . ' ORDER BY img');
            $size = DB::sql('SELECT * FROM `' . $this->table_is['size'] . '` WHERE item_id=:item_id', array(':item_id' => $item['item_id']));
            if (!empty($size)) {
                foreach ($size as $val) {
                    $item['size'][$val['type']] = $val;
                }
            }
        } else {
            HTTP::redirect('/admin/inspiration/items');
        }
        return $item;
    }

    /*
     * Get next item id
     * @param (int) $cur_id: current item id
     * @return (int)
     */
    public function get_next_item($cur_id) {
        $item_inf = DB::sql_row('SELECT item_id FROM (SELECT * FROM ' . $this->table_is['items'] . ' ORDER BY item_id) items WHERE item_id>' . $cur_id . ' LIMIT 1');
        return $item_inf['item_id'];
    }

    /*
     * Get prev item id
     * @param (int) $cur_id: current item id
     * @return (int)
     */
    public function get_prev_item($cur_id) {
        $item_inf = DB::sql_row('SELECT item_id FROM (SELECT * FROM ' . $this->table_is['items'] . ' ORDER BY item_id DESC) items WHERE item_id<' . $cur_id . ' LIMIT 1');
        return $item_inf['item_id'];
    }

    /*
     * Get new items
     * @return (array)
     */
    public function get_new_item() {
        $item = DB::sql_row('SELECT ' . $this->table_is['items'] . '.*, `' . $this->table_is['types'] . '`.title type_title FROM `' . $this->table_is['items'] . '` LEFT JOIN ' . $this->table_is['types'] . ' ON ' . $this->table_is['items'] . '.type_index = ' . $this->table_is['types'] . '.index WHERE `' . $this->table_is['items'] . '`.cat_id = 0 ORDER BY item_id');
        if (!empty($item)) {
            $item['imgs'] = DB::sql('SELECT img FROM `' . $this->table_is['items_img'] . '` WHERE item_id = ' . $item['item_id']);
        } else {
            HTTP::redirect('/admin/inspiration/items');
        }
        return $item;
    }

    /*
     * Remove item
     * @param (int) $id: item id
     */
    public function remove_item($id) {
        $pics_path = APPPATH . 'files/items/pics/';
        $thumb_path = APPPATH . 'files/items/thumbnails/';
        $item = DB::get_row($this->table_is['items'], 'id=' . $id);
        $item_images = DB::get($this->table_is['items_img'], 'item_id=' . $item['item_id']);
        DB::sql('DELETE FROM ' . $this->table_is['items'] . ' WHERE id = :id', array(':id' => $id));
        DB::sql('DELETE FROM ' . $this->table_is['items_img'] . ' WHERE item_id = :item_id', array(':item_id' => $item['item_id']));
        foreach ($item_images as $img) {
            @unlink($pics_path . $img['img'] . '.png');
            @unlink($thumb_path . $img['img'] . '.png');
        }
    }

    /*
     * Update inspiration item
     * @param (int) $id: item id
     */
    public function save_item($id) {
        $data = Request::initial()->post();
        DB::sql('UPDATE ' . $this->table_is['items'] . ' SET title=:title, client_item=:client, active=:active, cat_id=:cat, `count`=:count, job_id=:job_id, weight=:weight, comment=:comment 
            WHERE id = :id', array(':title' => $data['title'], ':client' => $data['client_item'], ':active' => $data['active'], ':cat' => $data['category'], ':weight' => $data['weight'], ':id' => $id, ':count' => $data['count'], ':job_id' => $data['job_id'], ':comment' => $data['comment']));

        $item = DB::sql_row('SELECT item_id FROM ' . $this->table_is['items'] . ' WHERE id=:id', array(':id' => $id));
        $item_id = $item['item_id'];

        //size
        $sizes = array('open', 'flat');
        $count_sizes = DB::sql('SELECT 1 FROM `' . $this->table_is['size'] . '` WHERE item_id=:item_id', array(':item_id' => $item_id));
        if (!empty($count_sizes)) {
            foreach ($sizes as $val) {
                DB::sql('UPDATE ' . $this->table_is['size'] . ' SET 
                description=:description, abbr=:abbr, width=:width, height=:height
                WHERE item_id=:item_id AND type=:type', array(':item_id' => $item_id, ':type' => $val, ':description' => $data['dim_descr'][$val], ':abbr' => $data['dim_abbr'][$val], ':width' => $data['dim_width'][$val], ':height' => $data['dim_height'][$val]));
            }
        } else {
            foreach ($sizes as $val) {
                DB::sql('INSERT INTO `' . $this->table_is['size'] . '` (item_id,`type`,description, abbr, width, height) 
                    VALUES (:item_id,:type,:description,:abbr,:width,:height)', array(':item_id' => $item_id, ':type' => $val, ':description' => $data['dim_descr'][$val], ':abbr' => $data['dim_abbr'][$val], ':width' => $data['dim_width'][$val], ':height' => $data['dim_height'][$val]));
            }
        }

        $finish_line = array();

        //paper
        DB::sql('DELETE FROM `' . $this->table_is['paper'] . '` WHERE item_id=:item_id', array(':item_id' => $item_id));
        if (!empty($data['use_paper'])) {
            $papers_line = array();
            foreach ($data['use_paper'] as $val) {
                DB::sql('INSERT INTO `' . $this->table_is['paper'] . '` (item_id,paper_id) VALUES (:item_id,:paper_id)', array(':item_id' => $item_id, ':paper_id' => $val));

                //create finish line
                $paper = DB::sql_row('SELECT name FROM print_papers WHERE id=:id', array(':id' => $val));
                if (!empty($paper)) {
                    $papers_line[] = $paper['name'];
                }
            }
            if (!empty($papers_line)) {
                $finish_line[] = implode(' + ', $papers_line);
            }
        }

        //coat
        DB::sql('DELETE FROM `' . $this->table_is['coat'] . '` WHERE item_id=:item_id', array(':item_id' => $item_id));
        if (!empty($data['coating_aval'])) {
            $coat_line = array();
            foreach ($data['coating_aval'] as $val) {
                DB::sql('INSERT INTO `' . $this->table_is['coat'] . '` (item_id,coat_id) VALUES (:item_id,:coat_id)', array(':item_id' => $item_id, ':coat_id' => $val));

                //create finish line
                $coat = DB::sql_row('SELECT abbr FROM print_coating WHERE id=:id', array(':id' => $val));
                if (!empty($coat) && !empty($coat['abbr'])) {
                    $coat_line[] = $coat['abbr'];
                }
            }
            if (!empty($coat_line)) {
                $finish_line[] = implode(' + ', $coat_line);
            }
        }

        //finish
        DB::sql('DELETE FROM `' . $this->table_is['finish'] . '` WHERE item_id=:item_id', array(':item_id' => $item_id));
        if (!empty($data['finishes_aval'])) {
            $finishes_line = array();
            foreach ($data['finishes_aval'] as $val) {
                DB::sql('INSERT INTO `' . $this->table_is['finish'] . '` (item_id,finish_id) VALUES (:item_id,:finish_id)', array(':item_id' => $item_id, ':finish_id' => $val));

                //create finish line
                $coat = DB::sql_row('SELECT abbr FROM print_finishes WHERE id=:id', array(':id' => $val));
                if (!empty($coat) && !empty($coat['abbr'])) {
                    $finishes_line[] = $coat['abbr'];
                }
            }
            if (!empty($finishes_line)) {
                $finish_line[] = implode(' + ', $finishes_line);
            }
        }

        //update finish line
        DB::sql('UPDATE ' . $this->table_is['items'] . ' SET finish_line=:finish_line WHERE id=:id', array(':id' => $id, ':finish_line' => implode('<br>', $finish_line)));
    }

    /*
     * Get items by criteria for menu
     * @param (string) $by: filter field
     * @return (array)
     */
    public function get_items_by($by) {
        if ($by == 'cats') {
            $key_items = 'cat_id';
            $key_by = 'id';
            $table = $this->table_is['cats'];
        } else {
            $key_items = 'type_index';
            $key_by = 'index';
            $table = $this->table_is['types'];
        }
        /* get rozdils */
        $rodilu = DB::sql('SELECT title,`' . $key_by . '` FROM `' . $table . '` WHERE active=1');
        $menu = array();

        $i = 0;
        foreach ($rodilu as $r) {
            $menu[$i]['title'] = $r['title'];
            $menu[$i]['index'] = $r[$key_by];
            $menu[$i]['items'] = array();
            $i++;
        }

        /* get_items */
        for ($i = 0; $i < count($menu); $i++) {
            $items = DB::sql('SELECT ' . $this->table_is['items'] . '.item_id, ' . $this->table_is['items'] . '.client_item, ' . $this->table_is['items_img'] . '.img FROM ' . $this->table_is['items'] . ' LEFT JOIN ' . $this->table_is['items_img'] . ' ON ' . $this->table_is['items_img'] . '.item_id = ' . $this->table_is['items'] . '.item_id WHERE `' . $key_items . '`="' . $menu[$i]['index'] . '" AND ' . $this->table_is['items_img'] . '.main = 1 AND ' . $this->table_is['items'] . '.active = 1');
            foreach ($items as $item) {
                $menu[$i]['items'][$item['item_id']] = $item;
            }
            unset($menu[$i]['index']);
        }

        return $menu;
    }

    /*
     * Get items without title
     * @return (array)
     */
    public function get_empty_items() {
        return DB::sql('SELECT * FROM ' . $this->table_is['items'] . ' WHERE title="" OR cat_id IS NULL');
    }

    /*
     * Get count items without title
     * @return (int)
     */
    public function count_empty_items() {
        $r = $this->get_empty_items();
        return count($r);
    }

    /*
     * Add item
     * @param (string) $item_parts: filename with item id
     */
    public function add_item($item) {
        $item_parts = explode('.', $item);
        $main_img = 0;
        if (empty($item_parts[2])) {
            $type_index = intval((floor($item_parts[0] / 100)) * 100);
            $item_is = DB::sql('SELECT id FROM ' . $this->table_is['items'] . ' WHERE item_id = :item_id', array(':item_id' => $item_parts[0]));
            if (empty($item_is)) {
                DB::sql('INSERT INTO ' . $this->table_is['items'] . ' (item_id,cat_id,type_index,title) VALUES (:item_id,NULL,:type,"")', array(':item_id' => $item_parts[0], ':type' => $type_index));
            }
            $img_name = $item_parts[0];
            $main_img = 1;
        } else {
            $img_name = $item_parts[0] . '.' . $item_parts[1];
        }
        $img_is = DB::sql('SELECT id FROM ' . $this->table_is['items_img'] . ' WHERE img = :img', array(':img' => $img_name));
        if (empty($img_is)) {
            DB::sql('INSERT INTO ' . $this->table_is['items_img'] . '(item_id,img,main) VALUES(:item_id,:img,:main)', array(':item_id' => $item_parts[0], ':img' => $img_name, ':main' => $main_img));
        }
    }

    /*
     * Search inspiration items
     * @param (string) $word: search words
     * @param (string) $search_felds: imploded search felds
     */
    public function search_inspiration($word, $search_felds) {
        $search = $rez = array();
        if (!empty($search_felds)) {
            $arr = explode('&', $search_felds);
            foreach ($arr as $val) {
                $values = explode('=', $val);
                $field = urldecode($values[1]);
                if (in_array($field, array('items', 'cats', 'types'))) {
                    $search[] = ' ' . $this->table_is[$field] . '.title LIKE "' . $word . '%" ';
                    $search[] = ' ' . $this->table_is[$field] . '.title LIKE "%' . $word . '" ';
                } else {
                    $search[] = ' ' . $this->table_is['items'] . '.`' . $field . '` LIKE "' . $word . '%" ';
                    $search[] = ' ' . $this->table_is['items'] . '.`' . $field . '` LIKE "%' . $word . '" ';
                }
            }
            $word = trim($word);

            $sql = 'SELECT SQL_CALC_FOUND_ROWS ' . $this->table_is['items'] . '.*,' . $this->table_is['cats'] . '.title cat_title,' . $this->table_is['cats'] . '.abbr cat_abbr
                FROM ' . $this->table_is['items'] . ' 
                    LEFT JOIN ' . $this->table_is['types'] . ' ON ' . $this->table_is['types'] . '.index = ' . $this->table_is['items'] . '.type_index 
                        LEFT JOIN ' . $this->table_is['cats'] . ' ON ' . $this->table_is['cats'] . '.id = ' . $this->table_is['items'] . '.cat_id 
                WHERE ' . $this->table_is['items'] . '.cat_id IS NOT NULL 
                    AND (' . implode('OR', $search) . ') ORDER BY id';
            $rez['items'] = DB::sql($sql);
            
            $sizes = DB::sql('SELECT item_id, abbr FROM ' . $this->table_is['size'] . ' WHERE `type`="flat"');
            if (!empty($sizes)) {
                foreach ($sizes as $v) {
                    $rez['sizes'][$v['item_id']] = $v['abbr'];
                }
            }
        }
        return $rez;
    }

    /*
     * Get next category id
     * @param (int) $id: current id
     * @return (int)
     */
    public function getNextCategory($id) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM `' . $this->table_is['cats'] . '` ORDER BY id) cat WHERE cat.id>:id LIMIT 1', array(':id' => $id));
        return $rez['id'];
    }

    /*
     * Get prev category id
     * @param (int) $id: current id
     * @return (int)
     */
    public function getPrevCategory($id) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM `' . $this->table_is['cats'] . '` ORDER BY id DESC) cat WHERE cat.id<:id LIMIT 1', array(':id' => $id));
        return $rez['id'];
    }

    /*
     * Get next type id
     * @param (int) $id: current id
     * @return (int)
     */
    public function getNextType($id) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM `' . $this->table_is['types'] . '` ORDER BY id) `type` WHERE `type`.id>:id LIMIT 1', array(':id' => $id));
        return $rez['id'];
    }

    /*
     * Get prev type id
     * @param (int) $id: current id
     * @return (int)
     */
    public function getPrevType($id) {
        $rez = DB::sql_row('SELECT id FROM (SELECT id FROM `' . $this->table_is['types'] . '` ORDER BY id DESC) `type` WHERE `type`.id<:id LIMIT 1', array(':id' => $id));
        return $rez['id'];
    }

    /*
     * Get all papers id for item
     * @param (int) $item_id: item id
     * @return (array)
     */
    public function get_used_papers($item_id) {
        $all = array();
        $rez = DB::sql('SELECT paper_id FROM `' . $this->table_is['paper'] . '` WHERE item_id=:item_id', array(':item_id' => $item_id));
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[] = $val['paper_id'];
            }
        }
        return $all;
    }

    /*
     * Get all coating id for item
     * @param (int) $item_id: item id
     * @return (array)
     */
    public function get_used_coating($item_id) {
        $all = array();
        $rez = DB::sql('SELECT coat_id FROM `' . $this->table_is['coat'] . '` WHERE item_id=:item_id', array(':item_id' => $item_id));
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[] = $val['coat_id'];
            }
        }
        return $all;
    }

    /*
     * Get all finishes id for item
     * @param (int) $item_id: item id
     * @return (array)
     */
    public function get_used_finishes($item_id) {
        $all = array();
        $rez = DB::sql('SELECT finish_id FROM `' . $this->table_is['finish'] . '` WHERE item_id=:item_id', array(':item_id' => $item_id));
        if (!empty($rez)) {
            foreach ($rez as $val) {
                $all[] = $val['finish_id'];
            }
        }
        return $all;
    }
    
    /*
     * Get default item for type
     * @param (int) $type_index: type index
     * @return (array)
     */
    public function get_default_item($type_index){
        $sample = DB::sql_row('SELECT sample_checked FROM is_types WHERE `index`=:index', array(':index'=>$type_index));
        return (empty($sample['sample_checked']))? array() : unserialize($sample['sample_checked']);
    }
    
}