<?php

defined('SYSPATH') or die('No direct access allowed.');

class Controller_Admin_Inspiration extends Admin {

    public function action_categories() {
        $param = $this->request->param('param1');
        switch ($param) {
            case 'add':
                if ($this->request->post('add_cat')) {
                    if ($this->check(array('title'))) {
                        if ($this->model->add_cat()) {
                            $this->redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                }
                $tpl = 'insp/add_cat';
                break;
            case 'del':
                $this->model->del_cat();
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('add_cat') || $this->request->post('save_next') || $this->request->post('save_and_prev')) {
                    if ($this->check(array('title'))) {
                        $id = Request::initial()->param('param2');
                        $this->model->save_cat($id);
                        if ($this->request->post('save_next')) {
                            $next = $this->model->getNextCategory($id);
                            $this->redirect('/admin/inspiration/categories/edit/' . $next);
                        } elseif ($this->request->post('save_and_prev')) {
                            $prev = $this->model->getPrevCategory($id);
                            $this->redirect('/admin/inspiration/categories/edit/' . $prev);
                        } else {
                            $this->redirect('/admin/inspiration/categories');
                        }
                    }
                }
                $this->data['cat'] = $this->model->get_cat();
                $tpl = 'insp/add_cat';
                break;
            default:
                $this->data['cats'] = $this->model->get_cats();
                $tpl = 'insp/cats';
        }
        $this->template->content = View::factory('admin/' . $tpl, $this->data);
    }

    public function action_types() {
        $param = $this->request->param('param1');
        switch ($param) {
            case 'add':
                if ($this->request->post('add_type')) {
                    if ($this->check(array('title', 'index'))) {
                        if ($this->model->add_type()) {
                            $this->redirect($_SERVER['HTTP_REFERER']);
                        }
                    }
                }
                $tpl = 'insp/add_type';
                break;
            case 'del':
                $this->model->del_type();
                $this->redirect($_SERVER['HTTP_REFERER']);
                break;
            case 'edit':
                if ($this->request->post('add_type') || $this->request->post('save_next') || $this->request->post('save_and_prev')) {
                    if ($this->check(array('index', 'title'))) {
                        $id = Request::initial()->param('param2');

                        $this->model->save_type($id);
                        if ($this->request->post('save_next')) {
                            $next = $this->model->getNextType($id);
                            $this->redirect('/admin/inspiration/types/edit/' . $next);
                        } elseif ($this->request->post('save_and_prev')) {
                            $prev = $this->model->getPrevType($id);
                            $this->redirect('/admin/inspiration/types/edit/' . $prev);
                        } else {
                            $this->redirect('/admin/inspiration/types');
                        }
                    }
                }
                $this->data['type'] = $this->model->get_type();
                $tpl = 'insp/add_type';
                break;
            default :
                $this->data['types'] = $this->model->get_types();
                $tpl = 'insp/types';
        }
        $this->template->content = View::factory('admin/' . $tpl, $this->data);
    }

    public function action_upload() {
        $file = $_FILES['files']['tmp_name'][0];
        $old_file = $_FILES['files']['name'][0];
        $path = pathinfo($_FILES['files']['name'][0]);
        $new_name = $path['filename'] . '.png';
        $new_path = 'application/files/items/pics/' . $new_name;
        $new_path_thumb = 'application/files/items/thumbnails/' . $new_name;
        $err = '';

        if (!is_file($file)) {
            $err = 'Upload error: Please, check file size';
        } else {
            if (preg_match('/[0-9]+\.(jpg|jpeg|png|gif)/i', $old_file) || preg_match('/[0-9]+\.[0-9]+\.(jpg|jpeg|png|gif)/i', $old_file)) {
                $inspiration_mod = Model::factory('Inspiration');
                $inspiration_mod->add_item($old_file);

                $img = Image::factory($file);
                $img->save($new_path, 90);
                $img = Image::factory($file);
                $img->resize(90, 90, Image::AUTO)->save($new_path_thumb, 90);
            } else {
                $err = 'Incorrect file name';
            }
        }
        $arr = array(0 => array('name' => $old_file, 'size' => $_FILES['files']['size'][0], 'err' => $err, 'id' => $path['filename']));
        echo json_encode($arr);
        exit;
    }

    public function action_items() {
        $print_model = Model::factory('Admin_Print');
        $action = $this->request->param('param1');
        $item_id = $this->request->param('param2');
        switch ($action) {
            case 'add':
                $this->template->scripts = array('/js/upload_file/jquery.ui.widget.js', '/js/upload_file/jquery.iframe-transport.js',
                    '/js/upload_file/jquery.fileupload.js', '/js/upload_file/jquery.fileupload-process.js',
                    '/js/upload_file/upload-init.js');
                $this->template->content = View::factory('admin/insp/add_items');
                break;
            case 'fill':
                $empty_items = $this->model->get_empty_items();
                if (count($empty_items) > 0) {
                    $item_id = $empty_items[0]['item_id'];
                } else {
                    $this->redirect('/admin/inspiration/items');
                }
            case 'edit':
                $this->data['edit_next'] = false;
                if (empty($item_id)) {
                    $session = Session::instance();
                    $items = $session->get('edit_insp');
                    if (empty($items[0])) {
                        $this->redirect('/admin/inspiration/items');
                    } else {
                        $item_id = $items[0];
                        $this->data['edit_next'] = true;
                    }
                }
                $this->template->scripts = array('/js/upload_file/jquery.ui.widget.js', '/js/upload_file/jquery.iframe-transport.js',
                    '/js/upload_file/jquery.fileupload.js', '/js/upload_file/jquery.fileupload-process.js',
                    '/js/upload_file/upload-init.js');

                $save_and_prev = $this->request->post('save_and_prev');
                $save_and_next = $this->request->post('save_and_next');
                $just_save = $this->request->post('just_save');

                $delete = $this->request->post('delete');
                if (!empty($save_and_next) || !empty($just_save) || !empty($save_and_prev)) {
                    $this->model->save_item($this->request->post('item_id'));
                    if (!empty($save_and_next)) {
                        if (!empty($items)) {
                            $shift = array_shift($items);
                            $session->set('edit_insp', $items);

                            $all_shift = $session->get('edit_insp_prev');
                            $all_shift[] = $shift;
                            $session->set('edit_insp_prev', $all_shift);
                        }
                        if ($this->data['edit_next']) {
                            $this->redirect('/admin/inspiration/items/edit');
                        } else {
                            if ($action == 'fill') {
                                $this->redirect('/admin/inspiration/items/fill');
                            } else {
                                $next = $this->model->get_next_item($item_id);
                                $this->redirect('/admin/inspiration/items/edit/' . $next);
                            }
                        }
                    } elseif (!empty($save_and_prev)) {
                        if (!empty($items)) {
                            $all_shift = $session->get('edit_insp_prev');
                            if (!empty($all_shift)) {
                                $last = array_pop($all_shift);
                            } else {
                                $this->redirect('/admin/inspiration/items');
                            }
                            array_unshift($items, $last);
                            $session->set('edit_insp', $items);
                        }
                        if ($this->data['edit_next']) {
                            $this->redirect('/admin/inspiration/items/edit');
                        } else {
                            $prev = $this->model->get_prev_item($item_id);
                            $this->redirect('/admin/inspiration/items/edit/' . $prev);
                        }
                    } else {
                        if (!empty($session)) {
                            $session->set('edit_insp', NULL);
                        }
                        $this->redirect('/admin/inspiration/items');
                    }
                } elseif (!empty($delete)) {
                    $this->model->remove_item($this->request->post('item_id'));
                }
                $this->data['item'] = $this->model->get_item($item_id);
                $this->data['cats'] = $this->model->get_cats();

                //from print
                $this->data['papers'] = $print_model->get_papers();
                $this->data['coating'] = $print_model->get_coating();
                $this->data['finishes'] = $print_model->get_coating('print_finishes');
                $this->data['papers_used'] = $this->model->get_used_papers($item_id);
                $this->data['coating_used'] = $this->model->get_used_coating($item_id);
                $this->data['finishes_used'] = $this->model->get_used_finishes($item_id);

                $this->template->content = View::factory('admin/insp/edit_item', $this->data);
                break;
            case 'remove':
                if (!empty($item_id)) {
                    $this->model->remove_item($item_id);
                    $this->redirect('/admin/inspiration/items');
                } elseif ($this->request->post('id')) {
                    foreach ($this->request->post('id') as $val) {
                        $this->model->remove_item($val);
                    }
                    exit;
                }
                break;
            default :
                $this->data['empty_items'] = $this->model->count_empty_items();
                $this->data['items'] = $this->model->get_items();
                $this->data['total_items'] = $this->admin_model->calcPages();
                $this->data['pages'] = ceil($this->data['total_items'] / 30);
                $this->data['paginator'] = Pagination::factory(array('total_items' => $this->data['total_items'], 'items_per_page' => 30))->render();
                $this->template->content = View::factory('admin/insp/items', $this->data);
                break;
        }
    }

    public function action_xls() {
        //update inventory DB from exel
        include VNDPATH . 'exel/reader.php';
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');

        $data->read(APPPATH . 'files/inventory_db.xls');
        if (!empty($data->sheets)) {
            $cols = $data->sheets[0]['cells'];
            $count = count($cols);

            $all = array();
            $products = DB::sql('SELECT * FROM is_items');
            foreach ($products as $v) {
                $all[] = $v['item_id'];
            }

            for ($x = 2; $x <= $count; $x++) {
                $counts = (empty($cols[$x][2])) ? 0 : $cols[$x][2];
                $job = (empty($cols[$x][3])) ? '' : $cols[$x][3];

                if (in_array($cols[$x][1], $all)) {
                    DB::sql('UPDATE is_items SET title=:title,`count`=:count,job_id=:job_id WHERE item_id=:id', array(':title' => $cols[$x][5], ':count' => $counts, ':job_id' => $job, ':id' => $cols[$x][1]));
                } else {
                    DB::sql('INSERT INTO is_items (title,`count`,job_id,item_id,active,cat_id) VALUES (:title,:count,:job_id,:id,0,NULL)', array(':title' => $cols[$x][5], ':count' => $counts, ':job_id' => $job, ':id' => $cols[$x][1]));
                    $path = APPPATH . 'files/items/';
                    $pic = trim($cols[$x][1]);
                    copy($path . 'empty.png', $path . 'pics/' . $pic . '.png');
                    copy($path . 'empty.png', $path . 'thumbnails/' . $pic . '.png');
                }
            }
        }
        exit;
    }

    public function action_ajax() {
        $post = $this->request->post();
        switch ($post['func']) {
            case 'send_edit_insp':
                $items = $post['items'];
                $session = Session::instance();
                $session->set('edit_insp', $items);
                $session->set('edit_insp_prev', NULL);
                echo 'location';
                break;
            case 'search_insp':
                $finded = $this->model->search_inspiration(trim($post['val']), $post['fields']);
                $html_data = View::factory('block_admin/insp_table', array('items' => $finded));
                break;
            case 'add_category':
                $r = DB::sql('INSERT INTO `is_cats` (title,active) VALUES(:title, 1)', array(':title' => $post['title']));
                $rez['ins_id'] = $r[0];
                break;
        }
        if (!empty($rez)) {
            echo json_encode($rez);
        } elseif (!empty($html_data)) {
            echo $html_data;
        }
        exit;
    }
    
    public function after() {
        $this->template->scripts[] = '/js/admin/inspiration.init.js';
        parent::after();
    }

}