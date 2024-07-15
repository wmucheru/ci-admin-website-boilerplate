<div class="clearfix">
    <div class="page-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php
                        breadcrumb_link('admin/dashboard', 'Home');
                        breadcrumb_text('Users');
                        breadcrumb_active($page_title);
                    ?>
                </ol>
            </nav>
            <h1><?php echo isset($page_title) ? $page_title : '' ?></h1>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-9">
                <div class="page-content">
                    <?php
                        flash_messages('users');

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
                                    echo anchor("admin/users/restoreUser/$u->id", 'Restore', ' class="btn btn-warning btn-xs"');
                                ?>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>