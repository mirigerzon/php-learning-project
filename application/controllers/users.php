<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['form', 'url']);
        $this->load->model('User_model');
    }

    public function login()
    {
        if ($this->input->post()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->User_model->get_by_username($username);

            if ($user && password_verify($password, $user->password)) {
                $this->session->set_userdata([
                    'user_id' => $user->user_id,
                    'username' => $user->username
                ]);
                redirect('home');
            } else {
                $data['error'] = "Username or password is incorrect";
                $this->load->view('users/login_view', $data);
            }
        } else {
            $this->load->view('users/login_view');
        }
    }

    public function register()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('users/register_view');
        } else {
            $data = [
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                'username' => $this->input->post('username'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->User_model->create_user($data);
            redirect('home');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('home');
    }
}
