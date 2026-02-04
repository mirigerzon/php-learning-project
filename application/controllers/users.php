<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->helper(['form', 'url']);
        $this->load->model('User_model');
    }

    // --- LOGIN ---
    public function login()
    {
        $data = []; // נתונים שנשלחים ל-view

        if ($this->input->post()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->User_model->get_by_username($username);

            if ($user && password_verify($password, $user->password)) {
                $this->session->set_userdata([
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'is_admin' => (isset($user->is_admin) && $user->is_admin) ? 1 : 0,
                    'project_permission' => $user->project_permission ?? null,
                ]);
                redirect('home');
            } else {
                $data['error'] = "Username or password is incorrect";
            }
        }

        $data['main_view'] = 'users/login_view';
        $data['title'] = 'Login';
        $this->load->view('layouts/main', $data);
    }

    // --- REGISTER ---
    public function register()
    {
        $data = [];

        $this->form_validation->set_rules('first_name', 'First Name', 'required', [
            'required' => 'The %s field is required.'
        ]);

        $this->form_validation->set_rules('last_name', 'Last Name', 'required', [
            'required' => 'The %s field is required.'
        ]);

        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]', [
            'required' => 'The %s field is required.',
            'is_unique' => 'The %s already exists.'
        ]);

        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]', [
            'required' => 'The %s field is required.',
            'min_length' => 'The %s must be at least 6 characters long.'
        ]);

        if ($this->form_validation->run() === TRUE) {
            $userData = [
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->User_model->create_user($userData);
            $this->session->set_flashdata('success', 'Registration successful! You can now log in.');
            redirect('home');
        }

        $data['main_view'] = 'users/register_view';
        $data['title'] = 'Register';
        $this->load->view('layouts/main', $data);
    }

    // --- LOGOUT ---
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('home');
    }
}
