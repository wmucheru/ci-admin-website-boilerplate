<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        $data['page_content'] = 'auth/login';
        $data['page_title'] = 'Login';

        $this->load->view('inc/template', $data);
    }

    function login_proc(){
        # redirect('dashboard');

        $email = $this->input->post('email-address');
        $password = $this->input->post('authkey');
        $persist_login = 'true';

        $this->form_validation->set_rules('email-address', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('authkey', 'Password', 'required');

        if($this->form_validation->run() === FALSE){
            $this->index();
        }
        else{

            $remember = $persist_login == 'true';

            if($this->auth_model->is_account_verified($email)){

                if ($this->aauth->login($email, $password, $remember)){
                    $this->dashboard();
                }
                else{
                    $this->session->set_flashdata('login_fail', 'Please enter the correct login details');
                    $this->index();
                }
            }
            else{
                $this->session->set_flashdata('login_fail', 'Your account is inactive or has been suspended');
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
        redirect('accounts/login');
    }
}
