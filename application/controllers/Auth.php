<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {

    function __construct(){
        parent::__construct();

        // Create default user to test with
        # var_dump($this->auth_model->create_user('admin@example.com', '123456', 'admin'));
    }

    function index() {

        if($this->auth_model->is_logged_in()){
            redirect('admin/dashboard');
        }
        else{
            render_auth('auth/login', 'Login', 'auth-bd');
        }
    }

    function login_proc(){
        # redirect('dashboard');

        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $persist_login = 'true';

        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if($this->form_validation->run() === FALSE){
            $this->index();
        }
        else{
            $remember = $persist_login == 'true';

            if(!$this->auth_model->is_account_verified($email)){
                $this->session->set_flashdata('login_fail', 'Account is inactive');
                $this->index();
            }
            else if($this->aauth->login($email, $password, $remember)){

                # Redirect to the page you were in before session expired
                $referrer = $this->session->userdata('referrer');
                $uri = !empty($referrer) ? $referrer : 'admin/dashboard';

                $this->session->unset_userdata('referrer');
                redirect($uri);
            }
            else{
                $this->session->set_flashdata('login_fail', 'Please enter the correct login details');
                $this->index();
            }
        }
    }

    # Redirect to dashboard
    function dashboard(){
        redirect('admin/dashboard');
    }

    function register_proc(){
        # var_dump($this->input->post());
    }

    function logout(){
        $this->aauth->logout();
        $this->auth_model->set_redirect_referrer();

        $this->index();
    }
}