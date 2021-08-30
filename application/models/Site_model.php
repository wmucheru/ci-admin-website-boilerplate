<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define('UPLOADS_MAX_SIZE', 5120);

# File extensions based on type
define('UPLOAD_TYPES_IMAGE', 'jpg|png|jpeg');
define('UPLOAD_TYPES_DOCUMENT', 'pdf|doc|docx|xls|xlsx');

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

    /**
     * 
     * File Uploads
     * 
     * This module is responsible for handling file uploads and/or processing of files. Any 
     * module making use of this function can set the options required.
     * 
     * @param obj: Key value object with upload options
     * 
     * GENERAL
     * - upload_path: Path to upload file (required)
     * - allowed_types: Pipe-separated types of files allowed e.g. jpg|png|gif (required)
     * - field_name: Name of the upload input field (required)
     * - file_name: Name of the file. Defaults to autogenerated filename (required)
     * - max_size: Maximum size of the file. Defaults to 5MB
     * 
     * IMAGES
     * - max_height: Maximum height of file (for images)
     * - max_width: Maximum width of file (for images)
     * 
     * RESIZING
     * - resize: Boolean to allow resizing. Default FALSE
     * - resize_height: Resize height of file (for images)
     * - resize_width: Resize width of file (for images)
     * - resize_path: Destination of resized image
     * 
     * WATERMARK
     * - watermark: Boolean to allow watermarking. Default FALSE
     * - watermark_image: Path to watermark image from `assets/img` folder (for images)
     * 
    */
    function uploadFile($obj){
        $obj = (object) $obj;
        $response = array();

        if(empty($obj->field_name)){
            $response['error'] = 'Field name is required';
        }
        else if(empty($obj->upload_path)){
            $response['error'] = 'Upload path is required';
        }
        else if(empty($obj->allowed_types)){
            $response['error'] = 'Allowed types is required';
        }
        else{
            $fileName = !empty($obj->file_name) ? $obj->file_name : $this->generateRef();
            $maxSize = !empty($obj->max_size) ? $obj->max_size : UPLOADS_MAX_SIZE;
            $uploadPath = "$this->uploads_root/$obj->upload_path";

            $config = array(
                'upload_path' => $uploadPath,
                'allowed_types' => $obj->allowed_types,
                'file_name' => $fileName,
                'overwrite' => TRUE,
                'max_size' => $maxSize
            );

            # Max dimensions
            $config['max_height'] = isset($obj->max_height) ? $obj->max_height : 0;
            $config['max_width'] = isset($obj->max_width) ? $obj->max_width : 0;

            $this->upload->initialize($config);

            if($this->upload->do_upload($obj->field_name)){
                $uploadData = $this->upload->data();

                # Resizing options
                if(isset($obj->resize) && $obj->resize === TRUE){
                    $dimensions = array();

                    if(!empty($obj->resize_height)){
                        $dimensions['height'] = $obj->resize_height;
                    }

                    if(!empty($obj->resize_width)){
                        $dimensions['width'] = $obj->resize_width;
                    }

                    $resizePath = !empty($obj->resize_path) ? $obj->resize_path : $uploadPath;

                    $uploadData['resize'] = $this->resizeImage($uploadData['file_name'], $resizePath, $dimensions);
                }

                # Watermark options
                if(isset($obj->watermark) && $obj->watermark === TRUE){

                    if(!empty($obj->watermark_image)){
                        $uploadData['watermark'] = $this->watermarkImage($obj->watermark_image);
                    }
                }

                $response = $uploadData;
            }
            else{
                $response['error'] = $this->upload->display_errors();
            }
        }

        return (object) $response;
    }

    /**
     * 
     * Resizing for images
     * 
    */
    function resizeImage($fileName, $filePath, $dimensions=array('height'=>400)){
        $config = array(
            'source_image' => $filePath . $fileName,
            'new_image' => $filePath,
            'maintain_ratio' => TRUE,
            'thumb_marker' => '_thumb',
            'quality' => 100
        );

        if(isset($dimensions['height'])){
            $config['height'] = $dimensions['height'];
        }

        if(isset($dimensions['width'])){
            $config['width'] = $dimensions['width'];
        }

        $config['image_library'] = 'gd2';
        $this->image_lib->initialize($config);

        if(!$this->image_lib->resize()){
            $response = $this->image_lib->display_errors();
        }
        else{
            $response = 'Resize successful';
        }

        $this->image_lib->clear();

        return $response;
    }

    /**
     * 
     * Watermarks for images
     * 
     * @param imagePath: Path to image to be watermarked
     * @param watermarkImage: Image to be used as watermark. Preferrably in PNG format
     * 
    */
    function watermarkImage($imagePath, $watermarkImage=''){
        $watermark = $watermarkImage != '' ? $watermarkImage : 'assets/img/watermark.png';

        $config = array(
            'source_image'=>$imagePath,
            'wm_type'=>'overlay',
            'wm_overlay_path'=>$watermark,
            'wm_vrt_alignment'=>'middle',
            'wm_hor_alignment'=>'center',
            'new_image'=>$imagePath
        );

        $this->image_lib->clear();
        $this->image_lib->initialize($config);

        if(!$this->image_lib->resize()){
            $response = $this->image_lib->display_errors();
        }
        else{
            $response = 'Watermark successful';
        }

        $this->image_lib->clear();

        return $response;
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
                    array('Content-Type: application/json')
                );
                
                break;
            
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
                
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
}
