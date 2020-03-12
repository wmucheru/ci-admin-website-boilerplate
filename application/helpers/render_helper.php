<?php

/**
 * 
 * Render helper: Helps simplify page rendering process
 * 
 */
function render_base($template, $pageContent, $pageTitle='', $bodyClass='', $pageData=array()){
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
    
    $CI->load->view($template, $data);
}

if(!function_exists('render_page')){

    function render_page($pageContent, $pageTitle='', $bodyClass='', $pageData=array()){
        render_base('inc/template', $pageContent, $pageTitle, $bodyClass, $pageData);
    }
}

if(!function_exists('render_auth')){

    function render_auth($pageContent, $pageTitle='', $bodyClass='', $pageData=array()){
        render_base('inc/template-auth', $pageContent, $pageTitle, $bodyClass, $pageData);
    }
}

if(!function_exists('render_admin')){

    function render_admin($pageContent, $pageTitle='', $bodyClass='', $pageData=array()){
        render_base('inc/template-admin', $pageContent, $pageTitle, $bodyClass, $pageData);
    }
}

if(!function_exists('nav_link')){

    /**
     * 
     * Build navbar url with link 
     * 
    */
    function nav_link($url, $text, $class=''){
        $class = !empty($class) ? "class=\"$class\"" : '';
        echo '<li>'. anchor($url, $text, $class) .'<li>';
    }
}

if(!function_exists('nav_divider')){

    /**
     * 
     * Return divider used in separating nav menu dropdown items
     * 
    */
    function nav_divider($text=''){
        echo "<li class=\"divider\">$text</li>";
    }
}

if(!function_exists('tab_link')){

    /**
     * 
     * Build nav tab links
     * 
    */
    function tab_link($anchor, $label, $active=false){
        $active = $active ? ' class="active"' : '';
        echo '<li '. $active .'><a data-toggle="tab" href="'. $anchor .'">'. $label .'</a></li>';
    }
}

if (!function_exists('build_link')){

    /**
     * 
     * Build link with url, title and/or permissions
     * 
     * 
    */
    function build_link($url, $title='', $permission=''){
        $slugs = explode('/', $url);

        if(count($slugs) > 0){
            $segment = $slugs[count($slugs) - 1];
        }
        else{
            $segment = $url;
        }

        $title = !empty($title) ? $title : ucwords(strtolower($segment));

        return array('url'=>$url, 'title'=>$title, 'perm'=>$permission);
    }
}

if(!function_exists('quick_form')){

    /**
     * 
     * Generate quick form based on key-value array
     * 
    */
    function quick_form($obj, $url){
        echo form_open($url, 'class="form-horizontal"');

        foreach($obj as $k => $v){
            
            if($k == 'id'){
                echo form_hidden($k, $v);
            }
            else{
                echo '<div class="form-group">';
                echo '<label class="control-label col-sm-3">'. ucwords($k) .'</label>';
                
                echo '<div class="col-sm-9">';

                echo form_input($k, $v, 'class="form-control"');

                echo '</div>';

                echo '</div>';
            }
        }

        echo '<div class="col-sm-offset-3 col-sm-9">';
        echo '<hr/><button class="btn btn-block btn-success">Update</button>';
        echo '</div>';

        echo form_close();
    }
}

if(!function_exists('form_box')){

    /**
     * 
     * Generate form box with input & validation. Contained in form-group
     * 
    */
    function form_box($name, $value, $attrs='class="form-control"'){
        echo '<div class="form-group">';

        echo form_input($name, $value, $attrs);
        echo form_error('name');

        echo '</div>';
    }

}

?>