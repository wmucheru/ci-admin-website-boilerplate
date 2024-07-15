
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

    echo '<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400,700&display=swap" rel="stylesheet">';

    $styles = [
        'assets/plugins/bootstrap/css/bootstrap.min.css',
        'assets/css/ionicons.min.css',
        'assets/plugins/datatables/datatables.min.css',
        'assets/css/style.css?t='.date('His'),
        'assets/css/admin.css?t='.date('His')
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
<script src="<?php echo base_url('assets/js/jquery.min.js') ?>"></script>
</head>
<body class="admin-bd <?php echo isset($body_class) ? $body_class : '' ?>">
    <div class="clearfix wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <?php nav_brand('admin/dashboard') ?>
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
                            $mainMenu = [
                                [
                                    'url'=>'/', 
                                    'title'=>'<i class="ion-md-home"></i> Home',
                                    'perm'=>true
                                ],
                                [
                                    'url'=>'admin/dashboard', 
                                    'title'=>'Dashboard', 
                                    'lclass'=>'dash-lnk', 
                                    'perm'=>true
                                ],
                                [
                                    'url'=>'',
                                    'title'=>'Users',
                                    'lclass'=>'user-lnk',
                                    'sublinks'=>[
                                        ['url'=>'admin/users', 'title'=>'Users'],
                                        ['url'=>'admin/users/permissions', 'title'=>'Groups & Permissions'],
                                        ['divider'=>''],
                                        ['url'=>'admin/users/suspended', 'title'=>'Suspended Accounts']
                                    ],
                                    'perm'=>PERM_USER_MANAGEMENT
                                ],
                                [
                                    'url'=>'',
                                    'title'=>'Tools',
                                    'lclass'=>'tools-lnk',
                                    'sublinks'=>[
                                        ['url'=>'admin/tools/settings', 'title'=>'Settings']
                                    ],
                                    'perm'=>PERM_IS_ADMIN
                                ]
                            ];

                            nav_menu($mainMenu);
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
            <p>&copy; <?php echo date('Y') .'. ' . $this->config->item('site_name') ?>.</p>
        </div>
    </footer>
    <?php
        echo '<script>const siteURL = "'. site_url() .'"</script>';

        if(is_localhost()){
            $this->output->enable_profiler(TRUE);
        }

        $scripts = array(
            'assets/plugins/bootstrap/js/bootstrap.min.js',

            # Datatables
            'assets/plugins/datatables/datatables.min.js',
            'assets/js/custom.js?t='. date('Hi')
        );

        foreach($scripts as $s){
            echo '<script src="' . base_url($s) . '"></script>';
        }
    ?>
</body>
</html>