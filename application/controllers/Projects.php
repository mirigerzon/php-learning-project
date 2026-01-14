<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Project_model');
    }

    public function index()
    {
        $projects = $this->Project_model->get_user_projects($this->session->userdata('user_id'));

        $data = [
            'main_view' => 'projects/projects',
            'projects' => $projects,
            'title' => 'My Projects'
        ];

        $this->load->view('layouts/main', $data);
    }

    public function add()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('project_title', 'Project Title', 'required');
        $this->form_validation->set_rules('project_body', 'Project Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            // מציג את הטופס
            $data = [
                'main_view' => 'projects/add',
                'title' => 'Add New Project'
            ];
            $this->load->view('layouts/main', $data);
        } else {
            $project_data = [
                'user_id' => $this->session->userdata('user_id'),
                'project_title' => $this->input->post('project_title'),
                'project_body' => $this->input->post('project_body'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->Project_model->add_project($project_data);

            $this->session->set_flashdata('success', 'Project created successfully!');
            redirect('projects');
        }
    }

    public function add_ajax()
    {
        $this->form_validation->set_rules('project_title', 'Project Title', 'required');
        $this->form_validation->set_rules('project_body', 'Project Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors('<p>', '</p>')
            ]);
        } else {
            $project_data = [
                'user_id' => $this->session->userdata('user_id'),
                'project_title' => $this->input->post('project_title'),
                'project_body' => $this->input->post('project_body'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->Project_model->add_project($project_data);

            echo json_encode([
                'success' => true,
                'message' => 'Project added successfully!',
                'redirect' => base_url("/projects")
            ]);
        }
    }

    public function edit($id)
    {
        $this->load->library('form_validation');

        $project = $this->Project_model->get_project($id);
        if (!$project) {
            show_404();
        }

        $this->form_validation->set_rules('project_title', 'Project Title', 'required');
        $this->form_validation->set_rules('project_body', 'Project Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'main_view' => 'projects/edit',
                'title' => 'Edit Project',
                'project' => $project
            ];
            $this->load->view('layouts/main', $data);
        } else {
            $project_data = [
                'project_title' => $this->input->post('project_title'),
                'project_body' => $this->input->post('project_body')
            ];

            $this->Project_model->update_project($id, $project_data);

            $this->session->set_flashdata('success', 'Project updated successfully!');
            redirect('projects');
        }
    }

    public function delete($id)
    {
        $project = $this->Project_model->get_project($id);
        if (!$project) {
            show_404();
        }

        $this->Project_model->delete_project($id);

        $this->session->set_flashdata('success', 'Project deleted successfully!');
        redirect('projects');
    }
}


