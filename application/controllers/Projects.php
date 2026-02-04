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
       LIST PROJECTS
       ======================= */
    public function index()
    {
        $this->require_project_permission(null, 'view');

        $user_id = $this->session->userdata('user_id');
        $projects = $this->Project_model->get_user_projects_with_status($user_id);

        foreach ($projects as $project) {
            $project->shared_users = $this->Project_model->get_project_shares($project->project_id);
            if ((int) $project->user_id !== (int) $user_id) {
                $user_share = array_filter($project->shared_users, function ($share) use ($user_id) {
                    return (int) $share->user_id === (int) $user_id;
                });
                $project->user_role = !empty($user_share) ? array_values($user_share)[0]->role : null;
            } else {
                $project->user_role = 'owner';
            }
        }

        $data = [
            'main_view' => 'projects/projects',
            'projects' => $projects,
            'title' => 'My App/Projects'
        ];

        $this->load->view('layouts/main', $data);
    }


    /* =======================
       ADD PROJECT
       ======================= */
    public function add()
    {
        $this->require_project_permission(null, 'edit');

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
        $this->require_project_permission(null, 'edit');
        $this->load->view('projects/add_form');
    }

    public function add_ajax()
    {
        $this->require_project_permission(null, 'edit');

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

        $insert_id = $this->db->insert_id();
        // Build project payload for frontend
        $project = [
            'project_id' => $insert_id,
            'project_title' => $this->input->post('project_title'),
            'project_body' => $this->input->post('project_body'),
            'task_count' => 0,
            'project_status' => 'no missions'
        ];

        echo json_encode([
            'success' => true,
            'project' => $project
        ]);
    }

    /* =======================
       EDIT PROJECT
       ======================= */
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

        // Fetch updated project and task info for frontend
        $project_row = $this->Project_model->get_project($project_id);
        $task_count = (int) $this->db->where('project_id', $project_id)->count_all_results('tasks');
        if ($task_count === 0) {
            $project_status = 'no missions';
        } else {
            $has_open = (bool) $this->db->where('project_id', $project_id)->where('status', 0)->count_all_results('tasks');
            $project_status = $has_open ? 'Open' : 'Closed';
        }

        $project = [
            'project_id' => $project_row->project_id,
            'project_title' => $project_row->project_title,
            'project_body' => $project_row->project_body,
            'task_count' => $task_count,
            'project_status' => $project_status
        ];

        echo json_encode([
            'success' => true,
            'project' => $project
        ]);
    }

    /* =======================
       DELETE PROJECT
       ======================= */
    public function delete($id, $from = 'user')
    {
        $this->require_project_permission($id, 'edit');

        $project = $this->Project_model->get_project($id);
        if (!$project) {
            show_404();
        }

        $this->Project_model->delete_project($id);
        $this->session->set_flashdata('success', 'Project deleted successfully!');
        redirect($from === 'admin' ? 'admin/projects' : 'projects');
    }



    public function require_project_permission($project_id = null, $required = 'view')
    {
        $user_id = $this->session->userdata('user_id');

        // לא מחובר
        if (!$user_id) {
            $this->deny_access();
        }

        $user = $this->User_model->get_by_id($user_id);
        if (!$user) {
            $this->deny_access();
        }

        // אדמין גלובלי
        if ((int) $user->is_admin === 1 && $user->project_permission === 'edit') {
            return 'admin';
        }

        // פעולות כלליות (index, add)
        if ($project_id === null) {
            return 'view';
        }

        // בדיקת בעלות על הפרויקט
        $project = $this->Project_model->get_project($project_id);
        if (!$project) {
            $this->deny_access();
        }

        if ((int) $project->user_id === (int) $user_id) {
            return 'admin';
        }

        // בדיקת שיתוף
        $share =
            $this->Project_model->get_user_project_permission($project_id, $user_id);
        // אמור להחזיר 'viewer' / 'editor' / 'admin' / null

        if (!$share) {
            $this->deny_access();
        }

        // בדיקת רמת הרשאה
        if ($required === 'edit' && $share === 'viewer') {
            $this->deny_access();
        }

        return $share;
    }

    private function deny_access()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success' => false,
                'message' => 'Forbidden'
            ]);
            exit;
        }

        show_error('Forbidden', 403);
    }


}
