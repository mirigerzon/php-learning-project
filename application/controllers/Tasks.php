<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Task_model');
        $this->load->model('Project_model');
        $this->load->library('form_validation');

        // בדיקת התחברות בסיסית
        if (!$this->session->user_id) {
            redirect('users/login');
        }
    }

    public function index($project_id)
    {
        $status_filter = $this->input->get('status');
        $from = $this->input->get('from') ?? 'projects';
        $user_id = $this->session->user_id;

        $tasks = $this->Task_model->get_project_tasks($project_id, $user_id, $from, $status_filter);
        $project = $this->Project_model->get_project($project_id);

        // קבלת הרשאה של המשתמש לפרויקט
        $user_role = $this->Project_model->get_user_project_permission($project_id, $user_id);
        $project->user_role = $user_role;

        foreach ($tasks as $task) {
            $task->image_count = $this->Task_model->count_task_images($task->task_id);
        }

        $data = [
            'main_view' => 'tasks/tasks',
            'tasks' => $tasks,
            'project' => $project,
            'project_id' => $project_id,
            'status_filter' => $status_filter,
            'from' => $from
        ];

        $this->load->view('layouts/main', $data);
    }



    public function view($project_id, $task_id)
    {
        $task = $this->Task_model->get_task_by_id($task_id);
        if (!$task)
            show_404();

        $data = [
            'main_view' => 'tasks/task_view',
            'project_id' => $project_id,
            'task' => $task,
            'task_images' => $this->Task_model->get_task_images($task_id)
        ];

        $this->load->view('layouts/main', $data);
    }

    public function add($project_id)
    {
        $this->form_validation
            ->set_rules('task_title', 'Task Title', 'required')
            ->set_rules('task_body', 'Task Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('layouts/main', [
                'main_view' => 'tasks/add',
                'project_id' => $project_id
            ]);
            return;
        }

        // 1. הוספת המשימה לטבלת tasks
        $task_id = $this->Task_model->add_task([
            'project_id' => $project_id,
            'task_title' => $this->input->post('task_title'),
            'task_body' => $this->input->post('task_body'),
            'due_date' => $this->input->post('task_due_date') ?: null,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 2. הוספת הרשומה שלך (המנהל/יוצר) לטבלת task_assignees
        $user_id = $this->session->user_id;
        $this->Task_model->add_task_assignee([
            'task_id' => $task_id,
            'user_id' => $user_id,
            'is_done' => 0,
            'done_at' => null
        ]);

        $this->session->set_flashdata('success', 'Task added successfully!');
        redirect("tasks/index/{$project_id}");
    }

    public function add_ajax_form($project_id)
    {
        $this->load->view('tasks/add_form', ['project_id' => $project_id]);
    }

    public function add_ajax($project_id)
    {
        $this->form_validation
            ->set_rules('task_title', 'Task Title', 'required')
            ->set_rules('task_body', 'Task Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors('<p>', '</p>')
            ]);
            return;
        }

        $task_data = [
            'project_id' => $project_id,
            'task_title' => $this->input->post('task_title'),
            'task_body' => $this->input->post('task_body'),
            'due_date' => $this->input->post('task_due_date') ?: null,
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $task_id = $this->Task_model->add_task($task_data);

        echo json_encode([
            'success' => true,
            'task' => [
                'task_id' => $task_id,
                'task_title' => $task_data['task_title'],
                'task_body' => $task_data['task_body'],
                'created_at' => $task_data['created_at'],
                'due_date' => $task_data['due_date'] ?: null,
                'status' => $task_data['status'],
                'project_id' => $project_id
            ]
        ]);

    }

    public function edit($project_id, $task_id)
    {
        $task = $this->Task_model->get_task($project_id, $task_id);
        if (!$task)
            show_404();

        $this->form_validation
            ->set_rules('task_title', 'Task Title', 'required')
            ->set_rules('task_body', 'Task Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'errors' => validation_errors()]);
                exit;
            } else {
                $this->load->view('layouts/main', [
                    'task' => $task,
                    'task_images' => $this->Task_model->get_task_images($task_id)
                ]);
                return;
            }
        }

        $this->Task_model->update_task($project_id, $task_id, [
            'task_title' => $this->input->post('task_title'),
            'task_body' => $this->input->post('task_body'),
            'due_date' => $this->input->post('task_due_date') ?: null
        ]);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            $this->session->set_flashdata('success', 'Task updated successfully!');
            redirect("tasks/view/{$project_id}/{$task_id}");
        }
    }

    public function edit_ajax_form($project_id, $task_id)
    {
        $task = $this->Task_model->get_task($project_id, $task_id);
        if (!$task)
            show_404();
        $this->load->view('tasks/edit_form', ['task' => $task, 'project_id' => $project_id]);
    }

    public function edit_ajax($project_id, $task_id)
    {
        $this->form_validation
            ->set_rules('task_title', 'Task Title', 'required')
            ->set_rules('task_body', 'Task Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $this->Task_model->update_task($project_id, $task_id, [
            'task_title' => $this->input->post('task_title'),
            'task_body' => $this->input->post('task_body'),
            'due_date' => $this->input->post('task_due_date') ?: null
        ]);

        echo json_encode(['success' => true]);
    }

    public function delete($project_id, $task_id)
    {
        $this->Task_model->delete_task($project_id, $task_id);
        $this->session->set_flashdata('success', 'Task deleted successfully!');
        redirect("tasks/index/{$project_id}");
    }

    public function mark_as_done($project_id, $task_id)
    {
        $this->Task_model->set_task_status($project_id, $task_id, 1);
        $this->session->set_flashdata('success', 'Task marked as done!');
        redirect("tasks/index/{$project_id}");
    }

    public function mark_as_un_done($project_id, $task_id)
    {
        $this->Task_model->set_task_status($project_id, $task_id, 0);
        $this->session->set_flashdata('success', 'Task marked as pending!');
        redirect("tasks/index/{$project_id}");
    }

    public function mark_as_done_for_user($task_id)
    {
        $user_id = $this->input->get('user_id');
        if (!$user_id)
            show_error('User not specified', 400);

        // מסמן את המשימה ב-task_assignees
        $this->load->model('Task_model');
        $this->Task_model->set_task_status_for_user($task_id, $user_id, 1);

        $this->session->set_flashdata('success', 'Task marked as done for you!');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function mark_as_undone_for_user($task_id)
    {
        $user_id = $this->input->get('user_id');
        if (!$user_id)
            show_error('User not specified', 400);

        $this->load->model('Task_model');
        $this->Task_model->set_task_status_for_user($task_id, $user_id, 0);

        $this->session->set_flashdata('success', 'Task marked as pending for you!');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function upload_images()
    {
        $task_id = $this->input->post('task_id');
        if (empty($_FILES['images']['name'][0]))
            redirect($_SERVER['HTTP_REFERER']);

        $files = $_FILES['images'];
        $this->load->library('upload');

        for ($i = 0; $i < count($files['name']); $i++) {
            $_FILES['image'] = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];

            $this->upload->initialize([
                'upload_path' => './uploads/tasks/',
                'allowed_types' => 'jpg|jpeg|png|gif',
                'file_name' => time() . '_' . $files['name'][$i]
            ]);

            if ($this->upload->do_upload('image')) {
                $data = $this->upload->data();
                $this->Task_model->add_task_image($task_id, 'uploads/tasks/' . $data['file_name']);
            }
        }

        $this->session->set_flashdata('success', 'Images uploaded successfully!');
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function delete_image()
    {
        $image_id = $this->input->post('image_id');
        $image = $this->Task_model->get_task_image($image_id);

        if ($image && file_exists($image->image_path)) {
            unlink($image->image_path);
        }

        $this->Task_model->delete_task_image($image_id);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function update_due_date($project_id, $task_id)
    {
        $this->Task_model->update_task(
            $project_id,
            $task_id,
            ['due_date' => $this->input->post('due_date')]
        );

        $this->session->set_flashdata('success', 'Due date updated successfully!');
        redirect("tasks/view/{$project_id}/{$task_id}");
    }

    public function admin_view($project_id)
    {
        if (
            !$this->session->userdata('user_id') ||
            !$this->session->userdata('is_admin')
        ) {
            show_error('Unauthorized', 403);
        }

        $project = $this->Project_model->get_project($project_id);
        if (!$project) {
            show_404();
        }

        $tasks = $this->Task_model->get_project_tasks($project_id);

        $data = [
            'project' => $project,
            'tasks' => $tasks,
            'main_view' => 'tasks/admin_view',
            'title' => 'Admin – Project Overview'
        ];

        $this->load->view('layouts/main', $data);
    }

    public function get_tasks_for_user($project_id = null, $user_id = null)
    {
        if (!$project_id || !$user_id)
            show_404();

        $tasks = $this->Task_model->get_project_tasks($project_id);
        $assigned_tasks = $this->Task_model->get_assigned_task_ids($user_id, $project_id);

        foreach ($tasks as $task) {
            $checked = in_array($task->task_id, $assigned_tasks) ? 'checked' : '';
            $task_id = htmlspecialchars($task->task_id);
            $task_title = htmlspecialchars($task->task_title);

            echo '<div class="form-check">';
            echo '<input class="form-check-input" type="checkbox" name="tasks[]" id="task_' . $task_id . '" value="' . $task_id . '" ' . $checked . '>';
            echo '<label class="form-check-label" for="task_' . $task_id . '">' . $task_title . '</label>';
            echo '</div>';
        }
    }



    // AJAX – שמירה של המשימות שהוקצו למשתמש
    public function save_assigned_tasks()
    {
        $user_id = $this->input->post('user_id');
        $project_id = $this->input->post('project_id');
        $task_ids = $this->input->post('tasks');

        $this->Task_model->update_user_tasks($user_id, $project_id, $task_ids ?? []);

        echo json_encode(['status' => 'success']);
    }

}
