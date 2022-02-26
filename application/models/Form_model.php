<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Form_model extends CI_Model{

    function __construct(){
        parent::__construct();

        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    /**
     * 
     * Build a form using form object. The form object is as below:
     * 
     * {
     *      url: '',  // URL to submit to
     *      formType: '',  // Normal or multipart form
     *      fields: [
     *          {
     *              field: '',  // Name of field
     *              type: '',  // Type of field
     *              accept: '',  // Allowed file types for upload
     *              dimensions: 'W,H',  // Allowed dimensions for image uploads (W * H)
     *              label: '',  // Form label
     *              hint: '',  // Form hint
     *              required: '',  // Is required field?
     *              autofocus: '',  // Is field autofocus?
     *              options: '',  // Options for dropdown
     *              default: ''  // Default value
     *          },
     *          {...}, {...}
     *      ],
     *      submitLabel: ''
     * }
     * 
     * 
     */
    function buildForm($formObj){
        $formObj = (object) $formObj;
        
        $formType = !empty($formObj->formType) ? $formObj->formType : '';
        $url = $formObj->url;
        $fields = $formObj->fields;

        $formClass = 'class="form-horizontal"';

        $form = $formType == 'multipart' ? 
            form_open_multipart($url, $formClass) :
            form_open($url, $formClass);

        # Build form fields
        foreach($fields as $f){
            $f = (object) $f;

            $fieldName = $f->field;
            $label = isset($f->label) ? $f->label : '';

            $data = array(
                'name' => $f->field,
                'id' => $f->field,
                'value' => isset($f->value) && !empty($f->value) ? $f->value : set_value($fieldName),
                'class' => 'form-control'
            );

            # Add attributes
            if(isset($f->placeholder) && $f->placeholder == true){
                $data['placeholder'] = '';
            }

            if(isset($f->required) && $f->required == true){
                $data['required'] = '';
            }

            if(isset($f->autofocus) && $f->autofocus == true){
                $data['autofocus'] = '';
            }

            if(isset($f->disabled) && $f->disabled == true){
                $data['disabled'] = '';
            }

            $form .= '<div class="form-group">';
            
            if(!empty($f->label)){
                $form .= "<label for=\"$fieldName\" class=\"col-sm-4 control-label\">$label</label>";
            }

            $form .= '<div class="col-sm-8">';
            
            switch($f->type){

                case 'hidden':
                    $form .= form_hidden($f->field, $f->value);
                    break;

                case 'textarea':
                    $form .= form_textarea($data);
                    break;

                case 'upload':
                    $data['class'] = '';
                    $data['accept'] = $f->accept;

                    if(isset($f->dimensions)){
                        $data['data-dimensions'] = $f->dimensions;
                    }

                    $form .= '<div class="upload-box" style="background:#ddd; padding:5px;">';
                    $form .= form_upload($data);
                    $form .= img($f->fileUrl, 'Preview here', 'class="img-responsive" style="margin-top:5px;"'); # TODO: Preview documents in iframe
                    $form .= '</div>';

                    break;

                case 'password':
                    $form .= form_password($data);
                    break;

                case 'dropdown':
                    $defaultValue = isset($f->value) ? $f->value : set_select($fieldName);
                    $form .= form_dropdown($fieldName, $f->options, $defaultValue, 'class="form-control"');
                    break;
                
                default:
                    $form .= form_input($data);
                    break;
            }

            if(isset($f->hint)){
                $form .= "<div class=\"text-info\"><small>$f->hint</small></div>";
            }
            
            $formError = form_error($fieldName);
            $form .= "<div class=\"text-danger\">$formError</div>";

            $form .= '</div>';
            $form .= '</div>';
        }

        $form .= '<div class="col-sm-offset-4 col-sm-8"><hr/>';
        $form .= "<button class=\"btn btn-lg btn-block btn-success\">$formObj->submitLabel</button>";
        $form .= '</div>';

        $form .= form_close();

        return $form;
    }

    /**
     * 
     * Process form
     * 
     */
    function processForm($formObj){
        $formObj = (object) $formObj;

        # var_dump($formObj);

        $params = $formObj->fields;
        $sessionKey = $formObj->sessionKey;

        $validations = array();
        $uploads = array();
        $tableObj = array();

        # Update fields
        $isUpdate = false;
        $updateId = '';

        foreach($params as $p){
            $p = (object) $p;

            # set fields with validation
            if(isset($p->validation)){
                $validations[] = $p;
            }

            # set fields with uploads
            if(isset($p->type) && $p->type == 'upload'){
                $uploads[] = $p;
            }

            # build tableObj to save in database
            $tableObj[$p->field] = $this->input->post($p->field);

            # if form field has an id specified, set edit mode to true
            if(isset($p->field) && $p->field == 'id' && !empty($p->value)){
                $isUpdate = true;
                $updateId = $p->value;
            }
        }

        # Add createdBy where applicable
        if(isset($formObj->createdBy)){
            $tableObj['createdby'] = $this->auth_model->get_user_data()->id;
        }

        # Check validations
        foreach($validations as $v){
            $v = (object) $v;
            $this->form_validation->set_rules($v->field, $v->label, $v->criteria);
        }

        if(!$this->form_validation->run() == FALSE){
            return array(
                'error'=>true,
                'message'=>'Validation failed'
            );
        }
        else{
            $uploadStatus = array();
            $uploadsCount = count($uploads);
            $uploadedFiles = array();
            $uploadFails = array();

            foreach($uploads as $u){
                $u = (object) $u;

                if(!empty($_FILES[$u->field]['name'])){
                    $fieldName = $u->field;
                    $fileName = !empty($u->fileName) ? $u->fileName : $this->site_model->generateRef();
                    $uploadType = isset($u->uploadType) ? $u->uploadType : 'image';
                    $dimensions = isset($u->dimensions) ? explode(',', $u->dimensions) : array();

                    if(count($dimensions) == 2){
                        $dimensions = array(
                            'width'=>$dimensions[0],
                            'height'=>$dimensions[1]
                        );
                    }

                    $status = $this->site_model->uploadDocument($fieldName, $fileName, $uploadType, $dimensions);

                    if(!isset($status['error'])){
                        $uploadedFiles[] = $status['full_path'];
                        $tableObj[$fieldName] = $status['file_name'];
                    }
                    else{
                        $uploadFails[] = false;
                    }
                    
                    $uploadStatus[] = $status;
                }
                else{
                    # Don't have to upload non-required files
                }
            }
            
            # var_dump($_FILES);
            # var_dump($uploads);
            # var_dump($tableObj);
            # var_dump($uploadStatus); exit();

            /**
             * 
             * If a form contains upload fields, all uploads must have successfully uploaded 
             * before saving their values to the database; otherwise delete uploaded files
             * 
             */
            if(in_array(false, $uploadFails)){
                foreach($uploadedFiles as $u){
                    unlink($u);
                }

                $this->session->set_flashdata($sessionKey.'_fail', 'Could not upload files');

                return array(
                    'status'=>$uploadStatus
                );
            }

            # Update entry if applies
            else if($isUpdate){

                # remove empty fields
                foreach(array_keys($tableObj) as $f){

                    if(empty($tableObj[$f])){
                        unset($tableObj[$f]);
                    }
                }

                # var_dump($tableObj); exit();

                $this->db->update($formObj->table, $tableObj, ['id'=>$updateId]);
                $this->session->set_flashdata($sessionKey.'_success', ucfirst($sessionKey).' saved');

                return (object) array(
                    'message'=>'Successfully saved'
                );
            }

            # Save new entry
            else if($this->db->insert($formObj->table, $tableObj)){
                $this->session->set_flashdata($sessionKey.'_success', ucfirst($sessionKey).' saved');

                return (object) array(
                    'message'=>'Successfully saved'
                );
            }

            # Else; fail
            else{
                $this->session->set_flashdata($sessionKey.'_fail', 'Could not save ' . ucfirst($sessionKey));

                return (object) array(
                    'error'=>true,
                    'message'=>'Could not save details'
                );
            }
        }
    }
}