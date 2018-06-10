<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    /*
     * USER FUNCTIONS
     *
     */
    function login_fast(){
        $this->aauth->login_fast(1);
    }
    
    function is_logged_in(){
        return $this->aauth->is_loggedin();
    }

    function set_login_redirect(){
        # Save referrer to redirect back to
        # $this->session->set_userdata('rdr', $this->agent->referrer());

        !$this->is_logged_in() ? redirect('accounts/login') : null;
    }

    function is_account_verified($email){

        $this->db->select('banned');
        $this->db->from('aauth_users');
        $this->db->where(array('email' => $email, 'banned' => '0'));

        $is_verified = $this->db->get();

        return $is_verified->num_rows() > 0;
    }

    function logout(){
        return $this->aauth->logout();
    }

    function check_email($email){
        return $this->aauth->user_exist_by_email($email);
    }

    # Get info about logged_in user
    function get_user_data(){
        return $this->aauth->get_user();
    }

    # Get info about user with specified user ID
    function get_user_info($user_id){
        $this->db->select('*');
        $this->db->where('id', $user_id);
        $this->db->limit(1);

        $user_info = $this->db->get('aauth_users');

        return $user_info->result()[0];
    }

    function create_user($email, $password, $fullname){
        return $this->aauth->create_user($email, $password, $fullname);
    }

    function store_user_activity($user_id, $action){
        $activity = array(
            'user_id' => $user_id,
            'action' => $action,
            'ip_address' => $this->input->ip_address(),
            'timestamp' => date('Y-m-d H:i:s')
        );

        return $this->index_model->addToTable($activity);
    }
    
    function settings() {
        
        //echo $this->aauth->_get_login_attempts(4);
        //echo $this->aauth->get_user_id('emre@emreakay.com');
        //$this->aauth->_increase_login_attempts('emre@emreakay.com');
        //$this->aauth->_reset_login_attempts(1);
    }

    /* Get error messages from Aauth */
    function get_aauth_errors($divider = '<br/>'){

        $errors_array = $this->aauth->get_errors_array();

        $msg = '';
        $msg_num = count($errors_array);
        $i = 1;

        foreach($errors_array as $e){
            $msg .= $e;

            if ($i != $msg_num){
                $msg .= $divider;
            }
            $i++;
        }
        return $msg;
    }

    /*
     * USER VARIABLES
     *
     */
    function set_user_var($user_par, $variable){
        return $this->aauth->set_user_var($user_par, $variable);
    }

    function unset_user_var($user_par){
        return $this->aauth->unset_user_var($user_par);
    }

    function get_user_var($user_par){
        return $this->aauth->get_user_var($user_par);
    }

    /*
     * Get the user type based on user_id; ie. is corporate, default, rider etc.
     * 
     */
    function get_user_type($user_id){
        
        $this->db->select('au.id, ag.id, ag.name');
        $this->db->from('aauth_users au');
        $this->db->join('aauth_user_to_group autg', 'autg.user_id = au.id', 'left');
        $this->db->join('aauth_groups ag', 'autg.group_id = ag.id', 'left');
        $this->db->where('au.id', $user_id);

        return $this->db->get()->result()[0]->name;
    }

    function set_system_var($user_par, $variable){
        return $this->aauth->set_system_var($user_par, $variable);
    }

    function unset_system_var($user_par){
        return $this->aauth->unset_system_var($user_par);
    }

    function get_system_var($user_par){
        return $this->aauth->get_system_var($user_par);
    }

    /*
     * MEMBER FUNCTIONS
     *
     */
    function is_member($group_param){
        return $this->aauth->is_member($group_param);
    }

    function is_admin(){
        return $this->aauth->is_member('Admin');
    }

    function get_user_groups($user_id){
        return $this->aauth->get_user_groups($user_id);
    }

    function get_group_name($group_id) {

        return $this->aauth->get_group_name($group_id);
    }

    function get_group_id($group_name) {

        return $this->aauth->get_group_id($group_name);
    }

    function list_users($group_par = FALSE, $limit = FALSE, $offset = FALSE, $include_banneds = FALSE){
        return $this->aauth->list_users($group_par, $limit, $offset, $include_banneds);
    }

    function list_groups() {
        $groups = $this->aauth->list_groups();

        $this->db->select('*');

        # Corporates are only created from Register form
        $this->db->where('name !=', 'Corporate');
        $this->db->where('name !=', 'Public');

        # Only Admin can create other admins and riders
        if(!$this->is_admin()){
            $this->db->where('name !=', 'Admin');
            $this->db->where('name !=', 'Accountant');
            $this->db->where('name !=', 'Rider');
            $this->db->where('name !=', 'Supervisor');
            $this->db->where('name !=', 'System');
        }

        if($this->auth_model->is_member('Corporate')){
            $this->db->where('name', 'Default');
        }

        return $this->db->get('aauth_groups')->result();
    }

    function is_banned($user_id){
        return $this->aauth->is_banned($user_id);
    }

    /*
     * Get overall banned users. Only ADMINs and Corporates can view;
     * for Corporates though, show only their members
     *
     */
    function get_banned_users(){

        # For ADMIN user, show all BANNED users
        $this->db->select('au.id, au.name, au.email, au.mobile, au.last_activity');

        if($this->is_member('Corporate')){

            $corporate_user_id = $this->get_user_data()->id;

            if($corporate_user_id != FALSE && $corporate_user_id != ''){

                # For Corporates, show banned members within that organization
                $this->db->from('corporate_members cmem');

                $this->db->join('aauth_users au', 'au.id = cmem.member_id', 'left');
                $this->db->join('corporate_users cu', 'cu.aauth_user_id = au.id', 'left');
                $this->db->where('cmem.corp_user_id', $corporate_user_id);
            }
        }
        else{
            $this->db->from('aauth_users au');
        }

        $this->db->where('banned', 1);

        $banned = $this->db->get();

        if($banned->num_rows() > 0){
            return $banned->result();
        }
        else{
            return false;
        }
    }

    function ban_user($user_id){
        return $this->aauth->ban_user($user_id);
    }

    function delete_user(){
        $a = $this->aauth->delete_user(7);

        print_r($a);
    }

    function unban_user(){
        $a = $this->aauth->unban_user(6);

        print_r($a);
    }

    function update_user($user_id, $email, $pass = FALSE, $name = FALSE){
        
        # return $this->aauth->update_user($user_id, $email, $pass, $name);

        $edit_array = array('email' => $email);

        if($pass != false){
            $edit_array['pass'] = $this->aauth->hash_password($pass, $user_id);
        }

        if($name != false){
            $edit_array['name'] = $name;
        }

        return $this->index_model->updateTable('aauth_users', $edit_array, array('id' => $user_id));
    }

    function update_activity() {
        $a = $this->aauth->update_activity();

        print_r($a);
    }

    function update_login_attempt() {
        $a = $this->aauth->update_login_attempts("a@a.com");

        print_r($a);
    }


    /*
     * GROUPS
     *
     * @group_par: Group id or name
     *
     */
    function create_group($group_name, $definition) {
        $a = $this->aauth->create_group($group_name, $definition);
    }

    function delete_group($group_par){
        return $this->aauth->delete_group($group_par);
    }

    function update_group($group_par, $group_name, $definition=false){
        $a = $this->aauth->update_group($group_par, $group_name, $definition);
    }

    function add_member($user_id, $group_par){
        return $this->aauth->add_member($user_id, $group_par);
    }

    # Remove member from current group, add to new group
    function update_member_group($user_id, $group_par){
        return $this->index_model->updateTable(
            'aauth_user_to_group', 
            array('group_id' => $group_par),
            array('user_id' => $user_id));
    }

    function fire_member(){
        $a = $this->aauth->fire_member(8, "deneme");
    }

    /*
     * Control sections that can be seen depending on permissions
     *
     * @perm_par: Control parameter
     * @redirect: Redirect to 404 page when perm not allowed, otherwise return boolean
     *
     */
    function control($perm_par, $redirect=false){
        $perm_id = $this->aauth->get_perm_id($perm_par);
        $this->aauth->update_activity();

        # if user or user's group not allowed
        if (! $this->aauth->is_allowed($perm_id) OR ! $this->aauth->is_group_allowed($perm_id) ){
            
            if($redirect === true){
                redirect('page-not-found');
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }
    }


    /*
     * PERMISSIONS
     *
     */
    function create_perm($name, $definition=''){
        return $this->aauth->create_perm($name, $definition);
    }


    function update_perm(){
        $a = $this->aauth->update_perm("deneme","deneme","xxx");
    }

    function delete_perm($perm_id){
        return $this->aauth->delete_perm($perm_id);
    }

    function allow_user() {

        $a = $this->aauth->allow_user(9,"deneme");
    }

    function deny_user() {

        $a = $this->aauth->deny_user(9,"deneme");
    }

    function allow_group() {
        $a = $this->aauth->allow_group("deneme","deneme");
    }

    function deny_group() {
        $a = $this->aauth->deny_group("deneme","deneme");
    }

    function list_perms() {
        return $this->aauth->list_perms();
    }

    function is_allowed($perm_par, $user_id=FALSE){
        return $this->aauth->is_allowed($perm_par, $user_id=FALSE);
    }

    /*
     *  Return an array of permission id
     */
    function get_group_perms($group_id){
        $this->db->select('perm_id');
        $this->db->from('aauth_perm_to_group');
        $this->db->where('group_id', $group_id);

        $perms_array = array();

        foreach($this->db->get()->result() as $perm){
            array_push($perms_array, $perm->perm_id);
        }

        return $perms_array;
    }    

    function get_perm_id() {

        $a = $this->aauth->get_perm_id("deneme");
        print_r($a);
    }


    /*
     * PRIVATE MESSAGING
     *
     */
    function send_pm() {

        $a = $this->aauth->send_pm(1,8,'s',"w");
        $this->aauth->print_errors();
    }

    function list_pms(){

        print_r( $this->aauth->list_pms() );
    }

    function get_pm(){

        print_r( $this->aauth->get_pm(39,false));
    }

    function delete_pm(){

        $this->aauth->delete_pm(41);
    }

    function count_unread_pms(){

        echo $this->aauth->count_unread_pms(8);
    }


    /*
     * ERRORS
     *
     */
    function error(){

        $this->aauth->error("asd");
        $this->aauth->error("xasd");
        $this->aauth->keep_errors();
        $this->aauth->print_errors();

    }

    function keep_errors(){

        $this->aauth->print_errors();
        //$this->aauth->keep_errors();
    }
}