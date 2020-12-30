<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * SETTINGS HELPER
 *
 * Custom settings for the system
 *  true / false
 *
 *
*/

function get_all_settings(){
    $ci =& get_instance();
    $ci->load->database();

    $sql = "SELECT * FROM sys_settings";

    $q = $ci->db->query($sql);

    return $q->result();
}

function get_setting($settingId){
    $ci =& get_instance();
    $ci->load->database();
    $q = $ci->db->query("SELECT value FROM sys_settings WHERE id=$settingId");

    return $q->num_rows() > 0 ? trim($q->row()->value) : '';
}

# SMS settings
define('SETTING_SMS_USERNAME', get_setting(1));
define('SETTING_SMS_API_KEY', get_setting(2));
define('SETTING_SMS_SHORTCODE', get_setting(3));
