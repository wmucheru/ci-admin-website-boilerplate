<div class="clearfix">
    <div class="page-header">
        <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
    </div>

    <div class="row">
        <?php
            
            $name = $user->name;
            $email = $user->email;
            $mobile = $user->mobile;
            $address = $user->address;
            $last_login = $user->last_login;
            $photo = !empty($user->photo_url) ? 'content/uploads/avatars/' . $user->photo_url : 'assets/img/user.png';

            echo form_open_multipart('users/updateProfile');
        ?>
        <div class="col-sm-7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Account Info</h3>
                </div>
                <div class="panel-body">
                    <div class="col-md-7">
                        <?php
                            $this->site_model->setFlashdataMessages('profile');

                            echo form_label('Full Name');
                            form_box('name', $name, '', 'required');

                            echo form_label('Email Address');
                            form_box('email', $email, 'email', 'required');

                            echo form_label('Phone Number');
                            form_box('mobile', $mobile, 'text', 'class="form-control intl-phone" required');

                            echo form_label('Your delivery address');
                            form_box_large('address', $address, 'class="form-control" rows="3" required');

                            /**
                             * 
                             * Show corporate user details if applicable
                             * 
                            */
                            if($this->auth_model->is_member('Corporate')){
                                # var_dump($corp);

                                $corp_name = $corp->name;
                                $address = $corp->address;
                                $telephone = $corp->tel;
                                $contact = $corp->contact;
                        ?>
                        <p><b>Corporate Name:</b> <?php echo $corp_name; ?></p>
                        <p><b>Physical Sddress:</b> <?php echo $address; ?></p>
                        <p><b>Telephone:</b> <?php echo $telephone; ?></p>
                        <p><b>Contact Person:</b> <?php echo $contact; ?></p>
                        <?php
                            }
                        ?>
                    </div>

                    <div class="col-sm-offset-1 col-md-4 hidden">
                        <img src="<?php echo base_url($photo) ?>" class="img-responsive" />
                        <br/>
                        <p>
                            <input type="file" name="ppic" id="ppic" accept=".jpg,.jpeg,.png"/>
                            <small class="text-info"><em>.JPG or .PNG (Max: 1Mb)</em></small>
                        </p>
                    </div>

                    <div class="col-sm-12">
                        <hr/>
                        <?php 
                            echo form_button([
                                'content'=>'Update Details',
                                'type'=>'submit',
                                'class'=>'btn btn-lg btn-block btn-primary'
                            ]); 
                        ?>
                    </div>
                </div>
            </div>    
        </div>
        <?php echo form_close(); ?>

        <div class="col-sm-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Change Password</h3>
                </div>
                <div class="panel-body">
                    <?php
                        $this->site_model->setFlashdataMessages('password');

                        echo form_open_multipart('users/updatePassword');

                        echo form_label('Old Password');
                        form_box('opassword', '', 'password', 'minlength="6"');

                        echo form_label('New Password');
                        form_box('password', '', 'password', 'minlength="6"');

                        echo form_button([
                            'content'=>'Update Password', 
                            'type'=>'submit', 
                            'class'=>'btn btn-lg btn-block btn-primary'
                        ]);

                        echo form_close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>