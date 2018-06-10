<div class="clearfix">

    <?php echo form_open('auth/login_proc', array('class'=>'form-horizontal col-sm-6')); ?>
    <fieldset>
        <h4 class="col-sm-offset-4 col-sm-12">Log In</h4>
        <br/>
        <br/>

        <div class="form-group">
            <label class="col-sm-4 control-label">Email</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="email" name="email-address"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Password</label>
            <div class="col-sm-8">
                <input type="password" class="form-control" id="authkey" name="authkey"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-12">
                <button class="btn btn-warning">Log in</button>
            </div>
        </div>
    </fieldset>
    <?php echo form_close(); ?>
</div>
