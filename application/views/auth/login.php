<div class="clearfix" style="padding-top:5%;">
    <?php 
        echo form_open('auth/login_proc', 'class="login-form"');

        flash_messages('login')
    ?>
    <div class="mb-3">
        <h1 class="display-6 text-center">Log In</h1>
    </div>

    <div class="form-floating mb-3">
        <input type="text" class="form-control" name="email" autofocus required
            value="<?php echo set_value('email') ?>" />
        <label>Email</label>
    </div>

    <div class="form-floating mb-3">
        <input type="password" class="form-control" name="password" required minlength="6" />
        <label>Password</label>
    </div>

    <div class="d-grid mb-3 mt-4">
        <button class="btn btn-lg btn-block btn-danger">Log In</button>
    </div>
    <?php echo form_close() ?>
</div>
