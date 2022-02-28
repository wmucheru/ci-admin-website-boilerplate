<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="en">
<head>
<?php
    $siteName = $this->config->item('site_name');
    $siteLogo = base_url('assets/img/'. $this->config->item('site_logo'));
    $siteDescription = '';
    $siteKeywords = '';

    $pageTitle = isset($page_title) ? $page_title : '';
    $pageDescription = isset($page_description) ? substr(strip_tags($page_description), 0, 200) : $siteDescription;
    $pageAuthor = isset($page_author) ? $page_author : $siteName;
    $pageKeywords = isset($page_keywords) ? $page_keywords : $siteKeywords;
    $pageImage = isset($page_image) ? $page_image : $siteLogo;

    echo "<title>$pageTitle</title>";

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
        array('name'=>'Content-type', 'content'=>'text/html; charset=utf-8', 'type'=>'equiv'),

        # Page Meta
        array('name'=>'description', 'content'=>$pageDescription),
        array('name'=>'keywords', 'content'=>$pageKeywords),
        array('name'=>'author', 'content'=>$pageAuthor),

        # Twitter
        array('name' => 'twitter:title', 'content' => $pageTitle),
        array('name' => 'twitter:description', 'content' => $pageDescription),
        array('name' => 'twitter:image', 'content' => $pageImage),
        array('name' => 'twitter:card', 'content' => $pageImage)
    );

    echo meta($seoMeta);

    # Facebook Open Graph tags
    og_tag('og:title', $pageTitle);
    og_tag('og:description', $pageDescription);
    og_tag('og:image', $pageImage);
    og_tag('og:url', current_url());

    echo link_tag('favicon.png', 'shortcut icon', 'image/png');

    $styles = array(
        'assets/css/bootstrap.min.css',
        'assets/css/ionicons.min.css',
        'assets/css/style.css?t='.date('His')
    );

    foreach($styles as $s){
        echo link_tag($s);
    }
?>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
        <script src="http://getbootstrap.com/docs-assets/js/html5shiv.js"></script>
        <script src="http://getbootstrap.com/docs-assets/js/respond.min.js"></script>
<![endif]-->
<?php $this->site_model->setGoogleAnalytics() ?>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <div class="clearfix wrapper">
        <nav class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" 
                        data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php nav_brand() ?>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <?php
                            nav_link('', 'Home', 'home-lnk');
                            nav_link('about', 'About', 'about-lnk');
                            nav_link('contact', 'Contact', 'contact-lnk');
                        ?>
                    </ul>
                    <?php
                        $user = user_data();
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
                                <?php 
                                    nav_link('admin/dashboard', 'Dashboard');
                                    nav_link('logout', 'Log Out');
                                ?>
                            </ul>
                        </li>
                    </ul>
                    <?php } ?>
                </div>
            </div>
        </nav>
        <div class="page-wrapper clearfix">
            <?php $this->load->view($page_content) ?>
        </div>
    </div>

    <footer>
        <div class="container clearfix">
            <p>&copy; <?php echo date('Y') .' '. $this->config->item('site_name') ?>.</p>
        </div>
    </footer>
    <?php
        # $this->output->enable_profiler(TRUE);

        $scripts = array(
            'assets/js/jquery.min.js',
            'assets/js/bootstrap.min.js',
            'assets/js/custom.js'
        );

        foreach($scripts as $s){
            echo '<script src="' . base_url($s) . '"></script>';
        }
    ?>
</body>
</html>