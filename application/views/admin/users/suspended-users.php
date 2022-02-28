<div class="clearfix">
    <div class="page-header">
        <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
        <div class="top-navigation">
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url('admin/dashboard') ?>">Home</a></li>
                <li><a href="<?php echo site_url('admin/users') ?>">Users</a></li>
                <li class="active"><?php echo isset($page_title) ? $page_title : '' ?></li>
            </ol>
        </div>
    </div>

    <div class="page-content clearfix">
        <?php
            $this->site_model->setFlashdataMessages('users');

            if(empty($suspended)){
                blank_state('No suspended users');
            }
            else{
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
                foreach($suspended as $u){
            ?>
            <tr>
                <td><?php echo $u->id ?></td>
                <td><?php echo $u->name ?></td>
                <td><?php echo $u->email ?></td>
                <td><?php echo $u->mobile ?></td>
                <td style="width:10em;">
                    <?php
                        echo anchor("admin/users/revoke_suspension/$u->id", 'Restore', ' class="btn btn-warning btn-xs"');
                    ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php
            }
        ?>
    </div>
</div>