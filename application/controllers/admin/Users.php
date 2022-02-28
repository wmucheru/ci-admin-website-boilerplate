<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->auth_model->setLoginRedirect();

        $this->auth_model->control(PERM_USER_MANAGEMENT);
    }

    /**
     * 
     * Users
     * 
     * @param userId
     *
     */
    function index(){
        $data['users'] = $this->auth_model->getSystemUsers();
        render_admin('admin/users/users', 'Users', 'user-bd', $data);
    }

    function userForm($userId=''){
        $data['groups'] = $this->auth_model->getSystemGroups();

        if($userId != ''){
            $data['user'] = $this->users_model->getUserInfo($userId);
            render_admin('admin/users/user-form', 'Edit User', 'user-bd', $data);
        }
        else{
            render_admin('admin/users/user-form', 'New User', 'user-bd', $data);
        }
    }

    function saveUser(){
        # var_dump($this->input->post()); exit();

        $userId = $this->input->post('id');
        $name = $this->input->post('fname');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $groupId = $this->input->post('groupid');
        $password = $this->input->post('password');

        $this->form_validation->set_rules('fname', 'Full Name', 'required');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required');
        $this->form_validation->set_rules('groupid', 'User Group', 'required');

        if($this->form_validation->run() == FALSE){
            $this->userForm($userId);
        }
        else{

            if(empty($userId)){
                if($this->auth_model->check_email($email)){
                    $this->session->set_flashdata('users_fail', 'Email already in use');
                    $this->userForm();
                }
                else{
                    $userId = $this->auth_model->create_user($email, $password);
                    $update = array(
                        'name'=>$name,
                        'mobile'=>$mobile,

                        # Automatically activate users added by ADMIN
                        'banned'=>'0',
                        'mobile_verified'=>'1'
                    );

                    $this->db->update('aauth_users', $update, ['id'=>$userId]);

                    $this->auth_model->update_member_group($userId, $groupId);

                    $this->session->set_flashdata('users_success', 'User account created');
                    redirect('admin/users');
                }
            }
            else{
                $password = !empty($password) ? $password : FALSE;

                $this->auth_model->update_user($userId, $email, $password, $name);
                $this->db->update('aauth_users', ['mobile'=>$mobile], ['id'=>$userId]);

                # Update member group
                $this->auth_model->update_member_group($userId, $groupId);

                $this->session->set_flashdata('users_success', 'User updated');
                redirect('admin/users/edit/'. $userId);
            }
        }
    }

    /*
     * 
     * Profile
     *
     */
    function profile(){
        $data['user'] = $this->auth_model->get_user_data();
        render_admin('admin/users/profile', 'My Account', 'account-bd', $data);
    }

    function updateProfile(){
        $userId = $this->auth_model->get_user_id();

        $name = $this->input->post('name');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $address = $this->input->post('address');

        $this->form_validation->set_rules('name', 'Full name', 'required');
        $this->form_validation->set_rules('email', 'Email address', 'required|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required');
        $this->form_validation->set_rules('address', 'Delivery Address', 'required');

        if($this->form_validation->run() == FALSE){
            $this->profile();
        }
        else{

            # $upload = $this->site_model->uploadDocument('ppic', '', 'avatar');

            $this->db->update('aauth_users', 
                array(
                    'name'=>$name,
                    'email'=>$email,
                    'mobile'=>$mobile,
                    'address'=>$address
                ),
                array('id'=>$userId)
            );

            $this->session->set_flashdata('profile_success', 'Profile updated');

            redirect('users/profile');
        }
    }

    function updatePassword(){
        $user = $this->auth_model->get_user_data();
        $userId = $user->id;

        $oldPassword = $this->input->post('opassword');
        $password = $this->input->post('password');

        $this->form_validation->set_rules('opassword', 'Old Password', 'required');
        $this->form_validation->set_rules('password', 'New Password', 'required');

        if($this->form_validation->run() == FALSE){
            $this->profile();
        }

        # Confirm old password is correct
        elseif(!$this->users_model->oldPasswordMatches($oldPassword, $userId)){
            $this->session->set_flashdata('password_fail', 'Old password is incorrect');
            $this->profile();
        }

        else{
            $userObj = array(
                'pass'=>$this->aauth->hash_password($password, $userId)
            );

            $this->db->update('aauth_users', $userObj, array('id'=>$userId));

            $this->session->set_flashdata('password_success', 'Password updated');

            redirect('users/profile');
        }
    }

    /**
     * 
     * Permissions
     * 
     * @param method: Create or delete. Default show all roles
     * @param groupId: groupId
     * 
    */
    function permissions($method='', $groupId=''){
        $data['groups'] = $this->auth_model->list_groups();
        $data['perms'] = $this->auth_model->list_perms();

        if($method == 'group' && $groupId != ''){
            $data['group'] = $this->auth_model->getGroup($groupId);
            $data['groupPerms'] = $this->auth_model->getGroupPerms($groupId);

            render_admin('admin/users/groups/group-permissions', 'Group Permissions', 'user-bd', $data);
        }
        else{
            render_admin('admin/users/groups/permissions', 'Group Permissions', 'user-bd', $data);
        }
    }

    function saveGroup(){
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $definition = $this->input->post('definition');

        $editMode = !empty($id);

        $this->form_validation->set_rules('name', 'Group Name', 'trim|required');
        $this->form_validation->set_rules('definition', 'Definition', 'trim|required');

        if($this->form_validation->run() == FALSE){
            $this->permissions();
        }
        else{

            if($editMode){

                if($this->auth_model->update_group($id, $name, $definition)){
                    $this->session->set_flashdata('group_success', 'Group updated');
                }
                else{
                    $this->session->set_flashdata('group_fail', 'Could not update group');
                }
            }
            else{
                if($this->auth_model->create_group($name, $definition) !== FALSE){
                    $this->session->set_flashdata('group_success', 'Group created');
                }
                else{
                    $this->session->set_flashdata('group_fail', 'Could not create group');
                }
            }

            redirect('admin/permissions');
        }
    }

    function deleteGroup($groupId){
        if($this->auth_model->delete_group($groupId)){
            $this->session->set_flashdata('group_success', 'Group deleted');
        }
        else{
            $this->session->set_flashdata('group_fail', 'Could not delete group');
        }

        redirect('admin/users/permissions');
    }

    /**
     * 
     * Add permissions to a specific group
     * 
    */
    function setPerms(){
        $permId = $this->input->post('pid');
        $groupId = $this->input->post('gid');
        $active = $this->input->post('active') == 'true';

        if(empty($permId) && empty($groupId)){
            $response = [
                'error'=>true,
                'message'=>'Could not update permission'
            ];
        }
        else{

            if($active){
                $this->aauth->allow_group($groupId, $permId);
            }
            else{
                $this->aauth->deny_group($groupId, $permId);
            }

            $response = ['message'=>'Permission saved'];
        }

        $this->site_model->returnJSON($response);
    }

    function suspended(){
        $data['suspended'] = $this->auth_model->get_banned_users();
        render_admin('admin/users/suspended-users', 'Suspended Accounts', 'user-bd', $data);
    }

    function suspend_user($userId){
        if($this->aauth->ban_user($userId)){
            $this->session->set_flashdata('users_success', 'User has been suspended');
        }
        else{
            $this->session->set_flashdata('users_fail', 'Could not suspend user');
        }

        redirect('admin/users');
    }

    function revoke_suspension($userId){
        if($this->aauth->unban_user($userId)){
            $this->session->set_flashdata('users_fail', 'Suspension was successfully revoked');
        }
        else{
            $this->session->set_flashdata('users_fail', 'Suspension could not be revoked');
        }

        redirect('admin/users/suspended/' . $userId);
    }
}
