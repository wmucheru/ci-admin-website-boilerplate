<div class="clearfix">
    <?php 
        echo form_open('auth/login_proc', 'class="form-horizontal login-form"');

        $this->site_model->setFlashdataMessages('login')
    ?>
    <div class="form-group">
        <h4>Log In</h4>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="text" class="form-control" name="email" value="<?php echo set_value('email') ?>" autofocus />
    </div>

    <div class="form-group">
        <label>Password</label>
        <input type="password" class="form-control" name="password" />
    </div>

    <div class="form-group">
        <label class="checkbox-inline">
            <input type="checkbox" name="remember" /> Remember me for 30 days
        </label>
    </div>

    <div class="form-group">
        <button class="btn btn-lg btn-block btn-danger">Log In</button>
    </div>
    <?php echo form_close() ?>
</div>
