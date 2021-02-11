<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    function __construct(){
		parent::__construct();

        $this->auth_model->set_login_redirect();
	}

    function index(){
        $data['stats'] = $this->reports_model->getSummaryStats();
        render_admin('admin/dashboard', 'Dashboard', 'dash-bd', $data);
	}
}
