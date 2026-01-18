<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Project_model');
        $this->load->model('Task_model');
        $this->load->model('User_model');

        // רק מנהלים
        if (!$this->session->userdata('user_id') || !$this->session->userdata('is_admin')) {
            show_error('Unauthorized', 403);
        }
    }

    public function index()
    {
        $data['projects_status'] = $this->Project_model->get_status_counts();
        $data['tasks_status'] = $this->Task_model->get_status_counts();
        $data['tasks_per_user'] = $this->Task_model->get_count_per_user_with_due();
        $data['projects_per_user'] = $this->Project_model->get_count_per_user();
        $data['tasks_status'] = $this->Task_model->get_status_counts();
        $data['tasks_per_user'] = $this->Task_model->get_count_per_user_with_due();
        $data['title'] = 'Admin Dashboard';
        
        $data['main_view'] = 'dashboard/admin_dshboard';

        $this->load->view('layouts/main', $data);
    }
}
