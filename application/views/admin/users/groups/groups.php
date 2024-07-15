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
        <?php
            flash_messages('group');
            flash_messages('perm');
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        Groups
                        <div style="float:right;">
                            <?php
                                if(PERM_IS_ADMIN){
                                    $this->load->view('admin/users/groups/group-form');
                                }
                            ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php 
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
                                    <button type="button" class="btn btn-xs btn-outline-warning btn-group-update" 
                                        data-id="<?php echo $g->id ?>" data-bs-toggle="modal" data-bs-target="#agModal">
                                        <i class="ion-md-create"></i> Edit
                                    </button>
                                    <?php
                                        echo anchor('admin/users/permissions/group/'. $g->id, 
                                            '<i class="ion-md-lock"></i> Set Permissions', 
                                            'class="btn btn-outline-primary btn-xs"');

                                        echo anchor('admin/users/deleteGroup/'. $g->id, 
                                            '<i class="ion-md-close"></i> Delete', 
                                            'class="btn btn-outline-danger btn-xs btn-delete"');
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
                <div class="card">
                    <div class="card-header">Permissions</div>
                    <div class="card-body">
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

        $('input[name=id]').val(g.id);
        $('input[name=name]').val(g.name);
        $('textarea[name=definition]').val(g.definition);
	});
});
</script>
