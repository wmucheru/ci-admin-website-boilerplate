<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
     * @param params: Email object containing details: 
     * - email
     * - name
     * - subject
     * - message
     * 
    */
    function sendEmail($params){
        /*
        $url = '';

        $body = isset($params['body']) ? $this->_emailTemplate($params['body']) : '';
        $params['body'] = $body;

        return $this->site_model->makeCURLRequest('POST', $url, $params);
        */

        $siteEmail = 'info@invoicer.co.ke';

        $mail = new PHPMailer(true);

        try {

            # Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.invoicer.co.ke';
            $mail->SMTPAuth   = true;
            $mail->Username   = $siteEmail;
            $mail->Password   = 'JFV4Q2_73A';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            # Recipients
            $mail->setFrom($siteEmail, 'Invoicer');
            $mail->addReplyTo($siteEmail, 'Invoicer');
            $mail->addAddress('willyk99@gmail.com');
            # $mail->addAddress('ellen@example.com');
            # $mail->addCC('cc@example.com');

            # Attachments
            # $mail->addAttachment('/var/tmp/file.tar.gz');
            # $mail->addAttachment('/tmp/image.jpg', 'new.jpg');

            # Content
            $mail->isHTML(true);
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function _emailTemplate($message){
        $siteName = $this->config->item('site_name');
        $siteLogo = $this->config->item('site_logo');

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
                            <br/>
                            <br/>
                            <i>'. $siteName .'</i>
                        </td>
                    </tr>
                    </table>
                    <table width="600" cellpadding="0" cellspacing="0">
                    <tr style=" margin: 0;">
                        <td style="font-size: 12px; color: #999; padding:20px;" align="center" valign="top">
                            Questions? Email <a href="mailto:info@hamisha.me" style="color: #999; text-decoration: underline; margin: 0;">info@hamisha.me</a>
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

	/*
     * 
     * Send SMS from MTech
     * 
     *
     **/
    function sendSMS($recipients, $message){
        $recipients = $this->formatPhoneNumber($recipients);

        $url = 'https://smsapi.mtechcomm.co.ke/';
        $params = array(
            'user'=>$this->username,
            'pass'=>$this->apikey,
            'shortCode'=>$this->shortCode,
            'msisdn'=>$recipients,

            # Include new lines to space out our message
            'message'=>"$message \n\n"
        );

        return $this->site_model->makeCURLRequest('GET_STRING', $url, $params);
    }

    /*
     *
     * https://firebase.google.com/docs/cloud-messaging/http-server-ref
     * https://gist.github.com/MohammadaliMirhamed/7384b741a5c979eb13633dc6ea1269ce
     * https://stackoverflow.com/a/37560904/3310235
     *
     * @var recipients - Array of individual user registration tokens
     */
    function sendPushNotifications($to, $title, $message, $data=array()){

        $fields = array(
            'notification'=>array(
                'body'=>$message,
                'title'=>$title,
                'color'=>'#ffffff'
            ),
            'data'=>$data
        );

        if(is_array($to)){
            # To multiple users: array of registration IDs
            $fields['registration_ids'] = json_encode($to);
        }
        else{
            # To single ID
            # To topic
            $fields['to'] = $to;
        }

        $headers = array(
            'Authorization: key=' . $this->fcmKey,
            'Content-Type: application/json'
        );

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