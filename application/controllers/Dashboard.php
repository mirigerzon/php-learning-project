<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Project_model');
        $this->load->model('Task_model');

        // רק משתמשים מחוברים
        if (!$this->session->userdata('user_id')) {
            show_error('Unauthorized', 403);
        }
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');

        // פרויקטים של המשתמש עם ספירת משימות
        $data['my_projects'] = $this->Project_model->get_user_projects_with_task_counts($user_id);

        // משימות של המשתמש לפי סטטוס
        $data['my_tasks'] = $this->Task_model->get_task_counts_by_status($user_id);

        $data['title'] = 'My Dashboard';
        $data['main_view'] = 'dashboard/user_dashboard';

        $this->load->view('layouts/main', $data);
    }
}
