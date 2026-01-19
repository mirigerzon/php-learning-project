<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_user_projects($user_id)
    {
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('projects');
        return $query->result();
    }

    public function get_user_projects_with_status($user_id)
    {
        $sql = "
        SELECT p.*, 
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM tasks t 
                    WHERE t.project_id = p.project_id AND t.status = 0
                ) THEN 'Open'
                ELSE 'Closed'
            END AS project_status
        FROM projects p
        WHERE p.user_id = ? OR EXISTS (
            SELECT 1 
            FROM project_shares ps 
            WHERE ps.project_id = p.project_id AND ps.user_id = ?
        )
        ORDER BY p.created_at DESC
    ";

        return $this->db->query($sql, [$user_id, $user_id])->result();
    }

    public function get_project($project_id)
    {
        $this->db->where('project_id', $project_id);
        $query = $this->db->get('projects');
        return $query->row();
    }

    public function add_project($data)
    {
        return $this->db->insert('projects', $data);
    }

    public function update_project($project_id, $data)
    {
        $this->db->where('project_id', $project_id);
        return $this->db->update('projects', $data);
    }

    public function delete_project($project_id)
    {
        // מוחק קודם את המשימות של הפרויקט
        $this->db->where('project_id', $project_id);
        $this->db->delete('tasks');

        // ואז מוחק את הפרויקט עצמו
        $this->db->where('project_id', $project_id);
        return $this->db->delete('projects');
    }

    public function share_project($project_id, $user_id, $role)
    {
        // בדיקה אם כבר קיים שיתוף
        $exists = $this->db
            ->where('project_id', $project_id)
            ->where('user_id', $user_id)
            ->get('project_shares')
            ->row();

        if ($exists) {
            if ($role) {
                // update
                return $this->db->update('project_shares', ['role' => $role], ['project_id' => $project_id, 'user_id' => $user_id]);
            } else {
                // delete
                return $this->db->delete('project_shares', ['project_id' => $project_id, 'user_id' => $user_id]);
            }
        } else {
            if ($role) {
                return $this->db->insert('project_shares', [
                    'project_id' => $project_id,
                    'user_id' => $user_id,
                    'role' => $role
                ]);
            }
        }
        return true; // no action needed
    }

    public function get_project_shares($project_id)
    {
        $this->db->select('ps.*, u.username');
        $this->db->from('project_shares ps');
        $this->db->join('users u', 'u.user_id = ps.user_id', 'left');
        $this->db->where('ps.project_id', $project_id);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_shared_projects_for_user($user_id)
    {
        $this->db->select('p.project_id, p.project_title, p.project_body, p.user_id AS owner_id, ps.role');
        $this->db->from('project_shares ps');
        $this->db->join('projects p', 'p.project_id = ps.project_id');
        $this->db->where('ps.user_id', $user_id);
        return $this->db->get()->result_array();
    }


    public function get_users_with_roles($project_id)
    {
        return $this->db
            ->select('
            users.user_id,
            users.username,
            project_shares.role
        ')
            ->from('users')
            ->join(
                'project_shares',
                'project_shares.user_id = users.user_id 
             AND project_shares.project_id = ' . (int) $project_id,
                'left'
            )
            ->order_by('users.username', 'ASC')
            ->get()
            ->result();
    }

    public function get_status_counts()
    {
        $sql = "
        SELECT 
            CASE 
                WHEN EXISTS (
                    SELECT 1 
                    FROM tasks t 
                    WHERE t.project_id = p.project_id AND t.status = 0
                ) THEN 'Open'
                ELSE 'Closed'
            END AS project_status,
            COUNT(*) as count
        FROM projects p
        GROUP BY project_status
    ";
        return $this->db->query($sql)->result_array();
    }

    public function get_count_per_user()
    {
        $this->db->select('users.username, COUNT(projects.project_id) as count');
        $this->db->from('projects');
        $this->db->join('users', 'projects.user_id = users.user_id');
        $this->db->group_by('projects.user_id');
        $query = $this->db->get();
        return $query->result_array(); // ['username' => ..., 'count' => ...]
    }

    public function get_user_projects_with_task_counts($user_id)
    {
        $sql = "
        SELECT p.project_id, p.project_title, COUNT(t.task_id) AS task_count
        FROM projects p
        LEFT JOIN tasks t ON t.project_id = p.project_id
        WHERE p.user_id = ? OR EXISTS (
            SELECT 1 FROM project_shares ps WHERE ps.project_id = p.project_id AND ps.user_id = ?
        )
        GROUP BY p.project_id
        ORDER BY p.created_at DESC
    ";
        return $this->db->query($sql, [$user_id, $user_id])->result_array();
    }

    public function get_all_projects_with_owner()
    {
        $this->db->select('p.*, u.username AS owner_name');
        $this->db->from('projects p');
        $this->db->join('users u', 'u.user_id = p.user_id', 'left');
        $this->db->order_by('p.created_at', 'DESC');
        return $this->db->get()->result();
    }

}
