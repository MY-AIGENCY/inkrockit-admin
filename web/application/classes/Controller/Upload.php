<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Upload extends Main {

    public function action_index() {
        $this->template->scripts[] = 'upload_file/jquery.iframe-transport';
        $this->template->scripts[] = 'admin/jquery-ui-1.9.2';
        $this->template->scripts[] = 'upload_file/jquery.fileupload';
        $this->template->scripts[] = 'upload_file/jquery.fileupload_init';

        $session = Session::instance();
        $this->data['uploaded'] = $session->get('uploaded_files');
        $this->template->content = View::factory('pages/upload', $this->data);
    }

    public function action_upload() {
        if (is_file($_FILES['file']['tmp_name'])) {
            copy($_FILES['file']['tmp_name'], APPPATH . 'files/upload/' . $_FILES['file']['name']);
        }
        $session = Session::instance();
        $uploaded_files = $session->get('uploaded_files');
        if (empty($uploaded_files)) {
            $uploaded_files = array();
        }
        $uploaded_files[] = $_FILES['file']['name'];
        $session->set('uploaded_files', $uploaded_files);

        echo json_encode(array('name' => $_FILES['file']['name'], 'size' => $_FILES['file']['size']));
        exit;
    }

    public function action_ajax() {
        $page = $this->request->param('id');
        switch ($page) {
            case 'remove_file':
                $file = $this->request->post('file');
                @unlink(APPPATH . 'files/upload/' . $file);

                $session = Session::instance();
                $uploaded_files = $session->get('uploaded_files');
                if (!empty($uploaded_files)) {
                    foreach ($uploaded_files as $key => $val) {
                        if ($val == $file) {
                            unset($uploaded_files[$key]);
                            break;
                        }
                    }
                }
                $session->set('uploaded_files', $uploaded_files);
                break;
            case 'send_message':

                break;
        }
        exit;
    }

}