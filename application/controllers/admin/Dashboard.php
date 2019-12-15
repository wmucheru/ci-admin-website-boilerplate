<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct(){
		parent::__construct();

        $this->auth_model->set_login_redirect();
	}
    
    public function index(){
        $data['body_id'] = 'dash-bd';
        $data['page_title'] = 'Dashboard';
        $data['page_content'] = 'admin/dashboard';
        
        $this->load->view('inc/template-admin', $data);
	}
}
