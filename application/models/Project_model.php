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
        $this->db->where('project_id', $project_id);
        return $this->db->delete('projects');
    }
}
