<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_username($username)
    {
        return $this->db->get_where('users', ['username' => $username])->row();
    }

    public function create_user($data)
    {
        return $this->db->insert('users', $data);
    }

    public function get_all_with_project_stats()
    {
        $sql = "SELECT u.*, 
            (SELECT COUNT(*) FROM projects p WHERE p.user_id = u.user_id AND EXISTS (SELECT 1 FROM tasks t WHERE t.project_id = p.project_id) AND NOT EXISTS (SELECT 1 FROM tasks t WHERE t.project_id = p.project_id AND t.status = 0)) AS completed_projects,
            (SELECT COUNT(*) FROM projects p WHERE p.user_id = u.user_id AND (NOT EXISTS (SELECT 1 FROM tasks t WHERE t.project_id = p.project_id) OR EXISTS (SELECT 1 FROM tasks t WHERE t.project_id = p.project_id AND t.status = 0))) AS in_progress_projects
            FROM users u";

        return $this->db->query($sql)->result();
    }

    public function get_by_id($user_id)
    {
        return $this->db->get_where('users', ['user_id' => $user_id])->row();
    }

    public function set_admin($user_id, $is_admin = 0)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->update('users', ['is_admin' => (int) $is_admin]);
    }

    public function count_admins()
    {
        $this->db->where('is_admin', 1);
        return (int) $this->db->count_all_results('users');
    }

    public function delete_user($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->delete('users');
    }

    public function get_all_users()
    {
        return $this->db->select('user_id, username')->get('users')->result();
    }

}
