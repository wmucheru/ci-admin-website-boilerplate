<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('USER_GROUP_ADMIN', 1);
define('USER_GROUP_PUBLIC', 2);
define('USER_GROUP_DEFAULT', 3);
define('USER_GROUP_CUSTOMER', 4);

class Users_model extends CI_Model{

    /**
    * 
    * Get list of users
    * 
    * @param id: User ID
    * @param groupPar: Group ID or name
    * 
    */
    function getUsers($id='', $groupPar=''){
        $this->db
            ->select('
                u.id, u.email, u.name, u.mobile, u.mobile_verified AS verified, 
                u.address, u.photo, u.date_created AS regdate, u.banned, 

                g.name AS group, g.id AS group_id'
            )
            ->from('aauth_users u')
            ->join('aauth_user_to_group ug', 'ug.user_id = u.id', 'left')
            ->join('aauth_groups g', 'g.id = ug.group_id', 'left');

        if($id != ''){
            $this->db->where('u.id', $id);
        }

        if($groupPar != ''){
            $this->db
                ->where('g.id', $groupPar)
                ->or_where('g.name', $groupPar);
        }

        $q = $this->db->get();

        return $id != '' ? $q->row() : $q->result();
    }

    function getUserInfo($id){
        return $this->getUsers($id);
    }

    function getCustomers($id=''){
        return $this->getUsers($id, USER_GROUP_CUSTOMER);
    }

    /**
     * 
     * Get user using provided email
     * 
    */
    function getByEmail($email){
        return $this->db
            ->select('*')
            ->where('email', $email)
            ->get('aauth_users')
            ->row();
    }

    function userExists($email){
        return $this->db
            ->get_where('aauth_users', array('email' => $email))
            ->num_rows() > 0;
    }

    /**
     * 
     * Check if old password provided matches the password in the database
     * 
    */
    function oldPasswordMatches($password, $userId){
        $hash = $this->aauth->hash_password($password, $userId);

        return $this->db
            ->select('id')
            ->get_where('aauth_users', array('id'=>$userId, 'pass'=>$hash))
            ->num_rows() > 0;
    }

    /**
     * 
     * Register new user
     * 
    */
    function registerUser($user){
        $response = array('error'=>true);

        $user = (object) $user;

        if(empty($user->name) || empty($user->email) || empty($user->password)){
            $response['message'] = 'Name, email and password are required';
        }
        else if($this->auth_model->check_email($user->email)){
            $response['message'] = 'Email is already in use by another account';
        }
        else if(empty($user->phone) && strlen($user->phone) < 10){
            $response['message'] = 'Enter a valid phone number';
        }
        elseif(empty($this->input->ip_address())){
            $response['message'] = 'Could not verify form type';
        }

        /*
        # We can allow a user to have multple accounts with the same phone number
        else if($this->users_model->phoneExists($phone)){
            $response = array(
                'error'=>true,
                'message'=>'This mobile no. has already been used'
            );
        }
        */

        else{
            $response = $this->createUser($user);
        }

        return (object) $response;
    }
    
    /**
     * 
     * Create user and assign user group
     * 
     * 
    */
    function createUser($user, $groupId=USER_GROUP_CUSTOMER){
        $response = array('error'=>true);

        $user = (object) $user;

        $name = $user->name;
        $email = $user->email;
        $phone = $this->messages_model->formatPhoneNumber($user->phone);
        $password = $user->password;

        if($userId = $this->auth_model->create_user($email, $password)){
            $this->db->update(
                'aauth_users',
                array(
                    'name'=>$name,
                    'mobile'=>$phone,
                    'banned' => '0', # Activate by default
                    'ip_address'=>$this->input->ip_address(),
                    'agent'=>$this->agent->agent_string()
                ),
                array('id'=>$userId)
            );

            # Update user group
            $this->auth_model->update_member_group($userId, $groupId);

            # Send verification SMS
            $this->sendVerificationCode($userId);
            
            # Send email to the user
            $loginLink = anchor('login', 'here');

            $emailObj = array(
                'email'=>$email,
                'name'=>$name,
                'subject'=>'Welcome to '. $this->config->item('site_name'),
                'body'=>"<p>Your account has been successfully created</p>" .
                    "<p>Your can now login $loginLink to create your first consignment</p>"
            );

            $this->messages_model->sendEmail($emailObj);

            $response = $this->auth_model->get_user_with_email($email, true);
        }
        else{
            $response['message'] = 'Could not create user account';
        }

        return (object) $response;
    }

