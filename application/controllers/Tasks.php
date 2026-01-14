<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Task_model');
        $this->load->model('Project_model');
        $this->load->library('session'); // דרוש ל־flashdata
        $this->load->library('form_validation');
    }

    public function index($project_id)
    {
        $status_filter = $this->input->get('status'); // null / pending / done / late
        $tasks = $this->Task_model->get_project_tasks($project_id, $status_filter);
        $project = $this->Project_model->get_project($project_id);

        foreach ($tasks as $task) {
            $task->image_count = $this->Task_model->count_task_images($task->task_id);
        }

        $data = [
            'main_view' => 'tasks/tasks',
            'tasks' => $tasks,
            'project_id' => $project_id,
            'project' => $project,
            'status_filter' => $status_filter,
            'success' => $this->session->flashdata('success') // הצגת הודעת הצלחה
        ];

        $this->load->view('layouts/main', $data);
    }

    public function view($task_id)
    {
        $task = $this->Task_model->get_task_by_id($task_id);
        if (!$task) {
            show_404();
        }

        $task_images = $this->Task_model->get_task_images($task_id);

        $data = [
            'main_view' => 'tasks/task_view',
            'task' => $task,
            'task_images' => $task_images
        ];

        $this->load->view('layouts/main', $data);
    }

    public function add($project_id)
    {
        $this->form_validation->set_rules('task_title', 'Task Title', 'required');
        $this->form_validation->set_rules('task_body', 'Task Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'main_view' => 'tasks/add',
                'project_id' => $project_id
            ];
            $this->load->view('layouts/main', $data);
        } else {
            $task_data = [
                'project_id' => $project_id,
                'task_title' => $this->input->post('task_title'),
                'task_body' => $this->input->post('task_body'),
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->Task_model->add_task($task_data);

            // Flash message
            $this->session->set_flashdata('success', 'Task added successfully!');
            redirect("tasks/index/{$project_id}");
        }
    }

    public function add_ajax($project_id)
    {
        $this->form_validation->set_rules('task_title', 'Task Title', 'required');
        $this->form_validation->set_rules('task_body', 'Task Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            echo json_encode([
                'success' => false,
                'message' => validation_errors('<p>', '</p>')
            ]);
        } else {
            $due_date = $this->input->post('task_due_date');

            $task_data = [
                'project_id' => $project_id,
                'task_title' => $this->input->post('task_title'),
                'task_body' => $this->input->post('task_body'),
                'due_date' => !empty($due_date) ? $due_date : null,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->Task_model->add_task($task_data);

            echo json_encode([
                'success' => true,
                'message' => 'Task added successfully!',
                'redirect' => base_url("/tasks/index/{$project_id}
                // ") // מוסיפים URL ל־redirect
            ]);
        }
    }

    public function edit($project_id, $task_id)
    {
        $task = $this->Task_model->get_task($project_id, $task_id);

        if (!$task)
            show_404();

        $this->form_validation->set_rules('task_title', 'Task Title', 'required');
        $this->form_validation->set_rules('task_body', 'Task Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'main_view' => 'tasks/edit',
                'task' => $task
            ];
            $this->load->view('layouts/main', $data);
        } else {
            $due_date = $this->input->post('task_due_date');

            $task_data = [
                'task_title' => $this->input->post('task_title'),
                'task_body' => $this->input->post('task_body'),
                'due_date' => !empty($due_date) ? $due_date : null,
            ];

            $this->Task_model->update_task($project_id, $task_id, $task_data);

            // Flash message
            $this->session->set_flashdata('success', 'Task updated successfully!');
            redirect("tasks/index/{$project_id}");
        }
    }

    public function delete($project_id, $task_id)
    {
        $this->Task_model->delete_task($project_id, $task_id);

        // Flash message
        $this->session->set_flashdata('success', 'Task deleted successfully!');
        redirect("tasks/index/{$project_id}");
    }

    public function mark_as_done($project_id, $task_id)
    {
        $this->Task_model->mark_as_done($project_id, $task_id);
        $this->session->set_flashdata('success', 'Task marked as done!');
        redirect("tasks/index/{$project_id}");
    }

    public function mark_as_un_done($project_id, $task_id)
    {
        $this->Task_model->mark_as_un_done($project_id, $task_id);
        $this->session->set_flashdata('success', 'Task marked as pending!');
        redirect("tasks/index/{$project_id}");
    }

    public function upload_images()
    {
        $task_id = $this->input->post('task_id');

        if (!empty($_FILES['images']['name'][0])) {
            $files = $_FILES;
            $count = count($_FILES['images']['name']);

            for ($i = 0; $i < $count; $i++) {
                $_FILES['image']['name'] = $files['images']['name'][$i];
                $_FILES['image']['type'] = $files['images']['type'][$i];
                $_FILES['image']['tmp_name'] = $files['images']['tmp_name'][$i];
                $_FILES['image']['error'] = $files['images']['error'][$i];
                $_FILES['image']['size'] = $files['images']['size'][$i];

                $config['upload_path'] = './uploads/tasks/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name'] = time() . '_' . $files['images']['name'][$i];

                $this->load->library('upload');
                $this->upload->initialize($config);

                if ($this->upload->do_upload('image')) {
                    $data = $this->upload->data();
                    $this->Task_model->add_task_image($task_id, 'uploads/tasks/' . $data['file_name']);
                    $this->session->set_flashdata('success', 'Images uploaded successfully!');
                } else {
                    echo $this->upload->display_errors();
                    exit;
                }
            }
        }

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
        $due_date = $this->input->post('due_date');

        $this->Task_model->update_task(
            $project_id,
            $task_id,
            ['due_date' => $due_date]
        );

        $this->session->set_flashdata('success', 'Due date updated successfully!');

        redirect("tasks/index/{$task_id}");
    }
}
