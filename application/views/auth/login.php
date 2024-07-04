<div class="clearfix">
    <?php 
        echo form_open('auth/login_proc', 'class="form-horizontal login-form"');

        flash_messages('login')
    ?>
    <div class="row mb-3">
        <h4>Log In</h4>
    </div>

    <div class="row mb-3">
        <label>Email</label>
        <input type="text" class="form-control" name="email" value="<?php echo set_value('email') ?>" autofocus />
    </div>

    <div class="row mb-3">
        <label>Password</label>
        <input type="password" class="form-control" name="password" />
    </div>

    <div class="row mb-3">
        <button class="btn btn-lg btn-block btn-danger">Log In</button>
    </div>
    <?php echo form_close() ?>
</div>
