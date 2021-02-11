<div class="page-header">
    <h1>
    </h1>
    <ol class="breadcrumb">
                <li><a href="<?php echo site_url('admin/dashboard'); ?>">Home</a></li>
        <li><a href="<?php echo site_url('admin/consignments/consignees'); ?>">Consignees</a></li>
        <li class="active"><?php echo isset($page_title) ? $page_title : ''; ?></li>
    </ol>
</div>

<div class="content clearfix">
    <div class="col-md-8">
        <div class="box">
            <div class="box-body table-responsive">
                <?php

                    $data['group_details'] = $group_details;
                    $this->load->view('admin/users/add-group-form', $data);
                ?>
            </div>
        </div>
    </div>
</div>