    /**
     * 
     * Log in app user: email/password & using social media
     * 
     * 
    */
    function loginUser($email, $password=''){

        if(empty($email) || empty($password)){
            $response = array(
                'error'=>true,
                'message'=>'Enter the required login details'
            );
        }
        
        /*
        else if(!$this->auth_model->is_account_banned($email)){
            $response = array(
                'error'=>true,
                'message'=>'Your account is inactive. Contact admin'
            );
        }
        */

        elseif($this->aauth->login($email, $password)){
            $user = $this->auth_model->get_user_with_email($email, true);

            if($user->mobile_verified == '0'){
                $this->sendVerificationCode($user->id);
            }

            $response = array(
                'user'=>$user
            );
        }
        else{
            $response = array(
                'error'=>true,
                'message'=>'Could not login with the details provided'
            );
        }
        
        return $response;
    }

    /**
     * 
     * Social Login
     * 
    */
    function loginSocial($name, $email){
        $response = array();

        # Check for social login
        if(empty($name) && empty($email)){
            $response = array(
                'error'=>true,
                'message'=>'Name & email required for social login'
            );
        }

        /*
        else if(!$this->auth_model->is_account_banned($email)){
            $response = array(
                'error'=>true,
                'message'=>'Your account is inactive. Contact admin'
            );
        }
        */

        else{
            $user = $this->auth_model->get_user_with_email($email, true);

            # Register user if they do not exist and activate them
            if(empty($user->id)){
                $userObj = array(
                    'name'=>$name,
                    'email'=>$email,
                    'phone'=>'',
                    'password'=>$this->site_model->generateRef(4)
                );

                $user = $this->users_model->createUser($userObj);
            }

            $response = array(
                'user'=>$user
            );
        }

        return (object) $response;
    }

    /**
     * 
     * Process password reset email
     * 
    */
    function sendResetEmail($email){
        $user = $this->getByEmail($email);

        # Does user exist?
        if(empty($user->id)){
            $response = array(
                'error'=>true,
                'message'=>'We could not find any account with that email'
            );
        }
        else{
            $resetCode = $this->site_model->generateRef(16);

            $this->db->update('aauth_users', 
                array('forgot_exp'=>$resetCode),
                array('id'=>$user->id)
            );

            $resetLink = anchor('reset/'. $resetCode);
            $supportEmail = $this->config->item('email');
            $supportEmailLink = mailto($supportEmail, $supportEmail);

            $emailObj = array(
                'email'=>$email,
                'name'=>$user->name,
                'subject'=>'Reset your password',
                'body'=>"<p>To reset your password please click on the link below:</p>" .
                    "<p>$resetLink</p>
                    <p>If you did not request this, please contact us on $supportEmailLink</p>"
            );

            $this->messages_model->sendEmail($emailObj);

            $response = array(
                'message'=>'Check your email for reset instructions'
            );
        }

        return (object) $response;
    }

    /**
     * 
     * Send verification code: email and phone
     * 
    */
    function sendVerificationCode($userId){
        $code = rand(100000, 999999);
        $message = "Your verification code is $code";

        $this->db->update(
            'aauth_users',
            array('verification_code'=>$code),
            array('id'=>$userId)
        );

        $user = $this->auth_model->get_user_data($userId);

        # Send via SMS
        if(!empty($user->mobile)){
            $this->messages_model->sendSMS($user->mobile, $message);
        }

        # Send via email
        $emailObj = array(
            'email'=>$user->email,
            'name'=>$user->name,
            'subject'=>'Your account verification code',
            'body'=>"<p>$message</p>"
        );

        $this->messages_model->sendEmail($emailObj);

        return (object) array(
            'success'=>true,
            'message'=>'You will receive a verification code shortly'
        );
    }

    /**
     * 
     * Verify user account used using provided code
     * 
    */
    function verifyAccount($userId, $verificationCode){
        $v = $this->db->get_where(
            'aauth_users',
            array(
                'id'=>$userId,
                'verification_code'=>$verificationCode
            )
        );

        if($v->num_rows() > 0){
            $this->db->update(
                'aauth_users', 
                array(
                    'mobile_verified'=>'1',
                    'verification_code'=>''
                ), 
                array('id'=>$userId)
            );

            $response = array(
                'success'=>true,
                'message'=>'Account verified'
            );
        }
        else{
            $response = array(
                'error'=>true,
                'message'=>'Could not verify account'
            );
        }

        return $response;
    }

    /**
     * 
     * Get users FCM registrationId for sending push notifications
     * 
     * 
    */
    function getUserRegistrationID($userId){
        $user = $this->db
            ->select('fcm_token')
            ->get_where('aauth_users', array('id'=>$userId))
            ->result();

        return !empty($user->fcm_token) ? $user->fcm_token : '';
    }
}