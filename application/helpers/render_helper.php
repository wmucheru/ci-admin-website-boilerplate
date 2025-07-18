<?php

define('ARRAY_VALUE_SEPARATOR', ', ');

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
        $class = !empty($class) ? $class : 'alert-info';
        echo "<div class=\"alert $class\">$text</div>";
    }
}

if(!function_exists('breadcrumb_link')){
    function breadcrumb_link($url, $text){
        echo '<li class="breadcrumb-item">'. anchor($url, $text) .'</li>';
    }
}

if(!function_exists('breadcrumb_home')){

    /**
     * 
     * Breadcrumb home link with icon
     * 
    */
    function breadcrumb_home(){
        echo '<li class="breadcrumb-item">'. 
            anchor('admin/dashboard', '<i class="ion-md-home"></i>') .
        '</li>';
    }
}

if(!function_exists('breadcrumb_text')){
    function breadcrumb_text($text){
        echo '<li class="breadcrumb-item">'. $text .'</li>';
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

if(!function_exists('nav_auth')){

    /**
     * 
     * Render auth nav menu
     * 
    */
    function nav_auth(){
        $CI =& get_instance();
        $user = $CI->auth_model->get_user_data();

        if(!empty($user->name)){
            echo '<ul class="navbar navbar-nav">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" 
                        aria-expanded="false">'. $user->name .'<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">';

            dropdown_link('/', 'Website');
            dropdown_link('logout', 'Log Out');

            echo '</ul>
                </li>
            </ul>';
        }
        else{
            echo anchor('auth', '<i class="ion-md-person"></i> Login', 
                'class="btn btn-outline-danger"');
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
            echo '<div class="row mb-3">';

            if(is_array($v)){
                echo form_dropdown($k, $v);
            }
            else{
                echo '<label class="col-form-label">'. ucwords($k) .'</label>';
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
                echo '<div class="row mb-3">';
                echo '<label class="col-form-label col-sm-3">'. ucwords($k) .'</label>';
                
                echo '<div class="col-sm-9">';

                echo form_input($k, $v, 'class="form-control"');

                echo '</div>';

                echo '</div>';
            }
        }

        echo '<div class="offset-sm-3 col-sm-9">';
        echo '<hr/><button class="btn btn-block btn-success">Update</button>';
        echo '</div>';

        echo form_close();
    }
}

if(!function_exists('form_legend')){

    /**
     * 
     * Add legend to form
     * 
    */
    function form_legend($label){
        echo '<legend>'. $label .'</legend>';
    }
}

if(!function_exists('form_box')){

    /**
     * 
     * Generate form box with input & validation. Contained in row mb-3
     * 
     * 
    */
    function form_box($name, $value, $type='text', $attrs=''){
        $attrs .= " type=\"$type\" ";
        $attrs .= ' class="form-control" ';

        echo '<div class="row mb-3">';

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

if(!function_exists('form_box_input')){

    /**
    * 
    * Generate form input
    * 
    * @param obj: Array of type, name, label, value, class, attr 
    *
    */
    function form_box_input($obj=[]){
        $obj = (object) $obj;

        $name = isset($obj->name) ? $obj->name : '';
        $nameStr = !empty($name) ? " name=\"$name\" " : '';

        $label = isset($obj->label) ? $obj->label : '';
        $type = isset($obj->type) ? $obj->type : 'text';
        $value = isset($obj->value) ? $obj->value : '';
        $class = isset($obj->class) ? $obj->class : '';

        # Placeholder
        $placeholder = isset($obj->placeholder) ? $obj->placeholder : '';

        # Rows for textarea
        $rowStr = isset($obj->rows) ? "rows=\"$obj->rows\"" : '';

        # Custom labels for `yes_no` type
        $labelYes = isset($obj->labelYes) ? $obj->labelYes : 'Yes';
        $labelNo = isset($obj->labelNo) ? $obj->labelNo : 'No';

        # Options for `radio` type
        $options = isset($obj->options) ? $obj->options : [];

        # Vertical orientation for `radio` type
        $vertical = isset($obj->vertical) ? $obj->vertical : false;

        # Show custom content
        $customContent = isset($obj->content) ? $obj->content : '';

        $classStr = empty($class) ? 'class="form-control"' : '';

        $required = isset($obj->required) ? $obj->required : FALSE;
        $requiredStr = $required ? ' required ' : '';

        $disabled = isset($obj->disabled) ? $obj->disabled : FALSE;
        $disabledStr = $disabled ? ' disabled ' : '';

        $attrStr = isset($obj->attrs) ? " $obj->attrs " : '';

        $str = $nameStr;
        $str .= $rowStr;
        $str .= $classStr;
        $str .= $requiredStr;
        $str .= $disabledStr;
        $str .= $attrStr;
        $str = trim($str);

        switch($type){
            case 'custom':
                echo $customContent;
                break;

            case 'textarea':
                echo "<textarea $str>". set_value($name, $value) ."</textarea>";
                break;

            case 'richtext':
                echo "<textarea $str data-type=\"richtext\">". 
                    set_value($name, $value) ."</textarea>";
                break;

            case 'select':
            case 'multiselect':
                $classStr = ' class="form-select '. $class .'" ';

                $typeStr = $type == 'multiselect' ? ' multiple' : '';

                $select = "<select $nameStr $typeStr $requiredStr $attrStr $disabledStr $classStr>";
                $select .= !empty($placeholder) ? "<option value=\"\">$placeholder</option>" : '';

                if(!empty($options)){
                    foreach($options as $o){
                        if(is_array($o) || is_object($o)){
                            $o = (array) $o;

                            $optLabel = $o[$obj->optLabelKey];
                            $optValue = $o[$obj->optValueKey];
                        }
                        else{
                            $optLabel = $o;
                            $optValue = $o;
                        }

                        $isSelected = $type == 'multiselect' ? 
                            in_array($optValue, $value) : 
                            $optValue === $value;

                        $select .= "<option value=\"$optValue\" ". 
                            set_select($name, $optValue, $isSelected) .">$optLabel</option>";
                    }
                }

                $select .= "</select>";

                echo $select;
                break;

            case 'radio':
                echo '<div class="d-block clearfix">';

                foreach($options as $option){
                    $option = (array) $option;

                    $optLabel = $option[$obj->optLabelKey];
                    $optValue = $option[$obj->optValueKey];
                    $checked = $optValue == $value ? 'checked' : '';

                    $block = "<div class=\"form-check\">
                            <input type=\"radio\" class=\"form-check-input\" name=\"$name\" 
                                value=\"$optValue\" $checked $requiredStr $disabledStr />
                            <label class=\"form-check-label\">$optLabel</label>
                        </div>";

                    $inlineClass = $vertical ? '' : 'form-check-inline';

                    echo "<div class=\"form-check $inlineClass\">$block</div>";
                }

                echo '</div>';
                break;

            case 'checkbox':
                echo '<div class="clearfix">';

                foreach($options as $option){
                    $option = (array) $option;
                    $optLabel = $option[$obj->optLabelKey];
                    $optValue = $option[$obj->optValueKey];

                    $valueArray = is_array($value) ? $value : explode(ARRAY_VALUE_SEPARATOR, $value);
                    $checked = in_array($optValue, $valueArray) ? ' checked ' : '';

                    $block = "<div class=\"form-check\">
                            <input type=\"checkbox\" class=\"form-check-input\" name=\"$name\" 
                                value=\"$optValue\" $checked $requiredStr $disabledStr />
                            <label class=\"form-check-label\">$optLabel</label>
                        </div>";

                    $inlineClass = $vertical ? '' : 'form-check-inline';

                    echo "<div class=\"form-check $inlineClass\">$block</div>";
                }

                echo '</div>';
                break;

            case 'phone':
                $valueStr = isset($obj->value) ? " value=\"". set_value($name, $value) ."\" " : '';

                echo "<input type=\"tel\" class=\"form-control intl-phone\" data-name=\"$name\"
                    style=\"max-width:32rem;\" $attrStr $valueStr $requiredStr />";
                break;

            # Renders special radio button with Yes/No options 
            case 'yes_no':
                $opts = ['1'=>$labelYes, '0'=>$labelNo];

                echo '<div class="clearfix">';

                foreach($opts as $v => $optLabel){
                    $checked = set_radio($name, $value, $v == $value);

                    echo "<div class=\"form-check form-check-inline\">
                            <input type=\"radio\" class=\"form-check-input\" name=\"$name\" value=\"$v\" ". 
                                $checked ." $requiredStr />
                            <label class=\"form-check-label\">$optLabel</label>
                        </div>";
                }

                echo '</div>';
                break;

            case 'country':
                $select = "<select $str>";
                $select .= !empty($placeholder) ? "<option value=\"\">$placeholder</option>" : '';

                $countries = get_country_list();

                foreach($countries as $c){
                    $selected = set_select($name, $value, $c->id == $value);
                    $select .= "<option value=\"$c->id\" $selected>$c->name</option>";
                }

                $select .= '</select>';
                echo $select;
                break;

            case 'file':
                $accept = isset($obj->accept) ? $obj->accept : '';
                $fileUrl = isset($obj->fileUrl) ? $obj->fileUrl : '';
                $uploadType = isset($obj->uploadType) ? $obj->uploadType : '';
                $types = !empty($uploadType) ? explode('|', UPLOAD_TYPES_IMAGE) : [];

                # Override `accept` if document type is available
                $acceptStr = !empty($uploadType) ? (".". join(",.", $types)) : $accept;

                $uploadStr = '<div style="clear:both; padding:5px 0;">';
                $uploadStr .= "<input type=\"file\" name=\"$name\" 
                    accept=\"$acceptStr\" $requiredStr />";

                if(empty($fileUrl)){
                    # Show nothing
                }
                else if($uploadType == 'image'){
                    $uploadStr .= "<br/>";
                    $uploadStr .= img($fileUrl, $label, 'class="img-fluid" 
                        style="margin:5px 0 0; max-width:270px;"');
                }
                else{
                    $uploadStr .= "<br/>";
                    $uploadStr .= anchor($fileUrl, 
                        '<strong style="display:block; padding-top:10px;">Click to View file</strong>', 
                        'target="_blank"');
                }

                $uploadStr .= '</div>';
                echo $uploadStr;
                break;

            case 'date':
                $str .= isset($obj->value) ? ' value="'. set_value($name, $value) ."\" " : '';
                $str .= !empty($type) ? " type=\"$type\" " : '';
                echo "<input $str style=\"max-width:12em;\" />";
                break;

            case 'range':
                $str = isset($obj->value) ? ' value="'. set_value($name, $value) ."\" " : '';
                $str .= ' class="form-range '. $class .'" ';

                echo "<input type=\"$type\" $str $requiredStr $disabledStr $attrStr />";
                break;

            case 'password':
                $str .= isset($obj->value) ? ' value="'. set_value($name, $value) ."\" " : '';
                $str .= !empty($type) ? " type=\"$type\" " : '';
                echo "<input $str />";

                // TODO: Add password show/hide toggler
                echo '<script></script>';
                break;

            default:
                $str .= isset($obj->value) ? ' value="'. set_value($name, $value) ."\" " : '';
                $str .= !empty($type) ? " type=\"$type\" " : '';
                $str .= !empty($placeholder) ? " placeholder=\"$placeholder\" " : '';

                echo "<input $str />";
                break;
        }
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

        $hint = isset($obj->hint) ? $obj->hint : '';

        # Rows for textarea
        $rowStr = isset($obj->rows) ? "rows=\"$obj->rows\"" : '';

        # Floating form boxes
        $floating = isset($obj->floating) ? $obj->floating : FALSE;

        # Holder class
        $holderClass = isset($obj->holderClass) ? $obj->holderClass : '';

        $required = isset($obj->required) ? $obj->required : FALSE;
        $requiredStr = $required ? ' required ' : '';

        $disabled = isset($obj->disabled) ? $obj->disabled : FALSE;
        $disabledStr = $disabled ? ' disabled ' : '';

        $attrStr = isset($obj->attrs) ? " $obj->attrs " : '';

        $str = '';
        $str .= !empty($name) ? " name=\"$name\" " : '';
        $str .= ' class="form-control '. (isset($obj->class) ? "$obj->class\" " : '') .'" ';
        $str .= $rowStr;
        $str .= $requiredStr;
        $str .= $disabledStr;
        $str .= $attrStr;
        $str = trim($str);

        if($floating){
            echo '<div class="mb-3 form-floating'. $holderClass .'">';

            form_box_input($obj);

            echo "<label class=\"$requiredStr\">$label</label>";

            if(!empty($hint)){
                echo "<div class=\"text-info\">
                    <small>$hint</small>
                </div>";
            }

            echo form_error($name);

            echo '</div>';
        }
        else{
            echo '<div class="row mb-4 '. $holderClass .'">';
            echo "<label class=\"col-md-4 col-form-label text-end $requiredStr\">$label</label>";

            echo '<div class="col-md-8">';

            form_box_input($obj);

            echo form_error($name);

            if(!empty($hint)){
                echo "<div class=\"text-info\">
                    <small>$hint</small>
                </div>";
            }

            echo '</div>';

            echo '</div>';
        }
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

        echo '<div class="row mb-3">';

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
    function form_box_button($text, $attrs='class="btn btn-lg btn-primary"'){
        echo '<div class="offset-md-4 mb-3">
            <hr class="border border-light-subtle" />
            <button '. $attrs .'>
                <i class="ion-md-checkmark"></i> '. $text .
            '</button>
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
            echo "<div class=\"col-xs-6 col stat-box\">
                    <div>
                        <h3>$number</h3>
                        <p class=\"text-truncate\">$label</p>
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
            echo "<div class=\"col-xs-6 col stat-box\">
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
            '<label class="badge bg-success">Yes</label>' :
            '<label class="badge bg-danger">No</label>';
    }
}

if(!function_exists('status_label')){

    /**
     * 
     * Render status label
     * 
    */
    function status_label($status){
        $label = '';

        switch(strtolower($status)){
            case 'pending':
                $label = "<span class=\"badge bg-warning text-uppercase\">$status</span>";
                break;

            case 'active':
                $label = "<span class=\"badge bg-success text-uppercase\">$status</span>";
                break;

            case 'complete':
                $label = "<span class=\"badge bg-primary text-uppercase\">$status</span>";
                break;

            case 'cancelled':
                $label = "<span class=\"text-warning text-uppercase\">$status</span>";
                break;

            default:
                $label = "<span class=\"text-info text-uppercase\">$status</span>";
                break;
        }

        return $label;
    }
}

?>
