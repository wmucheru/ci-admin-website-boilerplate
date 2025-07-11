<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

# System User
define('SYSTEM_USER_ID', 1);

# User Groups
define('USER_GROUP_ADMIN', 1);
define('USER_GROUP_MANAGER', 2);
define('USER_GROUP_EDITOR', 3);
define('USER_GROUP_DEFAULT_USER', 4);

class Users_model extends CI_Model{

    /**
     * 
     * USERS
     * 
    */

    /**
     * 
     * Get users
     * 
    */
    function getUsers($filter=[]){
        $filter = (object) $filter;

        $id = !empty($filter->id) ? $filter->id : '';
        $groupId = !empty($filter->groupId) ? $filter->groupId : '';
        $email = !empty($filter->email) ? $filter->email : '';

        $this->db
            ->select('
                u.*,

                utg.*, 

                g.name AS group'
            )
            ->from('aauth_users u')
            ->join('aauth_user_to_group utg', 'utg.user_id = u.id', 'left')
            ->join('aauth_groups g', 'g.id = utg.group_id', 'left');

            # List only system users
            // ->where('autg.group_id !=', USER_GROUP_CUSTOMER);

        if($id != ''){
            $this->db->where('u.id', $id);
        }

        if($groupId != ''){
            $this->db->where('u.groupid', $groupId);
        }

        if($email != ''){
            $this->db->where('u.email', $email);
        }

        $q = $this->db->get();

        return $id != '' || $email != '' ? $q->row() : $q->result();
    }

    function getUserById($id){
        return $this->getUsers(['id'=>$id]);
    }

    function getUserByEmail($email){
        return $this->getUsers(['email'=>$email]);
    }



    /**
     * 
     * GROUPS
     * 
    */
    function getUserGroups(){
        return $this->db
            ->select('*')
            ->from('aauth_groups')

            # List only system users
            // ->where('id !=', USER_GROUP_CUSTOMER)

            ->get()
            ->result();
    }

    function getUserGroup($userId){
        $q = $this->db
            ->select('
                u.id,

                g.id, g.name'
            )
            ->from('aauth_users u')
            ->join('aauth_user_to_group autg', 'autg.user_id = u.id', 'left')
            ->join('aauth_groups g', 'g.id = autg.group_id', 'left')
            ->where('au.id', $userId)
            ->get();

        return $q->num_rows() > 0 ? $q->row()->name : FALSE;
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
            ->get_where('aauth_users', ['id'=>$userId, 'pass'=>$hash])
            ->num_rows() > 0;
    }

