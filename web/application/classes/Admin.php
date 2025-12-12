<?php

defined('SYSPATH') or die('No direct access allowed.');

class Admin extends Controller_Template {

    public $template = 'admin', $data, $admin, $model;

    public function before() {
        parent::before();

        $this->template->controller = $this->request->controller();
        $this->template->action = $this->data['action'] = $this->request->action();

        $this->template->content = '';
        $this->template->styles = array();
        $this->template->scripts = array();
        $this->template->menu = '';
        $this->template->sub_menu = '';

        $session = Session::instance();
        $this->model = Model::factory('Inspiration');
        $this->admin_model = Model::factory('Admin');

        // Prefer server-side session for admin auth; fall back to legacy cookie.
        $this->admin = $session->get('admin_user');
        if (empty($this->admin)) {
            $this->admin = Cookie::get('admin_user');
        }
        if(!empty($this->admin)){
            // Legacy cookie path stores serialized array; session path stores the array directly.
            if (is_string($this->admin)) {
                $this->admin = @unserialize($this->admin);
            }
            if (is_array($this->admin)) {
                // Ensure session is hydrated even if we only had the legacy cookie.
                $session->set('admin_user', $this->admin);
            } else {
                $this->admin = NULL;
            }
        }

        if (empty($this->admin) && $this->data['action'] != 'login') {
            $this->redirect('/admin/login');
        } elseif (!empty($this->admin)) {
            $this->template->admin = $this->data['admin'] = $this->admin;
            
            //get tasks data
            $count_tasks = $this->admin_model->getMyTasks($this->admin['id']);
            $count_tasks = (!empty($count_tasks))? '(<span class="task_counts">'.$count_tasks.'</span>)' : '' ;
            
            $count_assign_tasks = $this->admin_model->getAssignTasks($this->admin['id']);
            $count_assign_tasks = (!empty($count_assign_tasks))? '(<span class="task_assigned_counts">'.$count_assign_tasks.'</span>)' : '' ;
//            $customers = $this->admin_model->getActiveCustomers();
//            $active_customers = (empty($customers))? '' : '('.$customers.')' ;
            
            $this->template->menu = array(
                'Sales' => array('title' => 'Sales & Communication Center', 'class' => 'nav_dashboard'),
                'Inspiration' => array('title' => 'Inspiration Station', 'class' => 'nav_dashboard'),
                'Print' => array('title' => 'Print It', 'class' => 'nav_graphs'),
                'Users' => array('title' => 'Users', 'class' => 'nav_typography')
            );
            $this->template->sub_menu = array(
                'Sales' => array(
                    array('title' => 'Contact list', 'url' => 'all'),
                    array('title' => 'Active Customers' , 'url' => 'tasks'),
                    array('title' => 'Assigned Tasks '.$count_assign_tasks, 'url' => 'assigned_tasks'),
                    array('title' => 'My Tasks '.$count_tasks, 'url' => 'current_jobs'),
                ),
                'Inspiration' => array(
                    array('title' => 'Product Types', 'url' => 'categories'),
                    array('title' => 'Industries', 'url' => 'types'),
                    array('title' => 'Sample Inventory', 'url' => 'items'),
                ),
                'Print' => array(
                    array('title' => 'Products', 'url' => 'items'),
//                    array('title' => 'Product Properties', 'url' => 'category'),
                    array('title' => 'Main Settings', 'url' => 'setting'),
                ),
                'Users' => array(
                    array('title' => 'All Users', 'url' => 'index'),
                    array('title' => 'Add New User', 'url' => 'edit'),
                    array('title' => 'Events', 'url' => 'events'),
                    array('title' => 'Email templates', 'url' => 'email'),
                )
            );
        }
    }

    public function check($arr) {
        foreach ($arr as $v) {
            if (!$this->request->post($v))
                return false;
        }
        return true;
    }

}