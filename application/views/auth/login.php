<div class="container">
    <div class="row login-box">
        <div class="col-sm-7 login-banner">
            <h4>My Site Banner</h4>
        </div>
        <div class="col-sm-5 login-form">
            <?php 
                echo form_open('auth/login_proc', array('class'=>'form-horizontal'));

                $this->site_model->setFlashdataMessages('login');
            ?>
            <fieldset>
                <h4>Log In</h4>
                <hr/>

                <div class="form-group">
                    <label class="col-sm-12">Email</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" name="email" value="<?php echo set_value('email'); ?>" autofocus />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-12">Password</label>
                    <div class="col-sm-12">
                        <input type="password" class="form-control" name="password"/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">
                        <button class="btn btn-block btn-danger">Log In</button>
                    </div>
                </div>
            </fieldset>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
