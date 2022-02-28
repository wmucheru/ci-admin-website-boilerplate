<div class="clearfix">
    <div class="page-header">
		<h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
		<ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard') ?>">Home</a></li>
            <li><a href="<?php echo site_url('admin/users') ?>">Users</a></li>
			<li class="active"><?php echo isset($page_title) ? $page_title : '' ?></li>
		</ol>
	</div>

    <div class="clearfix page-content">
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

            echo form_open('admin/user/saveUser', 'class="form-horizontal col-sm-8 col-md-6"');

            $this->site_model->setFlashdataMessages('users');

            echo form_hidden('id', $id);

            form_box_label([
                'name'=>'fname',
                'label'=>'Full Name',
                'type'=>'text',
                'value'=>$name,
                'required'=>true
            ]);

            form_box_label([
                'name'=>'email',
                'label'=>'Email Address',
                'type'=>'email',
                'value'=>$email,
                'required'=>true
            ]);

            form_box_label([
                'name'=>'mobile',
                'label'=>'Mobile',
                'type'=>'text',
                'value'=>$mobile,
                'required'=>true
            ]);
        ?>

        <div class="form-group">
            <label class="col-sm-4 control-label">User Group</label>
            <div class="col-sm-8">
                <select class="form-control" name="groupid" required>
                    <option value="">Select a Group</option>
                    <?php
                        foreach($groups as $g){
                            $selected = $g->id == $groupId ? ' selected' : '';
                            echo "<option value=\"$g->id\" $selected>$g->name</option>";
                        }
                    ?>
                </select>
                <?php echo form_error('groupid') ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">Password </label>
            <div class="col-sm-8">
                <input type="password" name="password" class="form-control" 
                    minlength="6" autocomplete="new-password" />
                <?php
                    echo form_error('password');

                    if($editMode){
                        echo '<em class="text-info">Leave password fields blank to retain old password(s)</em>';
                    }
                ?>
            </div>
        </div>
        <?php
            form_box_button('Save');

            echo form_close()
        ?>
    </div>
</div>
