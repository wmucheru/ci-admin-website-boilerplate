<div class="clearfix">
    <div class="page-header">
        <div class="container">
			<nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php
                        breadcrumb_link('admin/dashboard', 'Home');
                        breadcrumb_link('admin/users', 'Users');
                        breadcrumb_active($page_title);
                    ?>
                </ol>
            </nav>
            <h1><?php echo isset($page_title) ? $page_title : '' ?></h1>
		</div>
	</div>

    <div class="container page-content">
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

            echo form_open('admin/users/saveUser', 'class="form-horizontal col-md-6"');

            flash_messages('users');

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
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label">User Group</label>
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

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Password </label>
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
