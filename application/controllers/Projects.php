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

    public function index()
    {
        $projects = $this->Project_model->get_user_projects_with_status($this->session->userdata('user_id'));

        // מוסיפים לכל פרויקט את רשימת המשתמשים המשותפים
        foreach ($projects as $project) {
            $project->shared_users = $this->Project_model->get_project_shares($project->project_id);
        }

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

    public function add_ajax_form()
    {
        $this->load->view('projects/add_form');
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
            $project_id = $this->db->insert_id();
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Project added successfully!',
                'project_id' => $project_id
            ]);
            exit;

            echo json_encode([
                'success' => true,
                'message' => 'Project added successfully!',
                'project_id' => $project_id
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

    public function edit_ajax_form($project_id)
    {
        $project = $this->Project_model->get_project($project_id);
        if (!$project) {
            echo 'Project not found.';
            return;
        }

        $this->load->view('projects/edit_form', ['project' => $project]);
    }

    public function edit_ajax($project_id)
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
                'project_title' => $this->input->post('project_title'),
                'project_body' => $this->input->post('project_body')
            ];

            $this->Project_model->update_project($project_id, $project_data);

            echo json_encode([
                'success' => true,
                'project_id' => $project_id,
                'project_title' => $project_data['project_title'],
                'project_body' => $project_data['project_body']
            ]);
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

    public function share_ajax_form($project_id = null)
    {
        if (!$project_id) {
            show_error("Project ID is missing!");
            return;
        }

        $data['project_id'] = $project_id;
        $data['users'] = $this->Project_model->get_users_with_roles($project_id);

        $this->load->view('projects/share_ajax_form', $data);
    }

    public function share_ajax()
    {
        if (!$this->session->user_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
            return;
        }

        $project_id = $this->input->post('project_id');
        $roles = $this->input->post('roles');

        if (!$project_id || !$roles || !is_array($roles)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing data'
            ]);
            return;
        }

        $errors = [];
        foreach ($roles as $user_id => $role) {
            $result = $this->Project_model->share_project($project_id, $user_id, $role);
            if ($result === false) {
                $errors[] = "Failed to update for user $user_id";
            }
        }

        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'message' => implode(', ', $errors)
            ]);
        } else {
            echo json_encode([
                'success' => true
            ]);
        }
    }
}


