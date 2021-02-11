<div class="clearfix">
    <?php
        # var_dump($user);

        $id = '';
        $name = '';
        $email = '';
        $mobile = '';
        $groupId = '';

        if(!empty($user->id)){
            $id = $user->id;
            $name = $user->name;
            $email = $user->email;
            $mobile = $user->mobile;
            $groupId = $user->group_id;
        }

        $editMode = !empty($id);

        echo form_open('admin/user/save', 'class="form-horizontal"');

        $this->site_model->setFlashdataMessages('users');

        echo form_hidden('id', $id);
    ?>
    <div class="form-group">
        <label class="col-sm-4 control-label">Full Name</label>
        <div class="col-sm-8">
            <input type="text" name="fname" class="form-control" required
                value="<?php echo set_value('fname', $name); ?>">
            <?php echo form_error('fname'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">Email Address</label>
        <div class="col-sm-8">
            <input type="email" name="email" class="form-control" required
                value="<?php echo set_value('email', $email); ?>">
            <?php echo form_error('email'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">Mobile</label>
        <div class="col-sm-8">
            <input type="text" name="mobile" class="form-control" required
                value="<?php echo set_value('mobile', $mobile); ?>">
            <?php echo form_error('mobile'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">User Group</label>
        <div class="col-sm-8">
            <select class="form-control" name="group" required>
                <option value="">Select a Group</option>
                <?php
                    $defaultGroup = USER_GROUP_EDITOR;

                    foreach($groups as $g){

                        if(empty($id)){
                            $selected = $g->id == $defaultGroup ? ' selected' : '';
                        }
                        else{
                            $selected = $g->id == $groupId ? ' selected' : '';
                        }

                        echo "<option value=\"$g->id\" $selected>$g->name</option>";
                    }
                ?>
            </select>
            <?php echo form_error('group'); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">Password </label>
        <div class="col-sm-8">
            <input type="password" name="pwd" class="form-control" 
                minlength="6" autocomplete="off" />
            <?php
                echo form_error('pwd');

                if(isset($edit_mode) && $edit_mode == true){
                    echo '<div class="text-info">Leave password fields blank to retain old password(s)</div>';
                }
            ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">Confirm Password</label>
        <div class="col-sm-8">
            <input type="password" class="form-control" id="con-passwd" 
                name="cpwd" autocomplete="off" />
            <?php echo form_error('cpwd'); ?>
        </div>
    </div>
    <hr/>

    <div class="col-sm-offset-4 col-sm-8">
        <input type="submit" class="btn btn-primary" value="Submit" />
        <input type="reset" class="btn btn-default" value="Reset" />
    </div>

    <?php echo form_close(); ?>
</div>
