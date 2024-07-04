<div class="container">
	
    <div class="page-header">
        <h1>Sign Up for a Vendor Account</h1>
    </div>

    <?php 
        echo form_open('accounts/new-signup', array('class'=>'col-md-8 form-horizontal'));

        flash_messages('signup');
    ?>
    <div class="row mb-3">
        <div class="col-sm-offset-4 col-sm-8">
            <p class="text-danger">
                Fill in the required fields marked with an asterisk (*)
            </p>
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-4 col-form-label">Email<span class="text-danger">*</span></label>
        <div class="col-sm-8">
            <input type="email" name="email" class="form-control" value="<?php echo set_value('email'); ?>" autofocus required />
            <?php echo form_error('email'); ?>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-4 col-form-label">Name <span class="text-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="fullname" class="form-control" value="<?php echo set_value('fullname'); ?>" required />
            <?php echo form_error('fullname'); ?>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-4 col-form-label">Describe your Company</label>
        <div class="col-sm-8">
            <textarea name="description" class="form-control" rows="4"><?php echo set_value('description'); ?></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-4 col-form-label">Phone Number <span class="text-danger">*</span></label>
        <div class="col-sm-8">
            <input type="text" name="phone" class="form-control" value="<?php echo set_value('phone'); ?>" required />
            <?php echo form_error('phone'); ?>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-4 col-form-label">Physical Address <span class="text-danger">*</span></label>
        <div class="col-sm-8">
            <textarea name="address" class="form-control" rows="4" required><?php echo set_value('address'); ?></textarea>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-4 col-form-label">Password</label>
        <div class="col-sm-8">
            <input type="password" name="password" class="form-control" id="signup-pwd" required />
            <?php echo form_error('password'); ?>
            <label class="checkbox-inline">
                <input type="checkbox" id="password-toggle" /> <em>Hide/Show</em>
            </label>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-offset-4 col-sm-8">
            <p>
                By signing up, you agree to the 
                <?php echo anchor('terms', 'Terms and Conditions'); ?>
            </p>
            <p>
                <button class="btn btn-lg btn-block btn-success">Sign Up</button>
            </p>
        </div>
    </div>

    <?php echo form_close(); ?>
</div>