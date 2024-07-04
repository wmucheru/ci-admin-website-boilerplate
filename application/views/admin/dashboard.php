<div class="clearfix">
    <div class="page-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php
                        breadcrumb_link('admin/dashboard', 'Home');
                        breadcrumb_active($page_title);
                    ?>
                </ol>
            </nav>
            <h1><?php echo isset($page_title) ? $page_title : '' ?></h1>
        </div>
    </div>
    <div class="container">
        <div class="row stat-row">
            <?php
                # var_dump($stats);
                stat_box($stats->users, 'Users');
            ?>
        </div>
    </div>
</div>