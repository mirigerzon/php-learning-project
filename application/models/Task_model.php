<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_model extends CI_Model
{
    public function get_project_tasks($project_id, $status_filter = null)
    {
        $this->db->where('project_id', $project_id);

        if ($status_filter === 'done') {
            $this->db->where('status', 1);
        } elseif ($status_filter === 'pending') {
            $this->db->where('status', 0);
        }

        return $this->db->order_by('created_at', 'DESC')
            ->get('tasks')
            ->result();
    }

    public function add_task($data)
    {
        return $this->db->insert('tasks', $data);
    }

    public function mark_as_done($project_id, $task_id)
    {
        return $this->db
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->update('tasks', ['status' => 1]);
    }

    public function mark_as_un_done($project_id, $task_id)
    {
        return $this->db
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->update('tasks', ['status' => 0]);
    }

    public function delete_task($project_id, $task_id)
    {
        return $this->db
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->delete('tasks');
    }

    public function get_task($project_id, $task_id)
    {
        return $this->db
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->get('tasks')
            ->row();
    }

    public function update_task($project_id, $task_id, $data)
    {
        return $this->db
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->update('tasks', $data);
    }
}
