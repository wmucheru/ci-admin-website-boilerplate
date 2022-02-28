<?php

/**
 * 
 * Get logged-in user data
 * 
 * @param property: If specified, return value of property ()
 * 
*/
if(!function_exists('user_data')){

    function user_data($property=''){
        $CI =& get_instance();
        $user = $CI->auth_model->get_user_data();

        if($property != ''){
            return !empty($user->$property) ? $user->$property : '';
        }

        return $user;
    }
}

/**
 * 
 * Get logged-in user ID
 * 
*/
if(!function_exists('user_id')){

    function user_id(){
        return user_data('id');
    }
}

?>