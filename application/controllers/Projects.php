<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model');
        $this->load->model('User_model');
    }

    /* =======================
       PERMISSION CHECK
       ======================= */
    private function require_project_permission($required = 'view')
    {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            show_error('Unauthorized', 401);
        }

        $user = $this->User_model->get_by_id($user_id);

        if (
            !$user ||
            !$user->is_admin ||
            $user->project_permission === null
        ) {
            show_error('Forbidden', 403);
        }

        if ($required === 'edit' && $user->project_permission !== 'edit') {
            show_error('Forbidden', 403);
        }
    }

    /* =======================
       LIST PROJECTS
       ======================= */
    public function index()
    {
        $this->require_project_permission('view');

        $projects = $this->Project_model
            ->get_user_projects_with_status($this->session->userdata('user_id'));

        foreach ($projects as $project) {
            $project->shared_users =
                $this->Project_model->get_project_shares($project->project_id);
        }

        $data = [
            'main_view' => 'projects/projects',
            'projects' => $projects,
            'title' => 'Projects'
        ];

        $this->load->view('layouts/main', $data);
    }

    /* =======================
       ADD PROJECT
       ======================= */
    public function add()
    {
        $this->require_project_permission('edit');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('project_title', 'Project Title', 'required');
        $this->form_validation->set_rules('project_body', 'Project Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'main_view' => 'projects/add',
                'title' => 'Add New Project'
            ];
            $this->load->view('layouts/main', $data);
            return;
        }

        $this->Project_model->add_project([
            'user_id' => $this->session->userdata('user_id'),
            'project_title' => $this->input->post('project_title'),
            'project_body' => $this->input->post('project_body'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Project created successfully!');
        redirect('projects');
    }

    public function add_ajax_form()
    {
        $this->require_project_permission('edit');
        $this->load->view('projects/add_form');
    }

    public function add_ajax()
    {
        $this->require_project_permission('edit');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('project_title', 'Project Title', 'required');
        $this->form_validation->set_rules('project_body', 'Project Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors('<p>', '</p>')
            ]);
            return;
        }

        $this->Project_model->add_project([
            'user_id' => $this->session->userdata('user_id'),
            'project_title' => $this->input->post('project_title'),
            'project_body' => $this->input->post('project_body'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'success' => true,
            'project_id' => $this->db->insert_id()
        ]);
    }

    /* =======================
       EDIT PROJECT
       ======================= */
    public function edit($id)
    {
        $this->require_project_permission('edit');

        $project = $this->Project_model->get_project($id);
        if (!$project) {
            show_404();
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('project_title', 'Project Title', 'required');
        $this->form_validation->set_rules('project_body', 'Project Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'main_view' => 'projects/edit',
                'title' => 'Edit Project',
                'project' => $project
            ];
            $this->load->view('layouts/main', $data);
            return;
        }

        $this->Project_model->update_project($id, [
            'project_title' => $this->input->post('project_title'),
            'project_body' => $this->input->post('project_body')
        ]);

        $this->session->set_flashdata('success', 'Project updated successfully!');
        redirect('projects');
    }

    public function edit_ajax_form($project_id)
    {
        $this->require_project_permission('edit');

        $project = $this->Project_model->get_project($project_id);
        if (!$project) {
            echo 'Project not found.';
            return;
        }

        $this->load->view('projects/edit_form', ['project' => $project]);
    }

    public function edit_ajax($project_id)
    {
        $this->require_project_permission('edit');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('project_title', 'Project Title', 'required');
        $this->form_validation->set_rules('project_body', 'Project Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors('<p>', '</p>')
            ]);
            return;
        }

        $this->Project_model->update_project($project_id, [
            'project_title' => $this->input->post('project_title'),
            'project_body' => $this->input->post('project_body')
        ]);

        echo json_encode([
            'success' => true
        ]);
    }

    /* =======================
       DELETE PROJECT
       ======================= */
    public function delete($id)
    {
        $this->require_project_permission('edit');

        $project = $this->Project_model->get_project($id);
        if (!$project) {
            show_404();
        }

        $this->Project_model->delete_project($id);
        $this->session->set_flashdata('success', 'Project deleted successfully!');
        redirect('projects');
    }

    /* =======================
       SHARE PROJECT
       ======================= */
    public function share_ajax_form($project_id = null)
    {
        $this->require_project_permission('edit');

        if (!$project_id) {
            show_error('Project ID missing');
        }

        $data['project_id'] = $project_id;
        $data['users'] =
            $this->Project_model->get_users_with_roles($project_id);

        $this->load->view('projects/share_ajax_form', $data);
    }

    public function share_ajax()
    {
        $this->require_project_permission('edit');

        $project_id = $this->input->post('project_id');
        $roles = $this->input->post('roles');

        if (!$project_id || !is_array($roles)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing data'
            ]);
            return;
        }

        foreach ($roles as $user_id => $role) {
            $this->Project_model->share_project($project_id, $user_id, $role);
        }

        echo json_encode(['success' => true]);
    }
}
