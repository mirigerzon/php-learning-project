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
        $data['title'] = 'My App/Shares';
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

    public function share_ajax_form($project_id = null)
    {
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
        $project_id = $this->input->post('project_id');
        $this->require_project_permission($project_id, 'edit');

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

        // בדיקה אם יש הרשאה מספיקה
        $share_levels = ['view' => 1, 'edit' => 2, 'admin' => 3];
        $required_level = $share_levels[$required] ?? 1;
        $current_level = $share_levels[$share] ?? 0;

        if ($current_level < $required_level) {
            $this->deny_access();
        }

        return $share;
    }

    private function deny_access()
    {
        show_error('You do not have permission to access this resource');
        exit;
    }
}