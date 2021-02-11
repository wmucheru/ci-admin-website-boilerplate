<div class="clearfix">
    <div class="page-header">
        <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
    </div>

    <div class="page-content clearfix">
        <div class="col-sm-6">
            <?php $this->load->view('admin/users/user-form'); ?>
        </div>
    </div>
</div>