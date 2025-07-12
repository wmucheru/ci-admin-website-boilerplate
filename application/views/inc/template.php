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
    $seoMeta = [

        # Basic Meta
        ['name'=>'viewport', 'content'=>'width=device-width, initial-scale=1'], # For responsive layouts
        ['name'=>'Content-type', 'content'=>'text/html; charset=utf-8', 'type'=>'equiv'],

        # Page Meta
        ['name'=>'description', 'content'=>$pageDescription],
        ['name'=>'keywords', 'content'=>$pageKeywords],
        ['name'=>'author', 'content'=>$pageAuthor],

        # Twitter
        ['name' => 'twitter:title', 'content' => $pageTitle],
        ['name' => 'twitter:description', 'content' => $pageDescription],
        ['name' => 'twitter:image', 'content' => $pageImage],
        ['name' => 'twitter:card', 'content' => $pageImage]
    ];

    echo meta($seoMeta);

    # Facebook Open Graph tags
    og_tag('og:title', $pageTitle);
    og_tag('og:description', $pageDescription);
    og_tag('og:image', $pageImage);
    og_tag('og:url', current_url());

    echo link_tag('favicon.png', 'shortcut icon', 'image/png');

    $styles = [
        'assets/plugins/bootstrap/css/bootstrap.min.css',
        'assets/css/ionicons.min.css',
        'assets/css/style.css?t='.date('His')
    ];

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
<script src="<?php echo base_url('assets/js/jquery.min.js') ?>"></script>
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <div class="clearfix wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <?php nav_brand('/') ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" 
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar navbar-nav me-auto">
                        <?php
                            nav_link('', 'Home', 'home-lnk');
                            nav_link('about', 'About', 'about-lnk');
                            nav_link('contact', 'Contact', 'contact-lnk');
                        ?>
                    </ul>
                    <?php nav_auth() ?>
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
        if(is_localhost()){
            $this->output->enable_profiler(TRUE);
        }

        $scripts = [
            'assets/plugins/bootstrap/js/bootstrap.bundle.min.js',
            'assets/js/custom.js'
        ];

        foreach($scripts as $s){
            echo '<script src="' . base_url($s) . '"></script>';
        }
    ?>
</body>
</html>