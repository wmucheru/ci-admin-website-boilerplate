<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('SMS_UNIT_COST', 1);

class Messages_model extends CI_Model{
    var $username;
    var $apikey;
    var $shortCode;
    var $fcmKey;

	function __construct(){
        parent::__construct();

        $this->username = SETTING_SMS_USERNAME;
        $this->apikey = SETTING_SMS_API_KEY;
        $this->shortCode = SETTING_SMS_SHORTCODE;
        $this->fcmKey = "";
    }

    /**
     * 
     * Send emails using the service api
     *
     * @param emailObj: Email object containing details: 
     * - email
     * - name
     * - subject
     * - message
     * 
    */
    function sendEmail($emailObj){
        $this->load->library('email');

        $obj = (object) $emailObj;

        $email = !empty($obj->email) ? $obj->email : '';
        $subject = !empty($obj->subject) ? $obj->subject : '';
        $message = !empty($obj->message) ? $obj->message : '';

        # Contact
        $fromName = !empty($obj->fromName) ? $obj->fromName : $this->config->item('site_name');
        $fromEmail = !empty($obj->fromEmail) ? $obj->fromEmail : $this->config->item('email');
        $replyToName = !empty($obj->replyToName) ? $obj->replyToName : $fromName;
        $replyToEmail = !empty($obj->replyToEmail) ? $obj->replyToEmail : $fromEmail;

        # Meta
        $protocol = isset($obj->protocol) ? $obj->protocol : 'mail';

        # Is admin-bound email?
        $isAdmin = !empty($obj->isAdmin) ? $obj->isAdmin : FALSE;

        # SMTP vars
        $SMTPHost = isset($obj->smtp_host) ? $obj->smtp_host : SETTING_EMAIL_SMTP_HOST;
        $SMTPUser = isset($obj->smtp_user) ? $obj->smtp_user : SETTING_EMAIL_SMTP_USER;
        $SMTPPassword = isset($obj->smtp_pass) ? $obj->smtp_pass : SETTING_EMAIL_SMTP_PWD;
        $SMTPPort = isset($obj->smtp_port) ? $obj->smtp_port : SETTING_EMAIL_SMTP_PORT;
        $SMTPCrypto = isset($obj->smtp_crypto) ? $obj->smtp_crypto : SETTING_EMAIL_SMTP_CRYPTO;

        $body = $this->_emailTemplate($message);

        if(!$email){
            $response['message'] = 'Specify email';
        }
        elseif(!$subject){
            $response['message'] = 'Specify subject';
        }
        elseif(!$body){
            $response['message'] = 'Specify body';
        }
        else{
            $this->load->library('email');

            $config['mailtype'] = 'html';

            if($protocol == 'smtp'){
                $config['protocol'] = $protocol; # mail, sendmail or smtp
                $config['smtp_host'] = $SMTPHost;
                $config['smtp_user'] = $SMTPUser;
                $config['smtp_pass'] = $SMTPPassword;
                $config['smtp_port'] = $SMTPPort;
                $config['smtp_crypto'] = $SMTPCrypto; # tls or ssl
            }

            $this->email->initialize($config);
            $this->email->set_newline("\r\n");

            $this->email->from($fromEmail, $fromName);
            $this->email->to($email);
            $this->email->subject($subject);
            $this->email->message($body);
            $this->email->reply_to($replyToEmail, $replyToName);

            if($this->email->send()){
                $response['message'] = 'E-mail sent to '. $email; 
            }
            else{
                $response = [
                    'error'=>true,
                    'message'=>$this->email->print_debugger()
                ]; 
            }
        }

        return (object) $response;
    }

