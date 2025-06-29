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

/**
 * 
 * Check if a user is a member of specified group
 * 
*/
if(!function_exists('is_member')){
    function is_member($groupParam){
        $CI =& get_instance();
        return $CI->auth_model->is_member($groupParam);
    }
}

if(!function_exists('is_admin')){
    function is_admin(){
        $CI =& get_instance();
        return $CI->auth_model->is_member(USER_GROUP_ADMIN);
    }
}

if(!function_exists('is_manager')){
    function is_manager(){
        $CI =& get_instance();
        return $CI->auth_model->is_member(USER_GROUP_MANAGER);
    }
}

if(!function_exists('is_editor')){
    function is_editor(){
        $CI =& get_instance();
        return $CI->auth_model->is_member(USER_GROUP_EDITOR);
    }
}

if(!function_exists('is_default_user')){
    function is_default_user(){
        $CI =& get_instance();
        return $CI->auth_model->is_member(USER_GROUP_DEFAULT_USER);
    }
}

?>