    /**
     * 
     * Register new user
     * 
    */
    function registerUser($user){
        $response = ['error'=>true];

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
            $response = [
                'error'=>true,
                'message'=>'This mobile no. has already been used'
            ];
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
    function createUser($user, $groupId=USER_GROUP_DEFAULT_USER){
        $response = ['error'=>true];

        $user = (object) $user;

        $name = $user->name;
        $email = $user->email;
        $phone = $this->messages_model->formatPhoneNumber($user->phone);
        $password = $user->password;

        if($userId = $this->auth_model->create_user($email, $password)){
            $this->db->update(
                'aauth_users',
                [
                    'name'=>$name,
                    'mobile'=>$phone,
                    'banned' => '0', # Activate by default
                    'ip_address'=>$this->input->ip_address(),
                    'agent'=>$this->agent->agent_string()
                ],
                [
                    'id'=>$userId
                ]
            );

            # Update user group
            $this->auth_model->update_member_group($userId, $groupId);

            # Send verification SMS
            $this->sendVerificationCode($userId);
            
            # Send email to the user
            $loginLink = anchor('login', 'here');

            $emailObj = [
                'email'=>$email,
                'name'=>$name,
                'subject'=>'Welcome to '. $this->config->item('site_name'),
                'body'=>"<p>Your account has been successfully created</p>" .
                    "<p>Your can now login $loginLink to create your first consignment</p>"
            ];

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
            $response = [
                'error'=>true,
                'message'=>'Enter the required login details'
            ];
        }
        
        /*
        else if(!$this->auth_model->is_account_banned($email)){
            $response = [
                'error'=>true,
                'message'=>'Your account is inactive. Contact admin'
            ];
        }
        */

        elseif($this->aauth->login($email, $password)){
            $user = $this->auth_model->get_user_with_email($email, true);

            if($user->mobile_verified == '0'){
                $this->sendVerificationCode($user->id);
            }

            $response = ['user'=>$user];
        }
        else{
            $response = [
                'error'=>true,
                'message'=>'Could not login with the details provided'
            ];
        }
        
        return $response;
    }

    /**
     * 
     * Social Login
     * 
    */
    function loginSocial($name, $email){
        $response = ['error'=>true];

        # Check for social login
        if(empty($name) && empty($email)){
            $response = [
                'message'=>'Name & email required for social login'
            ];
        }

        /*
        else if(!$this->auth_model->is_account_banned($email)){
            $response = [
                'message'=>'Your account is inactive. Contact admin'
            ];
        }
        */

        else{
            $user = $this->auth_model->get_user_with_email($email, true);

            # Register user if they do not exist and activate them
            if(empty($user->id)){
                $userObj = [
                    'name'=>$name,
                    'email'=>$email,
                    'phone'=>'',
                    'password'=>generate_ref(4)
                ];

                $user = $this->users_model->createUser($userObj);
            }

            $response = ['user'=>$user];
        }

        return (object) $response;
    }

    /**
     * 
     * Process password reset email
     * 
    */
    function sendAccountResetEmail($email){
        $user = $this->getUserByEmail($email);

        # Does user exist?
        if(empty($user->id)){
            $response = [
                'error'=>true,
                'message'=>'We could not find any account with that email'
            ];
        }
        else{
            $resetCode = generate_ref(16);

            $this->db->update('aauth_users', 
                ['forgot_exp'=>$resetCode],
                ['id'=>$user->id]
            );

            $resetLink = anchor('reset/'. $resetCode);
            $supportEmail = $this->config->item('email');
            $supportEmailLink = mailto($supportEmail, $supportEmail);

            $emailObj = [
                'email'=>$email,
                'name'=>$user->name,
                'subject'=>'Reset your password',
                'body'=>"<p>To reset your password please click on the link below:</p>" .
                    "<p>$resetLink</p>
                    <p>If you did not request this, please contact us on $supportEmailLink</p>"
            ];

            $this->messages_model->sendEmail($emailObj);

            $response = ['message'=>'Check your email for reset instructions'];
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

        $this->db->update('aauth_users', ['verification_code'=>$code], ['id'=>$userId]);

        $user = $this->auth_model->get_user_data($userId);

        # Send via SMS
        if(!empty($user->mobile)){
            $this->messages_model->sendSMS($user->mobile, $message);
        }

        # Send via email
        $emailObj = [
            'email'=>$user->email,
            'name'=>$user->name,
            'subject'=>'Your account verification code',
            'body'=>"<p>$message</p>"
        ];

        $this->messages_model->sendEmail($emailObj);

        return (object) [
            'success'=>true,
            'message'=>'You will receive a verification code shortly'
        ];
    }

    /**
     * 
     * Verify user account used using provided code
     * 
    */
    function verifyAccount($userId, $verificationCode){
        $q = $this->db->get_where(
            'aauth_users',
            [
                'id'=>$userId,
                'verification_code'=>$verificationCode
            ]
        );

        if($q->num_rows() > 0){
            $this->db->update(
                'aauth_users', 
                [
                    'mobile_verified'=>'1',
                    'verification_code'=>''
                ], 
                ['id'=>$userId]
            );

            $response = ['message'=>'Account verified'];
        }
        else{
            $response = [
                'error'=>true,
                'message'=>'Could not verify account'
            ];
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
            ->get_where('aauth_users', ['id'=>$userId])
            ->result();

        return !empty($user->fcm_token) ? $user->fcm_token : '';
    }
}