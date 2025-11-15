<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Print extends Main {

    public function action_index() {
        $this->template->scripts[] = 'print.init';
        $this->template->scripts[] = 'jquery.numeric';
        $this->template->scripts[] = 'numeric_init';
        if ($this->request->param('id') == 'payment_information') {
//            $print_model = Model::factory('Print');

            if (!empty($_POST['save_payment_info'])) {
                $this->redirect('/print_it/order_confirmation');
            } elseif (!empty($_GET['NotSave'])) {
                $this->redirect('/print_it/order_confirmation');
            }


            $tpl = 'payment_information';
        } elseif ($this->request->param('id') == 'checkout') {

            if (!empty($_POST['next_payment'])) {
                $this->redirect('/print_it/payment_information');
            }
            $tpl = 'print_checkout';
        } elseif ($this->request->param('id') == 'order_confirmation') {
            $tpl = 'order_confirmation';
        } elseif ($this->request->param('id') == 'products') {
            $print_model = Model::factory('Print');
            $this->data['categories'] = $print_model->get_cats();
            if (!empty($_POST['checkout'])) {
                $this->redirect('/print_it/checkout');
            }
            $tpl = 'products';
        } else {
            $print_model = Model::factory('Print');
            $this->data['categories'] = $print_model->get_cats();
            if(!empty($_POST['print_form'])){
                $this->redirect('/print_it/products');
            }
            $tpl = 'print_home';
        }
        $this->template->content = View::factory('pages/' . $tpl, $this->data);
    }

    public function action_ajax() {
        $print_model = Model::factory('Print');
        $post = $this->request->post();
        switch ($post['func']) {
            case 'item_quantity':
                $_SESSION['save_quantity'] = trim($post['save']);
                break;
            case 'get_item_data':
                $data['item'] = $print_model->get_item($post['id']);
                //papers, inks
                if (!empty($data['item'])) {
                    $data['stick'] = $print_model->get_sticked($post['id']);
                    $data['slits'] = $print_model->getSlits();
                    $data['coats'] = $print_model->get_coats('print_coating');
                    $data['finish'] = $print_model->get_coats('print_finishes');
                    $data['inks'] = $print_model->get_inks();
                    $data['proofs'] = $print_model->getProofs();
                    $data['papers'] = $print_model->get_papers();
                    $data['item_prices'] = $print_model->get_item_prices($data['item']['id']);
                    $html_data = View::factory('block/print_item', $data);
                }
                break;
            case 'get_slit_detail':
                $rez = $print_model->getSlit($post['id']);
                break;
            case 'get_paper_detail':
                $rez = $print_model->getPaper($post['id']);
                break;
            case 'get_inks_detail':
                $rez = $print_model->getInk($post['id']);
                break;
        }

        if (!empty($rez)) {
            echo json_encode($rez);
        } elseif (!empty($html_data)) {
            echo $html_data;
        }
        exit;
    }

}