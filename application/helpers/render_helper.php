<?php

/**
 * 
 * Render helper: Helps simplify page rendering process
 * 
 */

if(!function_exists('render_page')){

    function render_page($pageContent, $pageTitle='', $bodyClass='', $pageData=array()){
        $CI =& get_instance();

        $data['page_title'] = $pageTitle;
        $data['page_content'] = $pageContent;
        $data['body_class'] = $bodyClass;

        if(!empty($pageData)){
            
            // Add keys to $data object
            foreach(array_keys($pageData) as $d){
                $data[$d] = $pageData[$d];
            }
        }
        
        $CI->load->view('inc/template', $data);
    }
}

if(!function_exists('render_admin')){

    function render_admin($pageContent, $pageTitle='', $bodyClass='', $pageData=array()){
        $CI =& get_instance();

        $data['page_title'] = $pageTitle;
        $data['page_content'] = $pageContent;
        $data['body_class'] = $bodyClass;

        if(!empty($pageData)){
            
            // Add keys to $data object
            foreach(array_keys($pageData) as $d){
                $data[$d] = $pageData[$d];
            }
        }
        
        $CI->load->view('inc/template-admin', $data);
    }
}

?>