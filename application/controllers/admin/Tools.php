<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tools extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->auth_model->control(PERM_IS_ADMIN);
    }

    /**
     * 
     * Settings
     * 
    */
    function settings($id=''){
        $s = $this->site_model->getSettings($id);

        if($id != ''){
            $data['setting'] = $s;
            render_admin('admin/tools/settings/settings-form', "Setting: $s->setting", 'tools-bd', $data);
        }
        else{
            $data['settings'] = $s;
            render_admin('admin/tools/settings/settings', 'Settings', 'tools-bd', $data);
        }
    }

    function newSetting(){
        render_admin('admin/tools/settings/settings-form', 'New Setting', 'tools-bd');
    }

    function saveSetting(){
        $post = $this->input->post();
        $id = $this->input->post('id');

        if(empty($id)){
            $this->db->insert('sys_settings', $post);
            $this->session->set_flashdata('setting_success', 'Setting added');
        }
        else{
            $this->db->update('sys_settings', $post, array('id'=>$id));
            $this->session->set_flashdata('setting_success', 'Setting updated');
        }

        redirect('admin/tools/settings');
    }
}