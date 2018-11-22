<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo isset($page_title) ? $page_title : ''; ?></title>
<meta name="description" content=""/>
<meta name="author" content=""/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<?php
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
    if(!$this->index_model->isLocalhost()){
        $gaCode = $this->config->item('ga_code');
        $gaCode = isset($gaCode) ? $gaCode : "";

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
                    <ul class="nav navbar-nav navbar-right">
                        <li><a class="pricing-lnk" href="<?php echo site_url(''); ?>">Home</a></li>
                        <li><a class="events-lnk" href="<?php echo site_url('about'); ?>">About</a></li>
                        <li><a class="contacts-lnk" href="<?php echo site_url('contacts'); ?>">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="page-wrapper clearfix">
    
