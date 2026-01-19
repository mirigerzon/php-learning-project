<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_model extends CI_Model
{
    public function get_project_tasks($project_id, $status_filter = null)
    {
        // מביאים קודם את owner_id של הפרויקט
        $project = $this->db->select('user_id')  // או owner_id לפי איך שהטבלה בנויה
            ->where('project_id', $project_id)
            ->get('projects')
            ->row();

        $owner_id = $project->user_id ?? null;

        $this->db->where('project_id', $project_id);

        if ($status_filter === 'done') {
            $this->db->where('status', 1);
        } elseif ($status_filter === 'pending') {
            $this->db->where('status', 0);
        } elseif ($status_filter === 'late') {
            $this->db->where('status', 0);
            $this->db->where('due_date <', date('Y-m-d'));
        }

        $tasks = $this->db->order_by('created_at', 'DESC')
            ->get('tasks')
            ->result();

        // מוסיפים לכל משימה את owner_id של הפרויקט
        foreach ($tasks as $task) {
            $task->project_owner_id = $owner_id;
        }

        return $tasks;
    }


    public function get_task_by_id($task_id)
    {
        return $this->db
            ->where('task_id', $task_id)
            ->get('tasks')
            ->row();
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

    public function get_task_images($task_id)
    {

        return $this->db
            ->where('task_id', $task_id)
            ->get('task_images')
            ->result();
    }

    public function get_task_image($image_id)
    {
        return $this->db
            ->where('id', $image_id)
            ->get('task_images')
            ->row();
    }

    public function add_task_image($task_id, $image_path)
    {
        return $this->db->insert('task_images', [
            'task_id' => $task_id,
            'image_path' => $image_path,
            'uploaded_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function delete_task_image($image_id)
    {
        return $this->db
            ->where('id', $image_id)
            ->delete('task_images');
    }

    public function count_task_images($task_id)
    {
        $count = $this->db->where('task_id', $task_id)
            ->count_all_results('task_images');
        return $count;
    }

    public function get_status_counts()
    {
        $sql = "
        SELECT 
            CASE 
                WHEN status = 0 THEN 'Pending'
                WHEN status = 1 THEN 'Done'
                ELSE 'In Progress'
            END AS task_status,
            COUNT(*) as count
        FROM tasks
        GROUP BY task_status
    ";
        return $this->db->query($sql)->result_array();
    }

    public function get_count_per_user()
    {
        $this->db->select('users.username, COUNT(tasks.task_id) as count');
        $this->db->from('tasks');
        $this->db->join('users', 'tasks.user_id = users.user_id');
        $this->db->group_by('tasks.user_id');
        $query = $this->db->get();
        return $query->result_array(); // ['username' => ..., 'count' => ...]
    }

    public function get_task_counts_by_status($user_id)
    {
        $sql = "
        SELECT 
            CASE 
                WHEN status = 0 THEN 'Pending'
                WHEN status = 1 THEN 'Done'
                ELSE 'In Progress'
            END AS task_status,
            COUNT(*) AS count
        FROM tasks
        WHERE project_id IN (SELECT project_id FROM projects WHERE user_id = ?)
        GROUP BY task_status
    ";
        return $this->db->query($sql, [$user_id])->result_array();
    }

    public function get_count_per_user_with_due()
    {
        $sql = "
        SELECT u.username,
               COUNT(t.task_id) AS total_tasks,
               SUM(CASE WHEN t.status = 0 AND t.due_date < CURDATE() THEN 1 ELSE 0 END) AS overdue_tasks
        FROM users u
        LEFT JOIN projects p ON p.user_id = u.user_id
        LEFT JOIN tasks t ON t.project_id = p.project_id
        GROUP BY u.user_id
    ";
        return $this->db->query($sql)->result_array();
    }

}
