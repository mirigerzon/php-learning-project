<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_model extends CI_Model
{
    public function get_project_tasks($project_id, $user_id = null, $from = 'projects', $status_filter = null)
    {
        if ($from === 'shares' && $user_id) {
            // משתמש רגיל ב-shares – רואה רק את עצמו
            $sql = "SELECT t.*, p.user_id AS project_owner_id, ta.is_done, ta.done_at
            FROM tasks t
            JOIN projects p ON p.project_id = t.project_id
            JOIN task_assignees ta ON ta.task_id = t.task_id AND ta.user_id = ?
            WHERE t.project_id = ?";
            $params = [$user_id, $project_id];

            $assigned_tasks = $this->get_assigned_task_ids($user_id, $project_id);
            if (empty($assigned_tasks))
                return [];
            $placeholders = implode(',', array_fill(0, count($assigned_tasks), '?'));
            $sql .= " AND t.task_id IN ($placeholders)";
            $params = array_merge($params, $assigned_tasks);

            // פילטר סטטוס לפי is_done
            if ($status_filter === 'done')
                $sql .= " AND ta.is_done = 1";
            elseif ($status_filter === 'pending')
                $sql .= " AND ta.is_done = 0";
            elseif ($status_filter === 'late')
                $sql .= " AND ta.is_done = 0 AND t.due_date < CURDATE()";

        } else {
            // מנהל או view רגיל – רואה אם כולם סיימו
            $sql = "SELECT t.*, p.user_id AS project_owner_id,
                   (SELECT COUNT(*) FROM task_assignees WHERE task_id = t.task_id AND is_done = 1) =
                   (SELECT COUNT(*) FROM task_assignees WHERE task_id = t.task_id) AS all_done
            FROM tasks t
            JOIN projects p ON p.project_id = t.project_id
            WHERE t.project_id = ?";
            $params = [$project_id];

            if ($status_filter === 'done')
                $sql .= " AND t.status = 1";
            elseif ($status_filter === 'pending')
                $sql .= " AND t.status = 0";
            elseif ($status_filter === 'late')
                $sql .= " AND t.status = 0 AND t.due_date < CURDATE()";
        }

        $sql .= " ORDER BY t.created_at DESC";

        return $this->db->query($sql, $params)->result();
    }

    public function get_task_by_id($task_id)
    {
        return $this->db
            ->where('task_id', $task_id)
            ->get('tasks')
            ->row();
    }

    public function set_task_status($project_id, $task_id, $status)
    {
        $status = ($status == 1) ? 1 : 0;

        return $this->db
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->update('tasks', ['status' => $status]);
    }

    public function set_task_status_for_user($task_id, $user_id, $done = 1)
    {
        return $this->db
            ->where('task_id', $task_id)
            ->where('user_id', $user_id)
            ->update('task_assignees', [
                'is_done' => $done,
                'done_at' => $done ? date('Y-m-d H:i:s') : null
            ]);
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

    // מביא את כל ה-task_id שהוקצו למשתמש בפרויקט מסוים
    public function get_assigned_task_ids($user_id, $project_id)
    {
        // מביאים את כל המשימות של הפרויקט
        $tasks = $this->db
            ->select('task_id')
            ->from('tasks')
            ->where('project_id', $project_id)
            ->get()
            ->result_array();

        $task_ids_in_project = array_column($tasks, 'task_id');

        if (empty($task_ids_in_project))
            return [];

        // מחזירים רק את המשימות שהוקצו למשתמש
        $assigned = $this->db
            ->select('task_id')
            ->from('task_assignees')
            ->where('user_id', $user_id)
            ->where_in('task_id', $task_ids_in_project)
            ->get()
            ->result_array();

        return array_column($assigned, 'task_id');
    }

    public function update_user_tasks($user_id, $project_id, $task_ids)
    {
        // 1️⃣ מביאים את כל ה-task_id של הפרויקט
        $tasks = $this->db
            ->select('task_id')
            ->from('tasks')
            ->where('project_id', $project_id)
            ->get()
            ->result_array();

        $task_ids_in_project = array_column($tasks, 'task_id');

        // 2️⃣ מוחקים הקצאות ישנות של המשתמש רק למשימות שבפרויקט
        if (!empty($task_ids_in_project)) {
            $this->db->where('user_id', $user_id)
                ->where_in('task_id', $task_ids_in_project)
                ->delete('task_assignees');
        }

        // 3️⃣ מוסיפים הקצאות חדשות
        if (!empty($task_ids)) {
            $data = [];
            foreach ($task_ids as $task_id) {
                $data[] = [
                    'task_id' => $task_id,
                    'user_id' => $user_id
                ];
            }
            $this->db->insert_batch('task_assignees', $data);
        }
    }

    public function create_task_with_creator($project_id, $user_id, $task_input)
    {
        $this->db->trans_start();

        $task_data = [
            'project_id' => $project_id,
            'task_title' => $task_input['task_title'],
            'task_body' => $task_input['task_body'],
            'due_date' => $task_input['due_date'],
            'status' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tasks', $task_data);
        $task_id = $this->db->insert_id();

        $this->db->insert('task_assignees', [
            'task_id' => $task_id,
            'user_id' => $user_id,
            'is_done' => 0,
            'done_at' => null
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return [
            'task_id' => $task_id,
            'task_title' => $task_data['task_title'],
            'task_body' => $task_data['task_body'],
            'created_at' => $task_data['created_at'],
            'due_date' => $task_data['due_date'],
            'status' => 0,
        ];
    }


    public function is_task_done_by_all($task_id)
    {
        $total = $this->db->where('task_id', $task_id)
            ->count_all_results('task_assignees');

        $done_count = $this->db->where('task_id', $task_id)
            ->where('is_done', 1)
            ->count_all_results('task_assignees');

        return $total > 0 && $total === $done_count;
    }

}
