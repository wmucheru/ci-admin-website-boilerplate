<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->auth_model->set_login_redirect();
    }

    /**
     * 
     * Users
     * 
     * @param id: User ID
     *
     */
    function index($id=''){
        $data['groups'] = $this->auth_model->getSystemGroups();

        if($id != ''){
            $data['user'] = $this->users_model->getUserInfo($id);
            render_admin('admin/users/edit-user', 'Edit User', 'user-bd', $data);
        }
        else{
            $data['users'] = $this->auth_model->getSystemUsers();
            render_admin('admin/users/users', 'Users', 'user-bd', $data);
        }
    }

    function saveUser(){
        # var_dump($this->input->post()); exit();

        $userId = $this->input->post('id');
        $name = $this->input->post('fname');
        $email = $this->input->post('email');
        $mobile = $this->input->post('mobile');
        $groupId = $this->input->post('group');
        $password = $this->input->post('pwd');
        $cPassword = $this->input->post('cpwd');

        $this->form_validation->set_rules('fname', 'Full Name', 'required');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required');
        $this->form_validation->set_rules('group', 'User Group', 'required');

        if($this->form_validation->run() == FALSE){
            $this->index($userId);
        }
        else{

            if(!empty($userId)){
                $password = empty($cPassword) ? FALSE : $cPassword;

                $this->auth_model->update_user($userId, $email, $password, $name);
                $this->db->update(
                    'aauth_users', 
                    array('mobile' => $mobile), 
                    array('id' => $userId)
                );

                # Update member group
                $this->auth_model->update_member_group($userId, $groupId);

                $this->session->set_flashdata('users_success', 'User updated');
                redirect('admin/users/'. $userId);
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

                $this->db->update('aauth_users', $update, array('id'=>$userId));

                $this->auth_model->update_member_group($userId, $groupId);

                $this->session->set_flashdata('users_success', 'User account created');
                redirect('admin/users');
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

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        $data['body_class'] = 'user-bd';

        $pageTitle = 'Group Permissions';
        $pageContent = 'users/permissions/permissions';

        # Include corporates in the groups list
        $data['groups'] = $this->auth_model->list_groups();
        $data['perms'] = $this->auth_model->list_perms();

        $perm_id = $this->input->post('perm_id');
        $name = $this->input->post('permname');
        $desc = $this->input->post('permdescription');
        // $moduleId = $this->input->post('moduleid');
        $type = $this->input->post('permtype');

        if($method == 'add'){

            if($type == 'update'){
                if(isset($perm_id)){
                    $edit_role = $this->auth_model->update_perm($perm_id, $name, $desc);
                    if($edit_role == false){
                        $this->session->set_flashdata('perm_fail', 'Permission could not be updated');
                    }
                    else{
                        $this->session->set_flashdata('perm_success', 'Permission updated successfully');
                    }
                }
            }
            else{

                $add_role = $this->auth_model->create_perm($name, $desc);

                if($add_role == false){
                    $this->session->set_flashdata('perm_fail', 'Permission could not be added');
                }
                else{
                    $this->session->set_flashdata('perm_success', 'Permission added successfully');
                }
            }

            redirect('admin/users/permissions');
        }

        if($method == 'group' && $groupId != ''){
            $group_name = $this->auth_model->get_group_name($groupId);

            $pageTitle = 'Group Permissions: ' . $group_name;
            $pageContent = 'users/permissions/group-permissions';

            $data['gid'] = $groupId;
            $data['group_perms'] = $this->auth_model->getGroupPerms($groupId);
        }

        if($method == 'delete'){
            $this->auth_model->delete_perm();
            redirect('admin/users/permissions');
        }

        render_admin($pageContent, $pageTitle, 'user-bd', $data);
    }

    function editGroup($groupId){
        $groupName = $this->auth_model->get_group_name($groupId);

        $data['gid'] = $groupId;
        $data['edit_mode'] = TRUE;
        $data['group_details'] = $this->auth_model->getGroups($groupId);

        render_admin('admin/users/groups/edit-group', "Group Permissions: $groupName", 'user-bd', $data);
    }

    /*
     * Add permissions to a specific group
     */
    function set_perms(){

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        $perm = $this->input->post('perm');
        $groupId = $this->input->post('group_id');

        if(!empty($perm) && !empty($groupId)){

            $group_perms = $this->auth_model->get_group_perm($groupId,$perm);

            if(!empty($group_perms)){
                foreach($group_perms as $pms){
                    $this->aauth->deny_group($groupId, $pms);
                }
            }else{
                $this->aauth->allow_group($groupId, $perm);
            }

            $response = array(
                'status' => 'success',
                'message' => "Permission successfully updated"
            );
        }
        else{
            $response = array(
                'status' => 'fail',
                'message' => "Permission could not be updated"
            );
        }

        $this->site_model->returnJSON($response);
    }

    function delete_perm($perm_id){

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        $this->auth_model->delete_perm($perm_id);
        redirect('admin/users/permissions');
    }

    # @method: create, delete, allow
    function groups($method='', $groupId=''){

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        #var_dump($this->input->post());#exit();
        $groupId = $this->input->post('group_id');
        $group_name = $this->input->post('group_name');
        $group_definition = $this->input->post('group_definition');
        $type = $this->input->post('type');
        #$moduleid=$this->input->post('moduleid');

        $this->form_validation->set_rules('group_name', 'Group Name', 'trim|required');

        if($this->form_validation->run() == FALSE){
            $this->permissions();
        }
        else{
            if($method == 'create'){
                $add_group = '';
                $update_group = '';

                if($type == 'update'){
                    if(isset($groupId)){
                        $update_group = $this->auth_model->update_group($groupId, $group_name, $group_definition);
                        # var_dump(); exit();
                        if($update_group == false){
                            $this->session->set_flashdata('group_fail', 'Group could not be updated');
                        }
                        else{
                            $this->session->set_flashdata('group_success', 'Group updated successfully');
                        }
                    }
                }
                else{
                    $add_group=$this->auth_model->create_group($group_name, $group_definition);

                    if($add_group == false){
                        $this->session->set_flashdata('group_fail', 'Group could not be added');
                    }
                    else{
                        $this->session->set_flashdata('group_success', 'Group added successfully');
                    }
                }
            }

            if($method == 'allow'){

            }

            redirect('admin/users/groups');
        }
    }

    function delete_group($groupId){

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        $group = $this->auth_model->delete_group($groupId);

        if($group == false){
            $this->session->set_flashdata('group_fail', 'Group could not be deleted');
        }
        else{
            $this->session->set_flashdata('group_success', 'Group deleted successfully');
        }

        redirect('admin/users/groups');
    }

    function suspended(){

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        $data['suspended_users'] = $this->auth_model->get_banned_users();

        render_admin('admin/users/suspended-users', 'Suspended Accounts', 'user-bd', $data);
    }

    function suspend_user($userId){

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        if($this->aauth->ban_user($userId)){
            $this->session->set_flashdata('users_success', 'User has been suspended');
        }
        else{
            $this->session->set_flashdata('users_fail', 'Suspension could not be processed');
        }

        redirect('users');
    }

    function revoke_suspension($userId){

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        if($this->aauth->unban_user($userId)){
            $this->session->set_flashdata('users_fail', 'Suspension was successfully revoked');
        }
        else{
            $this->session->set_flashdata('users_fail', 'Suspension could not be revoked');
        }

        redirect('admin/users/suspended/' . $userId);
    }

    function activity(){

        /* Control user access: 2 is control parameter for User access in aauth_perms table */
        $this->auth_model->control(2, true);

        $data['activity'] = $this->auth_model->get_user_activity();

        render_admin('admin/users/activity', 'User Activity', 'user-bd', $data);
    }
}
