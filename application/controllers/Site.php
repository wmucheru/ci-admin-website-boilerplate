<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {

	public function __construct(){
        parent::__construct();
    }
    
    function index(){
        $data['page_title'] = 'Home';
        $data['page_content'] = 'home';
        $data['body_class'] = 'home-bd';
        
        $this->load->view('inc/template', $data);
    }

    function about(){
        $data['page_title'] = 'About Us';
        $data['page_content'] = 'about';
        $data['body_class'] = 'about-bd';
        
        $this->load->view('inc/template', $data);
    }

    function contacts(){
        $data['page_title'] = 'Contact Us';
        $data['page_content'] = 'contacts';
        $data['body_class'] = 'contacts';
        
        $this->load->view('inc/template', $data);
    }
}
