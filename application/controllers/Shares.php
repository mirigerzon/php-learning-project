<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shares extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model');
        $this->load->model('User_model');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $data['shared_projects'] = $this->Project_model->get_shared_projects_for_user($user_id);

        $data['main_view'] = 'shares';

        $this->load->view('layouts/main', $data);
    }


    public function update_role_ajax()
    {
        $project_id = $this->input->post('project_id');
        $user_id = $this->input->post('user_id');
        $role = $this->input->post('role');

        $result = $this->Project_model->share_project($project_id, $user_id, $role);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update role']);
        }
    }
}
