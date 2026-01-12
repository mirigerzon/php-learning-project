<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Task_model');
        $this->load->model('Project_model');
    }

    public function index($project_id)
    {
        $status_filter = $this->input->get('status'); // null / pending / done
        $tasks = $this->Task_model->get_project_tasks($project_id, $status_filter);

        $project = $this->Project_model->get_project($project_id);

        $data = [
            'main_view' => 'tasks/tasks',
            'tasks' => $tasks,
            'project_id' => $project_id,
            'project' => $project,
            'status_filter' => $status_filter
        ];

        $this->load->view('layouts/main', $data);
    }

    public function add($project_id)
    {
        $this->load->library('form_validation');

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
            redirect("tasks/index/{$project_id}");
        }
    }

    public function mark_as_done($project_id, $task_id)
    {
        $this->Task_model->mark_as_done($project_id, $task_id);
        redirect("tasks/index/{$project_id}");
    }

    public function mark_as_un_done($project_id, $task_id)
    {
        $this->Task_model->mark_as_un_done($project_id, $task_id);
        redirect("tasks/index/{$project_id}");
    }

    public function delete($project_id, $task_id)
    {
        $this->Task_model->delete_task($project_id, $task_id);
        redirect("tasks/index/{$project_id}");
    }

    public function edit($project_id, $task_id)
    {
        $this->load->library('form_validation');

        $task = $this->Task_model->get_task($project_id, $task_id);

        if (!$task) {
            show_404();
        }

        $this->form_validation->set_rules('task_title', 'Task Title', 'required');
        $this->form_validation->set_rules('task_body', 'Task Description', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'main_view' => 'tasks/edit',
                'task' => $task
            ];
            $this->load->view('layouts/main', $data);
        } else {
            $task_data = [
                'task_title' => $this->input->post('task_title'),
                'task_body' => $this->input->post('task_body')
            ];

            $this->Task_model->update_task($project_id, $task_id, $task_data);
            redirect("tasks/index/{$project_id}");
        }
    }

}