<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {

	function __construct(){
        parent::__construct();
    }

    function test(){
        $this->messages_model->sendEmail(array());
    }

    function index(){
        render_page('home', 'Home', 'home-bd');
    }

    function about(){
        render_page('about', 'About Us', 'about-bd');
    }

    function contact(){
        render_page('contact', 'Contact Us', 'contact-bd');
    }

    function sendMessage(){
        $other = $this->input->post('other');

        $this->form_validation->set_rules('name', 'Name', 'trim|required');
        $this->form_validation->set_rules('email', 'E-mail', 'trim|required');
        $this->form_validation->set_rules('message', 'Message', 'trim|required');
        
        if(!$this->form_validation->run()){
            $this->contact();
        }

        # Check other field for misleading bots
        else if(!empty($other)){
            $this->session->set_flashdata('contact_fail', 'Could not verify sender');
            $this->contact();
        }
        
        else{

            # var_dump($this->input->post()); exit();

            $to = 'info@mysite.com';
            $subject = 'New message request from website';

            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $message = $this->input->post('message');

            # Send email
            $body = $this->site_model->emailTemplate(
                "From: $name ($email) <br><br>$message"
            );
            
            $config['mailtype'] = 'html';

            $this->email->initialize($config);
            $this->email->to($to);
            $this->email->from('site@mysite.com', 'My Website');
            $this->email->reply_to($email, $name);
            $this->email->subject($subject);
            $this->email->message($body);
            
            if($this->email->send()){
                $this->session->set_flashdata('contact_success', 'You message has been sent');
            }
            else{
                $this->session->set_flashdata('contact_fail', 'Could not send message');
            }

            redirect('contact');
        }
    }
}
