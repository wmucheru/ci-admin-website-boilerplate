<div class="clearfix">
    <div class="page-header">
        <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard') ?>">Home</a></li>
            <li><a href="<?php echo site_url('admin/users') ?>">Users</a></li>
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

                            if(empty($groups)){
                                blank_state('No groups found');
                            }
                            else{
                        ?>
                        <table class="table table-bordered table-striped dt">
                        <thead>
                            <tr>
                                <th width="50px">Id</th>
                                <th>Name</th>
                                <th>Definition</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($groups as $g){
                            ?>
                            <tr>
                                <td><?php echo $g->id ?></td>
                                <td><?php echo $g->name ?></td>
                                <td><?php echo $g->definition ?></td>
                                <td>
                                    <?php 
                                        if(PERM_IS_ADMIN){
                                    ?>
                                    <button type="button" class="btn-group-update btn btn-xs btn-warning" 
                                        data-id="<?php echo $g->id ?>" data-toggle="modal" data-target="#agModal">
                                        <i class="ion-md-create"></i> Edit
                                    </button>
                                    <?php
                                        echo anchor('admin/users/permissions/group/'. $g->id, 
                                            '<i class="ion-md-lock"></i> Set Permissions', 'class="btn btn-primary btn-xs"');

                                        echo anchor('admin/users/deleteGroup/'. $g->id, 
                                            '<i class="ion-md-close"></i> Delete', 'class="btn btn-danger btn-xs btn-delete"');
                                        }
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

            <div class="col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Permissions</h3>
                    </div>
                    <div class="panel-body">
                        <?php 
                            if(empty($perms)){
                                blank_state('No permissions found');
                            }
                            else{
                        ?>
                        <table class="table table-striped table-bordered dt">
                        <thead>
                            <tr>
                                <th width="50px">Id</th>
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
                                <td><?php echo $p->id ?></td>
                                <td><?php echo $p->name ?></td>
                                <td><?php echo $p->definition ?></td>
                                <td>-</td>
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
</div>
<script>
$(document).ready(function(){
    const groups = JSON.parse(`<?php echo json_encode($groups) ?>`);
    const groupModalTitle = $('#modal-title-group');
    const getGroup = (id) => {
        const g = groups.filter(g => g.id == id);
        return g.length > 0 ? g[0] : {};
    }

    $('.btn-new-group').click(() => groupModalTitle.html('New Group'));

    $('.btn-group-update').click(function(){
        const gId = $(this).data('id');
        const g = getGroup(gId);
        console.log(g);

        $('input[name=id]').val(g.id);
        $('input[name=name]').val(g.name);
        $('textarea[name=definition]').val(g.definition);
	});
});
</script>
