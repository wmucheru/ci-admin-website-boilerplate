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

if(!function_exists('get_country_list')){

    /**
     *
     * Get list of countries: {'id': 1, 'name': 'Aruba'}
     *
     */
    function get_country_list(){
        $CI =& get_instance();
        return $CI->db
            ->get('sys_countries')
            ->result();
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
            echo '<div class="alert alert-success alert-dismissible">'.
                    $success .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.
                '</div>';
        }

        if($fail != ''){
            echo '<div class="alert alert-danger alert-dismissible">'.
                    $fail .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.
                '</div>';
        }

        if($status != ''){
            echo '<div class="alert alert-info alert-dismissible">'.
                    $status .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'.
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

if (!function_exists('get_api_vars')){

    /**
     * 
     * Check if value isset, add null value if not
     * 
    */
    function get_api_vars($field=''){
        if($field != ''){
            return isset($_REQUEST[$field]) ? $_REQUEST[$field] : '';
        }
        else{
            return $_REQUEST;
        }
        /*
        parse_str($_SERVER['QUERY_STRING'], $query);

        return !empty($query[$field]) ? $query[$field] : '';
        */
    }
}

if (!function_exists('get_json_post')){

    /**
     * 
     * Get JSON POST object
     * 
    */
    function get_json_post(){
        return (object) json_decode(file_get_contents('php://input'));
    }
}

if (!function_exists('get_json_api_vars')){

    /* Check if value isset, add null value if not */
    function get_json_api_vars($field, $defaultValue=''){
        $postVars = (object) json_decode(file_get_contents('php://input'));
        return isset($postVars->$field) ? $postVars->$field : $defaultValue;
    }
}

if(!function_exists('read_json_file')){

    /**
     *
     * Read json file in app folder
     *
     */
    function read_json_file($path){
        return json_decode(file_get_contents(base_url($path)));
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

if(!function_exists('day_diff')){

    /**
     * 
     * Get count of days between two dates
     * Ref: https://stackoverflow.com/a/3923228/3310235
     * 
     * @param date1: First date
     * @param date2: Second date
     * @param endOfDay: Take into account end of date by adding 1 day
     *
     */
    function day_diff($date1, $date2, $endOfDay=FALSE){
        $d1 = new DateTime($date1);
        $d2 = new DateTime($date2);

        if($endOfDay){
            $d2->modify("+1 days");
        }

        return  date_diff($d2, $d1)->d;
    }
}

if(!function_exists('format_phone')){

    /**
     *
     * Format phone number
     * 
     * https://skynix.co/resources/guide-to-validating-phone-numbers-in-php
     *
     */
    function format_phone($phone, $international=FALSE){

        # Validate if correct international phone format
        if($international == TRUE && !preg_match('/^|+?[1-9]\d{1,14}$/', $phone)){
            return (object) [
                'error'=>'true', 
                'message'=>'Invalid international phone format'
            ];
        }

        # Validate if correct local phone format
        else if($international == FALSE && !preg_match('/^\d{10}$/', $phone)){
            return (object) [
                'error'=>'true', 
                'message'=>'Invalid local phone format'
            ];
        }

        return (object) [
            'phone'=>str_replace('+', '', $phone)
        ];
    }
}

if(!function_exists('log_sys_activity')){

    /**
     *
     * Log system activity
     *
     */
    function log_sys_activity($obj){
        $ci =& get_instance();
        $obj['ipaddress'] = $ci->input->ip_address();
        $obj['createdon'] = now();

        $ci->db->insert('sys_logs', $obj);
    }
}
