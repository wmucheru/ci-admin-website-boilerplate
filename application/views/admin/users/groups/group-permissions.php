<div class="page-header">
    <h1><?php echo isset($page_title) ? $page_title : '' ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/dashboard') ?>">Home</a></li>
        <li><a href="<?php echo site_url('admin/users/permissions') ?>">Groups & Permissions</a></li>
        <li class="active"><?php echo isset($page_title) ? $page_title : '' ?></li>
    </ol>
</div>

<div class="clearfix page-content">
    <div class="row">
        <div class="col-sm-9">
            <table class="table table-bordered table-striped dt">
                <thead>
                    <tr>
                        <th>Permissions</th>
                        <th>Allowed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($perms as $p){
                            $checked = in_array($p->id, $groupPerms) ? 'checked' : '';
                    ?>
                    <tr>
                        <td><?php echo $p->id . '. ' . $p->name ?></td>
                        <td>
                            <?php echo form_checkbox('perm[]', $p->id, $checked) ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <input type="hidden" name="gid" value="<?php echo $group->id ?>" />
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $('input:checkbox').click(function(){
        const pId = $(this).val();
        const gId = $('input[name=gid]').val();
        const active = $(this).is(':checked');

        $.ajax({
            url : siteURL + 'admin/users/setPerms',
            type: 'post',
            data : {'pid': pId, 'gid': gId, 'active': active},
            success: function(response) {
                // console.log(response);
            }
        });
    })
});
</script>
