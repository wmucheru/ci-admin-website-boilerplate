<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('is_production')){

    /**
     *
     * Return TRUE if app is running in production
     *
     */
    function is_production(){
        $ci =& get_instance();
        return $ci->config->item('debug') == '0';
    }
}

if(!function_exists('is_localhost')){

    /**
     *
     * Return TRUE if app is running locally
     *
     */
    function is_localhost(){
        return $_SERVER['SERVER_NAME'] === 'localhost';
    }
}

if(!function_exists('generate_ref')){

    /**
     *
     * Generate unique alphanumeric ref
     *
     */
    function generate_ref($length=8){
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}

if(!function_exists('return_json')){

    /**
     *
     * Response as JSON
     *
     */
    function return_json($data){
        $ci =& get_instance();
        $ci->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}

if(!function_exists('flash_messages')){

    /**
     *
     * Render flashdata messages
     * 
     * @param flashdataKey: Session key for flash data
     *
     */
    function flash_messages($flashdataKey){
        $ci =& get_instance();
        $success = $ci->session->flashdata($flashdataKey . '_success');
        $fail = $ci->session->flashdata($flashdataKey . '_fail');
        $status = $ci->session->flashdata($flashdataKey . '_status');

        if($success != ''){
            echo '<div class="alert alert-success">'.
                '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.
                    $success .
                '</div>';
        }

        if($fail != ''){
            echo '<div class="alert alert-danger">'.
                '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.
                    $fail .
                '</div>';
        }

        if($status != ''){
            echo '<div class="alert alert-info">'.
                '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.
                    $status .
                '</div>';
        }
    }
}

if(!function_exists('ci_post')){

    /**
     *
     * Get post object value. Default to empty
     * 
     * @param key: Key of post object
     *
     */
    function ci_post($key=''){
        $ci =& get_instance();
        return !empty($key) ? $ci->input->post($key) : $ci->input->post();
    }
}

if(!function_exists('pad_num')){

    /**
     *
     * Add leading zeros to number
     *
     */
    function pad_num($number){
        return str_pad($number, 4, 0, STR_PAD_LEFT);
    }
}

if(!function_exists('date_str')){

    /**
     *
     * Date formatting
     *
     */
    function date_str($date, $format='mini'){
        if($format == 'mini'){
            $format = 'Y-m-d';
        }
        elseif($format == 'long'){
            $format = 'Y-m-d H:i:s';
        }
        else{
            $format = 'M jS, Y';
        }

        return date($format, strtotime($date));
    }
}