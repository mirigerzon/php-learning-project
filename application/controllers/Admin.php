<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('User_model');
        $this->load->model('Project_model');
        $this->load->model('Task_model');

        // רק מנהלים
        if (!$this->session->userdata('user_id') || !$this->session->userdata('is_admin')) {
            show_error('Unauthorized', 403);
        }
    }

    /* =======================
       DASHBOARD USERS
       ======================= */
    public function index()
    {
        $data['users'] = $this->User_model->get_all_with_project_stats();
        $data['title'] = 'Admin - Users';
        $data['main_view'] = 'admin/users';
        $this->load->view('layouts/main', $data);
    }

    /* =======================
       TOGGLE ADMIN
       ======================= */
    public function toggle_admin()
    {
        if (!$this->input->post())
            redirect('admin');

        $user_id = (int) $this->input->post('user_id');
        $make_admin = $this->input->post('is_admin') ? 1 : 0;

        // לא ניתן לשנות את עצמך
        if ($user_id === (int) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot change your own admin status.');
            redirect('admin');
        }

        $user = $this->User_model->get_by_id($user_id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('admin');
        }

        // לא ניתן להוריד את המנהל האחרון
        if (!$make_admin && $user->is_admin) {
            $admin_count = $this->User_model->count_admins();
            if ($admin_count <= 1) {
                $this->session->set_flashdata('error', 'Cannot demote the last admin.');
                redirect('admin');
            }
        }

        // עדכון הסטטוס של Admin
        $this->User_model->set_admin($user_id, $make_admin);

        // אם מקודם ל-Admin וההרשאה לפרויקטים ריקה, נעדכן ל-view
        if ($make_admin && ($user->project_permission === null)) {
            $this->User_model->set_project_permission($user_id, 'view');
        }

        if (!$make_admin) {
            $this->User_model->set_project_permission($user_id, null);
        }

        $this->session->set_flashdata('success', $make_admin ? 'User promoted to admin.' : 'User demoted from admin.');
        redirect('admin');
    }

    /* =======================
       DELETE USER
       ======================= */
    public function delete_user()
    {
        if (!$this->input->post())
            redirect('admin');

        $user_id = (int) $this->input->post('user_id');
        if ($user_id === (int) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot delete your own account.');
            redirect('admin');
        }

        $user = $this->User_model->get_by_id($user_id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('admin');
        }

        if ($user->is_admin) {
            $admin_count = $this->User_model->count_admins();
            if ($admin_count <= 1) {
                $this->session->set_flashdata('error', 'Cannot delete the last admin.');
                redirect('admin');
            }
        }

        $this->db->trans_start();

        $projects = $this->Project_model->get_user_projects($user_id);
        foreach ($projects as $p) {
            $tasks = $this->Task_model->get_project_tasks($p->project_id);
            foreach ($tasks as $t) {
                $images = $this->Task_model->get_task_images($t->task_id);
                foreach ($images as $img) {
                    if (file_exists($img->image_path)) {
                        @unlink($img->image_path);
                    } elseif (file_exists(FCPATH . $img->image_path)) {
                        @unlink(FCPATH . $img->image_path);
                    }
                    $this->Task_model->delete_task_image($img->id);
                }
                $this->Task_model->delete_task($p->project_id, $t->task_id);
            }
            $this->Project_model->delete_project($p->project_id);
        }

        $this->User_model->delete_user($user_id);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'There was an error deleting the user.');
        } else {
            $this->session->set_flashdata('success', 'User deleted successfully.');
        }

        redirect('admin');
    }

    /* =======================
       ADMIN PROJECTS LIST
       ======================= */
    public function projects()
    {
        $projects = $this->Project_model->get_all_projects_with_owner();

        foreach ($projects as $project) {
            $project->shared_users = $this->Project_model->get_project_shares($project->project_id);
        }

        $data = [
            'main_view' => 'admin/projects',
            'projects' => $projects,
            'title' => 'Admin - Projects'
        ];

        $this->load->view('layouts/main', $data);
    }

    public function set_project_permission_ajax()
    {
        $user_id = (int) $this->input->post('user_id');
        $permission = $this->input->post('permission');

        // מביאים את המשתמש
        $user = $this->User_model->get_by_id($user_id);
        if (!$user || !$user->is_admin) {
            echo json_encode(['success' => false, 'message' => 'User not found or not admin']);
            return;
        }

        // בודקים שההרשאה חוקית
        if (!in_array($permission, ['view', 'edit', 'null'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid permission']);
            return;
        }

        // שמירה במודל
        $this->User_model->set_project_permission($user_id, $permission);

        echo json_encode(['success' => true]);
    }

}
