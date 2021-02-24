<?php

/**
 * 
 * Output Open Graph meta tags for Facebook
 * 
*/
if(!function_exists('og_tag')){

    function og_tag($property, $content){
        echo "<meta property=\"$property\" content=\"$content\">";
    }
}

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
        render_base('inc/template', $pageContent, $pageTitle, $bodyClass, $pageData);
    }
}

if(!function_exists('render_admin')){

    function render_admin($pageContent, $pageTitle='', $bodyClass='', $pageData=array()){
        render_base('inc/template-admin', $pageContent, $pageTitle, $bodyClass, $pageData);
    }
}

if(!function_exists('nav_brand')){

    function nav_brand($uri='/', $logo=''){
        $CI =& get_instance();

        $siteName = $CI->config->item('site_name');
        $logo = empty($logo) ? $CI->config->item('site_logo') : $logo;
        $img = img("assets/img/$logo", $siteName, 'class="img-responsive"');
        echo anchor($uri, $img, 'class="navbar-brand"');
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
        echo '<li>'. anchor($url, $text, $class) .'</li>';
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

if(!function_exists('quick_filter')){

    /**
     * 
     * Generate inline filter form to render on pages with data. Pass the 
     * variables to be filtered and form will submit a get request using the
     * current url as the action
     * 
     * @param filters
     * @param includeDates: By default show date filters (to & from)
     * 
    */
    function quick_filter($filters=array(), $includeDates=TRUE){
        $CI =& get_instance();

        echo form_open('', 'class="form-inline" method="get"');

        if($includeDates){
            $from = $CI->input->get('from');
            $to = $CI->input->get('to');

            $filters['from'] = today();
            $filters['to'] = today();
        }

        foreach($filters as $k => $v){
            
            echo '<div class="form-group">';

            if(is_array($v)){
                echo form_dropdown($k, $v);
            }
            else{
                echo '<label class="control-label">'. ucwords($k) .'</label>';
                echo form_input($k, $v, 'class="form-control" style="margin:0 5px;"');    
            }

            echo '</div>';
        }

        echo '<button class="btn btn-primary">Filter</button>';

        echo form_close();

        echo '<hr style="border:0; margin-bottom:0;" />';
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
     * 
    */
    function form_box($name, $value, $type='text', $attrs=''){
        $attrs .= " type=\"$type\" ";
        $attrs .= ' class="form-control" ';

        echo '<div class="form-group">';

        switch($type){
            case 'password':
                echo form_password($name, $value, $attrs);
                break;

            case 'text':
            case 'email':
            default:
                echo form_input($name, $value, $attrs);
                break;
        }
        echo form_error($name);

        echo '</div>';
    }
}

if(!function_exists('form_box_label')){

    /**
    * 
    * Generate labeled form box with input & validation. Contained in form-group
    * 
    * @param obj: Array of type, name, label, value, class, attr 
    *
    */
    function form_box_label($obj=array()){
        $obj = (object) $obj;

        $name = isset($obj->name) ? $obj->name : '';
        $label = isset($obj->label) ? $obj->label : '';
        $type = isset($obj->type) ? $obj->type : '';
        $value = isset($obj->value) ? $obj->value : '';

        $str = '';
        $str .= !empty($name) ? " name=\"$name\" " : '';
        $str .= ' class="form-control '. (isset($obj->class) ? "$obj->class\" " : '') . '" ';
        $str .= isset($obj->attrs) ? " $obj->attrs " : '';
        $str = trim($str);

        echo '<div class="form-group">';
        echo "<label class=\"control-label col-sm-4\">$label</label>";

        echo '<div class="col-sm-8">';

        switch($type){
            case 'textarea':
                echo "<textarea $str>$value</textarea>";
                break;

            default:
                $str .= isset($obj->value) ? " value=\"$obj->value\" " : '';
                $str .= !empty($type) ? " type=\"$type\" " : '';
                echo "<input $str />";
                break;
        }
        echo form_error($name);

        echo '</div>';

        echo '</div>';
    }
}

if(!function_exists('form_box_large')){

    /**
     * 
     * Generate form box with textarea
     * 
    */
    function form_box_large($name, $value, $attrs=''){
        $attrs .= ' class="form-control" rows="3"';

        echo '<div class="form-group">';

        echo form_textarea($name, $value, $attrs);
        echo form_error($name);

        echo '</div>';
    }
}


if(!function_exists('form_radio_inline')){

    /**
    * 
    * Generate inline radio button
    * 
    */
    function form_radio_inline($name, $value, $label, $checked=FALSE){
        echo '<label class="radio-inline">';

        echo form_radio($name, $value, $checked);
        echo " $label";

        echo '</label>';
    }
}

if(!function_exists('form_box_button')){

    /**
     * 
     * Return form submit button markup in form box
     * 
    */
    function form_box_button($text, $attrs='class="btn btn-lg btn-block btn-primary"'){
        echo '<div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">';
        echo "<hr/><button $attrs >$text</button>";
        echo '</div>
            </div>';
    }
}

if(!function_exists('stat_box')){

    /**
     * 
     * Return stat box
     * 
     * 
    */
    function stat_box($number, $label, $perm=TRUE){
        $number = empty($number) ? 0 : $number;

        if($perm){
            echo "<div class=\"col-sm-2 col-xs-4 stat-box\">
                    <div>
                        <h3>$number</h3>
                        <p>$label</p>
                    </div>
                </div>";
        }
    }
}

if(!function_exists('quick_validate')){

    /**
     * 
     * Check data object whether required variables are empty
     * 
     * Return FALSE if any is empty
     * 
    */
    function quick_validate($vars, $keys){
        $vars = (object) $vars;
        $errors = array();

        foreach($vars as $key => $value){
            $value = $vars->$key;

            if(in_array($key, $keys) && empty($value)){
                array_push($errors, $key);
            }
        }

        return empty($errors) ? TRUE : (object) array(
            'error'=>true,
            'errorStr'=>join(', ', $errors)
        );
    }
}

?>
