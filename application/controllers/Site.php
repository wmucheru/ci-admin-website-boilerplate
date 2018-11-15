<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {

	public function __construct(){
        parent::__construct();
    }
    
    function index(){
        render_page('home', 'Home', 'home-bd');
    }

    function about(){
        render_page('about', 'About Us', 'about-bd');
    }

    function contacts(){
        render_page('contacts', 'Contact Us', 'contacts-bd');
    }
}
