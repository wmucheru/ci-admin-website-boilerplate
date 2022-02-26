<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model{

    function __construct(){
        parent::__construct();
    }

    /*
     * 
     * User Functions
     *
     */
    function login_fast($userId){
        $this->aauth->login_fast($userId);
    }
    
    function is_logged_in(){
        return $this->aauth->is_loggedin();
    }

    function set_redirect_referrer(){
        $this->session->set_userdata('referrer', $this->agent->referrer());
    }

    function set_login_redirect(){
        if(!$this->is_logged_in()){
            $this->set_redirect_referrer();
            redirect('accounts/login');
        }
    }

    function is_account_verified($email){
        return $this->db
            ->select('banned')
            ->from('aauth_users')
            ->where([
                'email'=>$email, 
                'banned'=>'0'
            ])
            ->get()
            ->num_rows() > 0;
    }

    function logout(){
        return $this->aauth->logout();
    }

    function check_email($email){
        return $this->aauth->user_exist_by_email($email);
    }

    /**
     * 
     * Get user info
     * 
     * @param userId: Optional provide userId to view a specific user's data 
     * 
    */
    function get_user_data($userId=''){
        return $this->aauth->get_user($userId);
    }

    function create_user($email, $password, $fullname){
        return $this->aauth->create_user($email, $password, $fullname);
    }

    function store_user_activity($userId, $action){
        $activity = array(
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $this->input->ip_address(),
            'timestamp' => date('Y-m-d H:i:s')
        );

        return $this->site_model->addToTable($activity);
    }

    /*
     * Get the user type based on user_id; ie. is corporate, default, rider etc.
     * 
     */
    function get_user_type($user_id){
        $q = $this->db
            ->select('au.id, ag.id, ag.name')
            ->from('aauth_users au')
            ->join('aauth_user_to_group autg', 'autg.user_id = au.id', 'left')
            ->join('aauth_groups ag', 'autg.group_id = ag.id', 'left')
            ->where('au.id', $user_id)
            ->get();

        return $q->num_rows() > 0 ? $q->row()->name : null;
    }

    /**
     * 
     * Member Functions
     * 
     * @param groupParam: Name (Case-sensitive) or ID of group
     *
     */
    function is_member($groupParam){
        return $this->aauth->is_member($groupParam);
    }

    function is_admin(){
        return $this->aauth->is_member('Admin');
    }

    function get_user_groups($userId){
        return $this->aauth->get_user_groups($userId);
    }

    function get_group_name($groupId) {
        return $this->aauth->get_group_name($groupId);
    }

    function get_group_id($groupParam) {
        return $this->aauth->get_group_id($groupParam);
    }

    function list_users($groupParam = FALSE, $limit = FALSE, $offset = FALSE, $include_banneds = FALSE){
        return $this->aauth->list_users($groupParam, $limit, $offset, $include_banneds);
    }

    /**
     * 
     * List all groups in the system
     * 
     * @param adminOnlyGroups: Array of all groups that only admins can view
     * 
    */
    function list_groups($adminOnlyGroups=[]) {
        $groups = $this->aauth->list_groups();

        $this->db->select('*');

        if(!$this->is_admin()){

            foreach($adminOnlyGroups as $g){
                $this->db->where('name !=', $g);
            }
        }

        return $this->db->get('aauth_groups')->result();
    }

    function is_banned($userId){
        return $this->aauth->is_banned($userId);
    }

    /*
     * 
     * Get list of banned users
     *
     */
    function get_banned_users(){
        return $this->db
            ->select('
                id, name, email, mobile, last_activity'
            )
            ->from('aauth_users au')
            ->where('banned', '1')
            ->get()
            ->result();
    }

    function ban_user($userId){
        return $this->aauth->ban_user($userId);
    }

    function update_user($userId, $email, $pass=FALSE, $name=FALSE){
        $obj = array('email'=>$email);

        if($pass != false){
            $obj['pass'] = $this->aauth->hash_password($pass, $userId);
        }

        if($name != false){
            $obj['name'] = $name;
        }

        return $this->db->update('aauth_users', $obj, ['id'=>$userId]);
    }

    /**
     * 
     * Group Functions
     *
     * @param groupPar: Group id or name
     *
     */
    function create_group($groupName, $definition) {
        $a = $this->aauth->create_group($groupName, $definition);
    }

    function delete_group($groupPar){
        return $this->aauth->delete_group($groupPar);
    }

    function update_group($groupPar, $groupName, $definition=FALSE){
        $a = $this->aauth->update_group($groupPar, $groupName, $definition);
    }

    function add_member($userId, $groupPar){
        return $this->aauth->add_member($userId, $groupPar);
    }

    /**
     * 
     * Update member group. Remove from current group, add to new group
     * 
    */
    function update_member_group($userId, $groupId){
        $this->db
            ->delete('aauth_user_to_group')
            ->where(['userId'=>$userId, 'group_id'=>$groupId]);

        return $this->db->update(
            'aauth_user_to_group', 
            ['group_id'=>$groupId],
            ['user_id'=>$userId]
        );
    }

    /**
     * 
     * Limit pages to authorized users
     *
     * @param permParam: Control parameter
     * @param redirect: Redirect to 404 page when perm not allowed, otherwise return boolean
     *
     */
    function control($permParam, $redirect=FALSE){
        $permId = $this->aauth->get_perm_id($permParam);
        $this->aauth->update_activity();

        if(!$this->aauth->is_allowed($permId) OR !$this->aauth->is_group_allowed($permId)){
            if($redirect){
                show_404();
            }
        }
    }

    /**
     * 
     * Permissions
     *
     */
    function create_perm($name, $definition=''){
        return $this->aauth->create_perm($name, $definition);
    }

    function delete_perm($permId){
        return $this->aauth->delete_perm($permId);
    }

    function list_perms() {
        return $this->aauth->list_perms();
    }

    function is_allowed($permParam, $userId=FALSE){
        return $this->aauth->is_allowed($permParam, $userId);
    }

    function is_group_allowed($permParam, $userId=FALSE){
        return $this->aauth->is_group_allowed($permParam, $userId);
    }

    /**
     * 
     * Return a group's permissions as an ID array
     * 
    */
    function getGroupPerms($groupId){
        $perms = $this->db
            ->select('perm_id')
            ->from('aauth_perm_to_group')
            ->where('group_id', $groupId)
            ->get()
            ->result();

        $permsArr = array();

        foreach($perms as $p){
            array_push($permsArr, $p->perm_id);
        }

        return $permsArr;
    }
}