<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_model extends CI_Model{

	var $gallery_path;
	var $gallery_path_url;

	public function __construct(){
        parent::__construct();

        date_default_timezone_set('Africa/Nairobi');

        $this->form_validation->set_error_delimiters('<p class="error">', '</p>');
		
		$this->gallery_path = realpath(FCPATH . '/content/');
		$this->gallery_path_resize = realpath(FCPATH . '/content/');
		$this->gallery_path_url = base_url().'../content/';
    }

    function getSystemLogs($limit=500){
        return $this->db
            ->select('id, tag, description, ipaddress, reference, status, createdon')
            ->limit($limit)
            ->order_by('id', 'DESC')
            ->get('sys_logs')
            ->result();
    }

    function getSettings($id=''){
        $this->db
            ->select('id, setting, description, value, tag')
            ->from('sys_settings');

        if($id != ''){
            $this->db->where('id', $id);
        }

        $q = $this->db->get();

        return $id != '' ? $q->row() : $q->result();
    }

    function isProduction(){
        return $this->config->item('debug') == '0';
    }

    function writeLog($string){
        $file = realpath(FCPATH .'/logs/log.txt');
        $current = file_get_contents($file);

        file_put_contents($file, $current . $string);
    }

    function setGoogleAnalytics(){
        $gaCode = $this->config->item('ga_code');
        
        if(!$this->site_model->isLocalhost() && !empty($gaCode)){
            echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id=$gaCode\"></script>
            <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '$gaCode');
            </script>";
        }
    }

    function generateRef(){
        return bin2hex(openssl_random_pseudo_bytes(8));
    }

    function setFlashdataMessages($flashdataKey){

        $success = $this->session->flashdata($flashdataKey . '_success');
        $fail = $this->session->flashdata($flashdataKey . '_fail');
        $status = $this->session->flashdata($flashdataKey . '_status');

        if($success != ''){
            echo '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    ' . $success . '
                </div>';
        }

        if($fail != ''){
            echo '<div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    ' . $fail . '
                </div>';
        }

        if($status != ''){
            echo '<div class="alert alert-info">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    ' . $status . '
                </div>';
        }
    }

    function uploadDocument($fieldname='', $filename='', $uploadType='', $dimensions=array()){
        $response = array();

        $config = array(
            'allowed_types' => "gif|jpg|png|jpeg",
            'file_name' => $filename,
            'overwrite' => TRUE,
            'max_size' => "512000" # Max 512KB
        );

        if($uploadType == 'poster'){
            $config['upload_path'] = $this->gallery_path . '/uploads/posters/';
            $config['max_height'] = $dimensions['height'];
            $config['max_width'] = $dimensions['width'];
        }

        if($uploadType == 'adverts'){
            $config['upload_path'] = $this->gallery_path . '/uploads/adverts/';
            $config['max_height'] = $dimensions['height'];
            $config['max_width'] = $dimensions['width'];
        }

        if($uploadType == 'gallery'){
            $config['upload_path'] = $this->gallery_path . '/uploads/gallery/';
        }

        $this->upload->initialize($config);

        if($this->upload->do_upload($fieldname)){
            $response['upload_data'] = $this->upload->data();
        }
        else{
            $response['error'] = $this->upload->display_errors();
        }

        return $response;
    }

    function resizeImage($filename, $path){
        $source_path = FCPATH.$path. $filename;
        $target_path = FCPATH.$path;
        $config_manip = array(
            'source_image' => $source_path,
            'new_image' => $target_path,
            'maintain_ratio' => TRUE,
            'thumb_marker' => '_thumb',
            'width' => 480,
            'height' => 480
        );
        
        $config_manip['image_library'] = 'gd2';
        $this->image_lib->initialize($config_manip);
        
        if (!$this->image_lib->resize()) {
            echo $this->image_lib->display_errors();
            exit();
        }
        $this->image_lib->clear();
    }
    
    /* Check if on local or live server */
    function isLocalhost(){
        $domain = $_SERVER['SERVER_NAME'];
        return ($domain == 'localhost') ? true : false;
    }
    
    # JSON responses
    function returnJSON($data){
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /**
     *
     * CURL REQUESTS
     * http://hayageek.com/php-curl-post-get/
     */
    function makeCURLRequest($method, $url, $array_params='', $headers=FALSE){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $array_params = (empty($array_params)) ? array() : $array_params;
        $post_fields = json_encode($array_params);
        
        switch($method){
            
            case "POST":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
                curl_setopt($curl, CURLOPT_HTTPHEADER, 
                    array(
                        'Content-Type:text/plain',
                        'Content-Length:' . strlen($post_fields)
                    )
                );
                
                break;
                
            case "POST_JSON":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode((object) $array_params));
                curl_setopt($curl, CURLOPT_HTTPHEADER, 
                    array('Content-Type:application/json')
                );
                
                break;
            
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_field_string);
                
                break;
            
            default: # GET
                $url = $url . '?' . http_build_query($array_params, '', '&');
                curl_setopt($curl, CURLOPT_URL, $url);
            
                break;
        }
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        # curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        if($headers !== FALSE){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        return $method == 'GET_STRING' ? $response : json_decode($response);
    }

    function makeSOAPRequest($url, $soap_body){
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:text/xml'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $soap_body);
        
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
    /**
     * AUTH FUNCTIONS: Check if email or username
     * 
     * */
    #new login function mikey
    function validate_user($email, $password, $check = '') {
        
        $this->db->from('auth_admin');
        $this->db->where('admin_username',$email );
        $this->db->where( 'admin_key', sha1($password) );
        
        #add to check status
        $login = $this->db->get()->result();
        
        if ( is_array($login) && count($login) == 1 ) {
            $this->details = $login[0];
            $this->set_session($check);
            return true;
        }

        return false;
    }

    function set_session($check=''){
        
        $this->session->set_userdata(
            array(
                'id'=>$this->details->admin_id,
                'levelid'=> $this->details->admin_username,
                'admin_level'=> $this->details->admin_type,
                'username'=>$this->details->admin_email,
                'isLoggedIn'=>true
            )
        );
    }
	
	/*
     * BASIC CRUD
     * 
     * */
    function addToTable($table, $array, $dataname=''){
        $query = $this->db->insert($table,$array);
        
        if($query){
            # Saved successfully?
            $this->session->set_userdata('successMsg', $dataname.' successfully added.');
            return true;
        }
        else {
            $this->session->set_userdata('errorMsg', $dataname.'could not be saved. Try again later.');
            return false;
        }
    }

    function addBatchToTable($table, $batch_array){
        $query = $this->db->insert_batch($table, $batch_array);
        
        return $query;
    }
	
	function updateTable($table, $array, $edit_array, $dataname=''){
        $this->output->enable_profiler(TRUE);
		$qry = $this->db->update($table, $array, $edit_array);
		
		if($qry){
			$this->session->set_userdata('successMsg', $dataname.' successfully updated.');
			return true;
		}
		else{
			$this->session->set_userdata('errorMsg', $dataname.' could not be updated. Try again later.');
			return false;
		}
	}

    function updateBatchTable($table, $batchUpdateArray, $where_key){
        $query = $this->db->update_batch($table, $batchUpdateArray, $where_key);

        return $query;
    }
	
    # Delte row(s) of data. Optional Soft delete by updating `deleted` column to 1
	function deleteFromTable($table, $array, $dataname=''){
		$qry = $this->db->delete($table, $array);
		
		if($qry){
			$this->session->set_userdata('successMsg', $dataname.' successfully deleted.');
			return true;
		}
		else{
			$this->session->set_userdata('errorMsg', $dataname.' could not be deleted. Try again later.');
			return false;
		}
	}
    
    /*
     * Create a long/short format
     * Ref: http://www.w3schools.com/php/func_date_date.asp
     * 
     * @format
     * short (default): January 1st, 2015
     * mini: 1/1/2015
     * long: Saturday 18th of April 2015 05:39:58 AM
     *
     * @date_add: how much time after added date
     *
     */
    function date_format($date, $format='', $date_add=''){
        if($format == 'long'){
            $fmt = 'l jS \of F Y h:i:s A';
        }
        else if($format == 'mini'){
            $fmt = 'd/m/Y';
        }
        else{
            $fmt = 'M jS, Y';
        }
        
        $date_str = strtotime($date);
        
        if($date_add != ''){
            $date_str = strtotime($date . $date_add);
        }
        return date($fmt, $date_str);
    }

    /**
     * 
     * Send email
     *
     * @param emailObj: [email, name, subject, message]
     * 
     */
    function sendEmail($emailObj){
        # var_dump($emailObj);
        $this->load->library('email');

        $obj = (object) $emailObj;

        $email = !empty($obj->email) ? $obj->email : '';
        $name = !empty($obj->name) ? $obj->name : '';
        $subject = !empty($obj->subject) ? $obj->subject : '';
        $message = !empty($obj->message) ? $obj->message : '';

        # Site email info
        $siteName = $this->config->item('site_name');
        $siteEmail = $this->config->item('site_email');

        if(empty($email)){
            echo 'Specify email';
        }
        elseif(empty($name)){
            echo 'Specify name';
        }
        elseif(empty($subject)){
            echo 'Specify subject';
        }
        elseif(empty($message)){
            echo 'Specify message';
        }
        elseif(empty($siteName)){
            echo 'Specify siteName';
        }
        elseif(empty($siteEmail)){
            echo 'Specify siteEmail';
        }
        else{
            $body = $this->_emailTemplate($message);

            $config['mailtype'] = 'html';

            $this->email->initialize($config);

            $this->email->from($siteEmail, $siteName);
            $this->email->reply_to($siteEmail, $siteName);
            $this->email->to($email);
            $this->email->subject($subject);
            $this->email->message($body);

            if(!$this->site_model->isLocalhost()){
                $this->email->send();
            }
            else{
                echo $body;
            }
        }
    }

    function _emailTemplate($message){
        $siteName = $this->config->item('site_name');
        $siteEmail = $this->config->item('site_email');
        $siteLogo = $this->config->item('site_logo');

        $logo = img('assets/img/'. $siteLogo, $siteName, 'style="width:220px;"');

        return '
            <table style="font-family:Arial;font-size:14px;width:100%;" bgcolor="#f6f6f6">
            <tr>
                <td valign="top" align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="border-radius: 3px;border: 1px solid #e9e9e9;" bgcolor="#fff">
                    <tr>
                        <td style="padding: 20px;" align="center" valign="top" bgcolor="#fff">'. $logo .'</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="margin: 0; padding: 20px; line-height:26px;" align="left" valign="top">
                            ' . $message . '
                        </td>
                    </tr>
                    </table>
                    <table width="600" cellpadding="0" cellspacing="0">
                    <tr style=" margin: 0;">
                        <td style="font-size: 12px; color: #999; padding:20px;" align="center" valign="top">
                            Questions? Email <a href="mailto:'. $siteEmail .'" 
                                style="color: #999; text-decoration: underline; margin: 0;">'. $siteEmail .'</a>
                        </td>
                    </tr>
                    </table>
                </td>
            </tr>
            </table>
        ';

        // <a href="#" style="color: #FFF; text-decoration: none; font-size: 13px; font-weight:bold; cursor: pointer; display: inline-block;
        //                    border-radius: 5px; background-color: #348eda; padding: 8px 12px;">
        //     Do this action
        // </a>
    }
}