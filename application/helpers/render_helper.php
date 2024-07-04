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
function render_base($template, $pageContent, $pageTitle='', $bodyClass='', $pageData=[]){
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

    function render_page($pageContent, $pageTitle='', $bodyClass='', $pageData=[]){
        render_base('inc/template', $pageContent, $pageTitle, $bodyClass, $pageData);
    }
}

if(!function_exists('render_auth')){

    function render_auth($pageContent, $pageTitle='', $bodyClass='', $pageData=[]){
        render_base('inc/template', $pageContent, $pageTitle, $bodyClass, $pageData);
    }
}

if(!function_exists('render_admin')){

    function render_admin($pageContent, $pageTitle='', $bodyClass='', $pageData=[]){
        render_base('inc/template-admin', $pageContent, $pageTitle, $bodyClass, $pageData);
    }
}

if(!function_exists('render_header')){

    function render_header($pageTitle){
        $title = isset($pageTitle) ? $pageTitle : '-';
        echo "<div class=\"page-header\">
                <h1>$title</h1>
            </div>";
    }
}

if(!function_exists('blank_state')){

    function blank_state($text, $class=''){
        $class = !empty($class) ? $class : 'alert-warning';
        echo "<div class=\"alert $class\">$text</div>";
    }
}

if(!function_exists('breadcrumb_link')){
    function breadcrumb_link($url, $text){
        echo '<li class="breadcrumb-item">'. anchor($url, $text) .'</li>';
    }
}

if(!function_exists('breadcrumb_active')){
    function breadcrumb_active($text){
        echo '<li class="breadcrumb-item active">'. $text .'</li>';
    }
}

if(!function_exists('dropdown_link')){
    function dropdown_link($url, $text){
        echo '<li>'. anchor($url, $text, 'class="dropdown-item"') .'</li>';
    }
}

if(!function_exists('nav_brand')){
    /**
     * 
     * Build nav brand link
     * 
    */
    function nav_brand($url=''){
        $CI =& get_instance();

        $siteName = $CI->config->item('site_name');
        $logoUrl = $CI->config->item('site_logo');
        $siteLogo = img('assets/img/'. $logoUrl, $siteName);

        echo anchor($url, $siteLogo, 'class="navbar-brand"');
    }
}

if(!function_exists('nav_link')){

    /**
     * 
     * Build navbar url with link 
     * 
    */
    function nav_link($url, $text, $class=""){
        $class = !empty($class) ? "class=\"nav-link $class\"" : '';
        echo '<li class="nav-item">'. anchor($url, $text, $class) .'</li>';
    }
}

if(!function_exists('nav_divider')){

    /**
     * 
     * Return divider used in separating nav menu dropdown items
     * 
    */
    function nav_divider($text=''){
        echo "<li class=\"dropdown-divider\">$text</li>";
    }
}

