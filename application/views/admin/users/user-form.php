<div class="container">
    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                    breadcrumb_home();
                    breadcrumb_link('admin/users', 'Users');
                    breadcrumb_active($page_title);
                ?>
            </ol>
        </nav>
        <h1><?php echo isset($page_title) ? $page_title : '' ?></h1>
	</div>

    <div class="page-content">
        <?php
            # var_dump($user);

            $id = isset($user->id) ? $user->id : '';
            $name = isset($user->name) ? $user->name : '';
            $email = isset($user->email) ? $user->email : '';
            $mobile = isset($user->mobile) ? $user->mobile : '';
            $groupId =  isset($user->group_id) ? $user->group_id : '';

            $isEditMode = !empty($id);

            echo form_open('admin/users/saveUser', 
                'class="form-horizontal container-xs m-0"');

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
                'type'=>'phone',
                'name'=>'mobile',
                'label'=>'Mobile',
                'value'=>$mobile,
                'required'=>true
            ]);

            form_box_label([
                'type'=>'select',
                'name'=>'groupid',
                'label'=>'User Group',
                'options'=>$groups,
                'optLabelKey'=>'name',
                'optValueKey'=>'id',
                'value'=>$groupId,
                'required'=>true
            ]);

            form_box_label([
                'type'=>'password',
                'name'=>'password',
                'label'=>'Password',
                'attrs'=>'minlength="6" autocomplete="new-password"',
                'hint'=>$isEditMode ? 
                    'Leave password fields blank to retain old password(s)' : '',
                'required'=>!$isEditMode
            ]);

            form_box_button('Save');

            echo form_close();
        ?>
    </div>
</div>
