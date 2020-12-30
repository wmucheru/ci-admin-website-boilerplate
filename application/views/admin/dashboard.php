<div class="clearfix">
    <div class="page-header">
        <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
        <div class="top-navigation">
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url('admin/dashboard'); ?>">Home</a></li>
                <li class="active"><?php echo isset($page_title) ? $page_title : ''; ?></li>
            </ol>
        </div>
    </div>
    <div class="page-content clearfix">
        <h4 style="margin-top:0;">All Data</h4>
    </div>
</div>