    /**
     * 
     * Send emails using Mailgun
     * 
     * https://documentation.mailgun.com/en/latest/quickstart-sending.html#send-via-api
     * 
     * @param params: params[email, subject, body]
     * 
    */
    function sendMailgunEmail($params){
        $params = (object) $params;

        $email = isset($params->email) ? $params->email : '-';
        $subject = isset($params->subject) ? $params->subject : '-';
        $body = isset($params->body) ? $this->_emailTemplate($params->body) : '';
        $attachments = isset($params->attachments) ? $params->attachments : [];

        $url = SETTING_EMAIL_MG_URL .'/messages';
        $post = [
            'from'=>SETTING_EMAIL_SENDER_NAME. ' <'. SETTING_EMAIL_SENDER_EMAIL .'>',
            'to'=>$email,
            'subject'=>$subject,
            'html'=>$body,

            # Tracking
            'o:tracking'=>true
        ];

        $curl = curl_init();

        if(!empty($attachments)){
            foreach($attachments as $index => $a){
                $index = $index + 1;
                $post["attachment[$index]"] = $a;
            }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_USERPWD, SETTING_EMAIL_MG_API_KEY);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    function _emailTemplate($message){
        $siteName = $this->config->item('site_name');
        $siteLogo = $this->config->item('site_logo');
        $siteEmail = $this->config->item('email');

        return '
            <table style="font-family:Arial;font-size:14px;width:100%;" bgcolor="#f6f6f6">
            <tr>
                <td valign="top" align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="border-radius: 3px;border: 1px solid #e9e9e9;" bgcolor="#fff">
                    <tr>
                        <td style="padding: 20px;" align="center" valign="top" bgcolor="#fff">
                            <img src="'. base_url('assets/img/'. $siteLogo) .'" alt="'. $siteName .'" style="width:180px"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="margin: 0; padding: 20px; line-height:26px;" align="left" valign="top">
                            ' . $message . '
                            <br/><br/>
                            <i>'. $siteName .'</i>
                        </td>
                    </tr>
                    </table>
                    <table width="600" cellpadding="0" cellspacing="0">
                    <tr style=" margin: 0;">
                        <td style="font-size: 12px; color: #999; padding:20px;" align="center" valign="top">
                            Questions? Email 
                            <a href="mailto:'. $siteEmail .'" style="color: #999; text-decoration: underline; margin: 0;">'. $siteEmail .'</a>
                        </td>
                    </tr>
                    </table>
                </td>
            </tr>
            </table>';
    }

    /**
     * 
     * Format phone numbers into international format
     * 
     * 
    */
    function formatPhoneNumber($phone, $locale='KE'){
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        try {
            $phone = $phoneUtil->parse($phone, $locale);
            $phoneNumber = $phoneUtil->format($phone, \libphonenumber\PhoneNumberFormat::E164);

            return str_replace('+', '', $phoneNumber);
        }
        catch (\libphonenumber\NumberParseException $e) {
            return $phone;
        }
    }

    /**
     *
     * https://developers.africastalking.com/docs/sms/sending/premium
     *
     * @var recipients: An array of recipients
     * @var message: Message to be sent
     * receipients are the number sent for messages
     */
    function sendSMS($recipients, $message, $sendFromLocal=FALSE){
        $response = [];
        $recipients = is_array($recipients) ? join(',', $recipients) : $recipients;
        $post = [
            "from"=>$this->shortCode,
            "username"=>$this->username,
            "apikey"=>$this->apikey,
            "to"=>$recipients,
            "message"=>$message,
            # "enqueue"=>1
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.africastalking.com/version1/messaging',
            CURLOPT_HTTPHEADER => [
                "apikey:$this->apikey",
                'Accept:application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_POST=>1,
            CURLOPT_POSTFIELDS=>http_build_query($post)
        ]);

        if(is_localhost() && !$sendFromLocal){
            $response = [
                'message'=>'No sending from local'
            ];
        }
        else{
            $result = curl_exec($curl);
            curl_close($curl);

            $response = json_decode($result);
        }

        return $response;
    }

    /**
     *
     * http://docs.africastalking.com/userdata/balance
     *
     * Check no. of SMS tokens remaining. Get balance and divide by unit cost
     *
    */
    function checkRemainingSMSes(){
        $unitCost = SMS_UNIT_COST;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://api.africastalking.com/version1/user?username=". $this->username,
            CURLOPT_HTTPHEADER => [
                "Apikey:$this->apikey",
                "Accept:application/json"
            ]
        ]);

        $result = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($result);
        $result = isset($response->UserData) ? $response->UserData->balance : 0;

        $balance = str_replace('KES ', '', $result);

        # return $result;
        # return $balance;
        return [
            'balance'=>floor($balance),
            'unitCost'=>$unitCost
        ];
    }

    /**
     *
     * Get SMS logs
     *
    */
    function getSMSLogs($ref=''){
        $this->db
            ->select('
                j.id, j.ref, j.message, j.response, j.createdon,

                u.name AS sender'
            )
            ->from('sms_jobs j')
            ->join('aauth_users u', 'u.id = j.createdby', 'left')
            ->order_by('j.id', 'desc');

        if($ref != ''){
            $this->db->where('j.ref', $ref);
        }

        $q = $this->db->get();

        return $ref != '' ? $q->row() : $q->result();
    }

    /**
     *
     * https://firebase.google.com/docs/cloud-messaging/http-server-ref
     * https://gist.github.com/MohammadaliMirhamed/7384b741a5c979eb13633dc6ea1269ce
     * https://stackoverflow.com/a/37560904/3310235
     *
     * @var recipients - Array of individual user registration tokens
     */
    function sendPushNotifications($to, $title, $message, $data=[]){
        $fields = [
            'notification'=>[
                'body'=>$message,
                'title'=>$title,
                'color'=>'#ffffff'
            ],
            'data'=>$data
        ];

        if(is_array($to)){
            # To multiple users: array of registration IDs
            $fields['registration_ids'] = json_encode($to);
        }
        else{
            # To single ID
            # To topic
            $fields['to'] = $to;
        }

        $headers = [
            'Authorization: key=' . $this->fcmKey,
            'Content-Type: application/json'
        ];

        # Send Reponse To FireBase Server
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        curl_close($ch);

        # Echo Result Of FireBase Server
        return json_decode($result);
    }
}