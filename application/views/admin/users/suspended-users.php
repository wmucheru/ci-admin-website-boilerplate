<div class="clearfix">
    <div class="page-header">
        <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
        <div class="top-navigation">
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url('admin/dashboard'); ?>">Home</a></li>
                <li><a href="<?php echo site_url('admin/users'); ?>">Users</a></li>
                <li class="active"><?php echo isset($page_title) ? $page_title : ''; ?></li>
            </ol>
        </div>
    </div>

    <div class="page-content clearfix">
        <?php
            $this->site_model->setFlashdataMessages('users');

            if(!empty($suspended_users)){
        ?>
        <table id="example" class="table table-bordered table-striped dt">
            <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php 
                foreach($suspended_users as $u){
            ?>
            <tr>
                <td><?php echo $u->id; ?></td>
                <td><?php echo $u->name; ?></td>
                <td><?php echo $u->email; ?></td>
                <td><?php echo $u->mobile; ?></td>
                <td style="width:10em;">
                    <a href="<?php echo site_url('admin/users/revoke_suspension/' . $u->id); ?>" class="btn btn-warning btn-xs">
                        <i class="fa fa-times"></i> Revoke Suspension
                    </a>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php
            }
            else{
                echo '<div class="alert alert-info">No suspended users</div>';
            }
        ?>
    </div>
</div>