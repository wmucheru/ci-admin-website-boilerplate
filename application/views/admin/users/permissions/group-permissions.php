<div class="page-header">
    <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
</div>

<div class="page-content">
    <?php
        $hidden = array('gid'=>$gid);
        echo form_open('', '', $hidden);
    ?>
    <table class="table table-bordered table-striped dt">
        <thead>
            <tr>
                <th>Permissions</th>
                <th>Allowed</th>
            </tr>
        </thead>
        <tbody>
            <form>
                <input type="hidden" id="group_id" name="group_id" value="<?php echo $gid; ?>" />
                <?php
                    foreach($perms as $perm){
                        $checked = in_array($perm->id, $group_perms) ? 'checked' : '';
                ?>
                <tr>
                    <td><?php echo $perm->id . '. ' . $perm->name ?></td>
                    <td>
                        <input type="checkbox" id="perm[]" name="perm[]" class="check" 
                            value="<?php echo $perm->id ?>" <?php echo $checked; ?> />
                    </td>
                </tr>
                <?php } ?>
            </form>
        </tbody>
    </table>
    <?php
        echo form_close();
    ?>
</div>
<script>
$(document).ready(function(){
    $('input:checkbox').click( function() {
        var perm = $(this).val()
        var group_id  = $('input[name=gid]').val()

        $.ajax({
            url : siteURL + 'admin/user/perm',
            type: 'post',
            data : {'perm':perm,'group_id':group_id},
            success: function(response) {
                console.log(response);
                // if(response.status == 'success') {
                //     swal({ icon: 'success', text: response.message });
                // }
                // else{
                //     swal({ icon: 'error', text: response.message });
                // }
            }
        });
    })
});
</script>