if(!function_exists('nav_menu')){

    /**
     * 
     * Return nav menu items
     * 
    */
    function nav_menu($menu){
        foreach($menu as $m){
            $m = (object) $m;
            $active = uri_string() == $m->url ? 'active' : '';
            $perm = isset($m->perm) ? $m->perm : false;
            $class = isset($m->lclass) ? $m->lclass : '';

            if($perm == false){
                # Do not show anything
            }
            else if(isset($m->sublinks)){
                echo "<li class=\"nav-item dropdown\">
                    <a href=\"#\" class=\"nav-link dropdown-toggle $class\" role=\"button\" 
                        data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                        $m->title <b class=\"caret\"></b>
                    </a><ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdown\">";

                foreach($m->sublinks as $s){
                    $s = (object) $s;

                    if(!empty($s->perm) && !$s->perm){
                        # Hide item
                    }
                    else if(isset($s->divider)){
                        nav_divider($s->divider);
                    }
                    else{
                        dropdown_link($s->url, $s->title);
                    }
                }

                echo '</ul></li>';
            }
            else{
                nav_link($m->url, $m->title, $class .' '. $active);
            }
        }
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

        return ['url'=>$url, 'title'=>$title, 'perm'=>$permission];
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
    function quick_filter($filters=[], $includeDates=TRUE){
        $CI =& get_instance();

        echo form_open('', 'class="form-inline" method="get"');

        if($includeDates){
            $from = $CI->input->get('from');
            $to = $CI->input->get('to');

            $filters['from'] = !empty($from) ? $from : today();
            $filters['to'] = !empty($to) ? $to : today();
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
    function form_box_label($obj=[]){
        $obj = (object) $obj;

        $name = isset($obj->name) ? $obj->name : '';
        $label = isset($obj->label) ? $obj->label : '';
        $type = isset($obj->type) ? $obj->type : 'text';
        $value = isset($obj->value) ? $obj->value : '';

        # Show custom content
        $customContent = isset($obj->content) ? $obj->content : '';

        $required = isset($obj->required) ? $obj->required : FALSE;
        $requiredStr = $required ? 'required' : '';

        $str = '';
        $str .= !empty($name) ? " name=\"$name\" " : '';
        $str .= ' class="form-control '. (isset($obj->class) ? "$obj->class\" " : '') .'" ';
        $str .= isset($obj->attrs) ? " $obj->attrs " : '';
        $str .= $requiredStr;
        $str = trim($str);

        echo '<div class="form-group">';
        echo "<label class=\"control-label col-sm-4 $requiredStr\">$label</label>";

        echo '<div class="col-sm-8">';

        switch($type){
            case 'custom':
                echo $customContent;
                break;

            case 'textarea':
                echo "<textarea $str>". set_value($name, $value) ."</textarea>";
                break;

            case 'select':
                $select = "<select $str>";
                $select .= isset($obj->hint) ? "<option>$obj->hint</option>" : '';

                if(isset($obj->options)){
                    foreach($obj->options as $o){
                        $o = (array) $o;
                        $optLabel = $o[$obj->optLabelKey];
                        $optValue = $o[$obj->optValueKey];
                        $isSelected = $optValue === $value;

                        $select .= "<option value=\"$optValue\" ". 
                            set_select($name, $optValue, $isSelected) .">$optLabel</option>";
                    }
                }

                $select .= "</select>";

                echo $select;
                break;

            # Renders special radio button with Yes/No options 
            case 'yes_no':
                $opts = ['1'=>'Yes', '0'=>'No'];

                foreach($opts as $v => $optLabel){
                    $isChecked = $value == $v;

                    echo "<label class=\"radio-inline\">
                        <input type=\"radio\" name=\"$name\" value=\"$v\"". 
                            set_radio($name, $value, $isChecked) ." $requiredStr /> $optLabel
                    </label>";
                }
                break;

            case 'file':
                $accept = isset($obj->accept) ? $obj->accept : '';
                $fileUrl = isset($obj->fileUrl) ? $obj->fileUrl : '';
                $uploadType = isset($obj->uploadType) ? $obj->uploadType : '';

                if($uploadType == 'image'){
                    echo img($fileUrl, $label, 'class="img-responsive" 
                        style="margin-bottom:10px; max-width:150px;"');
                }

                echo "<input type=\"file\" name=\"$name\" accept=\"$accept\" />";
                break;

            default:
                $str .= isset($obj->value) ? " value=\"". set_value($name, $value) ."\" " : '';
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
    */
    function stat_box($number, $label, $perm=TRUE){

        if($perm){
            echo "<div class=\"col-sm-2 col-xs-6 stat-box\">
                    <div>
                        <h3>$number</h3>
                        <p class=\"ellipsis\">$label</p>
                    </div>
                </div>";
        }
    }
}

if(!function_exists('stat_box_link')){

    /**
     * 
     * Return stat box
     * 
     * 
    */
    function stat_box_link($number, $label, $uri, $perm=TRUE){
        $number = empty($number) ? 0 : $number;

        if($perm){
            echo "<div class=\"col-sm-2 col-xs-4 stat-box\">
                    <a href=". site_url($uri) .">
                        <div>
                            <h3>$number</h3>
                            <p>$label</p>
                        </div>
                    </a>
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
        $errors = [];

        foreach($vars as $key => $value){
            $value = $vars->$key;

            if(in_array($key, $keys) && empty($value)){
                array_push($errors, $key);
            }
        }

        return empty($errors) ? TRUE : (object) [
            'error'=>true,
            'errorStr'=>join(', ', $errors)
        ];
    }
}

if(!function_exists('yes_no_label')){

    /**
     * 
     * Render yes/no label
     * 
    */
    function yes_no_label($value){
        return isset($value) && $value == '1' ? 
            '<label class="label label-success">Yes</label>' :
            '<label class="label label-danger">No</label>';
    }
}

?>
