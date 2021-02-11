<div class="clearfix">
    <div class="page-header">
        <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard'); ?>">Home</a></li>
            <li><a href="<?php echo site_url('admin/users'); ?>">Users</a></li>
            <li class="active">Group Permissions</li>
        </ol>
    </div>

    <div class="clearfix">
        <?php
            $this->site_model->setFlashdataMessages('group');
            $this->site_model->setFlashdataMessages('perm');
        ?>

        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Groups</h3>
                    </div>
                    <div class="panel-body">
                        <?php 
                            if(PERM_IS_ADMIN){
                                $this->load->view('admin/users/groups/group-form');
                                echo '<hr/>';
                            }

                            if(!empty($groups)){
                        ?>
                        <table class="table table-bordered table-striped dt">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Definition</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($groups as $group){
                            ?>
                            <tr>
                                <td><?php echo $group->id; ?></td>
                                <td><?php echo $group->name; ?></td>
                                <td><?php echo $group->definition; ?></td>
                                <td>
                                    <?php 
                                        if(PERM_IS_ADMIN){
                                    ?>
                                    <button type="button"
                                        class="btn-updateGroup btn btn-xs btn-warning" data-id="editperm" date-fmode="edit"
                                        data-group_id="<?php echo $group->id; ?>"
                                        data-group_name="<?php echo $group->name; ?>"
                                        data-def="<?php echo $group->definition; ?>"
                                        data-toggle="modal" data-target="#agModal">
                                        <i class="fa fa-pencil"></i> Edit
                                    </button>
        
                                    <?php 
                                        }
        
                                        if($group->id != 16){
                                    ?>
                                    <a href="<?php echo site_url('admin/users/permissions/group/' . $group->id); ?>"
                                        class="btn btn-success btn-xs" data-toggle-="modal" data-target-="#pmModal"
                                        data-grpid="<?php echo $group->id; ?>">
                                        <i class="fa fa-lock"></i> Set Permissions
                                    </a>
                                    <?php
                                        }
        
                                        if(PERM_IS_ADMIN){
                                    ?>
                                    <a href="<?php echo site_url('admin/users/delete_group/' . $group->id); ?>" class="btn btn-danger btn-xs del"
                                        data-resource="group">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                        <?php
                            }
                            else{
                                echo '<div class="alert alert-info">No groups found</div>';
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Permissions</h3>
                    </div>
                    <div class="panel-body">
                        <?php 
                            if(!empty($perms)){
                        ?>
                        <table class="table table-striped table-bordered dt">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Definition</th>
                                <?php 
                                    if(PERM_IS_ADMIN){
                                        echo '<th>Actions</th>';
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($perms as $p){
                            ?>
                            <tr>
                                <td><?php echo $p->id; ?></td>
                                <td><?php echo $p->name; ?></td>
                                <td><?php echo $p->definition; ?></td>
                                <td>-</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                        <?php
                            }
                            else{
                                echo '<div class="alert alert-info">No permissions found</div>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
	$('.btn-updatePerm').click(function(){
        var data = $(this).data()

        $("#permtype").val("update");
        $("#perm_id").val(data.permid);
        $("#permname").val(data.perm);
        $("#permdescription").val(data.def);
	})

    $('.btn-updateGroup').click(function(){
        var data = $(this).data()

        $("#type").val("update");
        $("#group_id").val(data.group_id);
        $("#group_name").val(data.group_name);
        $("#group_definition").val(data.def);
	})
});
</script>
