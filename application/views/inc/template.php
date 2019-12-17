<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php 
    $pageTitle = isset($page_title) ? $page_title : '';
    $pageDescription = isset($page_description) ? substr($page_description, 0, 200) : '';
    $pageAuthor = isset($page_author) ? $page_author : '';
    $pageKeywords = isset($page_keywords) ? $page_keywords : '';
    $pageImage = isset($page_image) ? $page_image : '';

    echo $pageTitle; 
?></title>
<?php

    /**
     * 
     * SEO Meta Tags
     * 
     * - https://codeigniter.com/user_guide/helpers/html_helper.html#meta
     * - https://css-tricks.com/essential-meta-tags-social-media/
     * 
     */
    $seoMeta = array(

        # Basic Meta
        array('name'=>'viewport', 'content'=>'width=device-width, initial-scale=1'), # For responsive layouts
        array(
            'name'=>'Content-type',
            'content'=>'text/html; charset=utf-8', 
            'type'=>'equiv'
        ),

        # Page Meta
        array('name'=>'description', 'content'=>$pageDescription),
        array('name'=>'author', 'content'=>$pageAuthor),

        # Facebook
        array(
            array('property' => 'og:title', 'content' => $pageTitle),
            array('property' => 'og:description', 'content' => $pageDescription),
            array('property' => 'og:image', 'content' => $pageImage),
            array('property' => 'og:url', 'content' => current_url()),
        ),

        # Twitter
        array('name' => 'twitter:title', 'content' => $pageTitle),
        array('name' => 'twitter:description', 'content' => $pageDescription),
        array('name' => 'twitter:image', 'content' => $pageImage),
        array('name' => 'twitter:card', 'content' => $pageImage)
    );

    echo meta($seoMeta);


    echo link_tag('favicon.png', 'shortcut icon', 'image/png');

    $styles = array(
        'assets/css/bootstrap.min.css',
        'assets/css/ionicons.min.css',
        'assets/css/bootstrap-datepicker.min.css',
        'assets/plugins/timepicker/jquery.timepicker.min.css',
        'assets/plugins/bxslider/jquery.bxslider.css',
        'assets/css/style.css'
    );

    foreach($styles as $stl){
        echo link_tag($stl);
    }

?>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
        <script src="http://getbootstrap.com/docs-assets/js/html5shiv.js"></script>
        <script src="http://getbootstrap.com/docs-assets/js/respond.min.js"></script>
<![endif]-->
<?php 
    $gaCode = $this->config->item('ga_code');
    
    if(!$this->index_model->isLocalhost() && !empty($gaCode)){
        echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id=$gaCode\"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '$gaCode');
        </script>";
    }
?>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <div class="clearfix wrapper">
        <nav class="navbar static" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" 
                        data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo site_url(); ?>">
                        <?php echo img('assets/img/site-logo.png', 'Site Logo'); ?>
                    </a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="<?php echo site_url(); ?>">Home</a></li>
                        <li><a href="<?php echo site_url('about'); ?>">About</a></li>
                        <li><a href="<?php echo site_url('contact-us'); ?>">Contact Us</a></li>
                    </ul>

                    <?php
                        $user = $this->auth_model->get_user_data();
                        # var_dump($user);

                        if(isset($user->id)){
                    ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?php
                                    echo isset($user->name) ? $user->name : '-'; 
                                    
                                ?> <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('logout'); ?>">Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </nav>
        <div class="page-wrapper clearfix">
        <?php
            $this->load->view($page_content);
        ?>
        </div>
    </div>

    <footer>
        <div class="container clearfix">
            <p>&copy; <?php echo date('Y') .' '. $this->config->item('site_name'); ?>. All Rights Reserved.</p>
        </div>
    </footer>
    <?php
        # $this->output->enable_profiler(TRUE);
        
        $scripts = array(
            'assets/js/jquery.min.js',
            'assets/js/bootstrap.min.js',
            'assets/plugins/bxslider/jquery.bxslider.min.js',
            'assets/js/custom.js'
        );
        
        foreach($scripts as $script){
            echo '<script src="' . base_url($script) . '"></script>';
        }
    ?>
</body>
</html>
