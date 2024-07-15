<div class="clearfix">
	<div class="page-header">
		<div class="jumbotron">
            <div class="container">
                <h1 class="display-1">Contact</h1>
            </div>
        </div>
	</div>

	<div class="container">
        <div class="row">
            <div class="col-md-5">
                <?php
                    echo form_open('contact/message');

                    # Bots will fill this in as usual, so this will be used to validate
                    echo form_hidden('other');
                ?>
                <div class="mb-3">
                    <?php flash_messages('contact'); ?>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" name="name" class="form-control" required autofocus
                        value="<?php echo set_value('name')  ?>" />
                    <label>Name</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" required
                        value="<?php echo set_value('email')  ?>" />
                    <label>E-mail</label>
                </div>

                <div class="form-floating mb-3">
                    <textarea name="message" class="form-control" style="height:12em;" 
                        required><?php echo set_value('message') ?></textarea>
                    <label>Message</label>
                </div>

                <div class="form-floating mb-3 mt-4">
                    <div class="clearfix">
                        <div class="g-recaptcha" data-sitekey="<?php echo config_item('recaptcha_key') ?>"></div>
                        <button class="btn btn-block btn-lg btn-danger">
                            <i class="ion-md-mail"></i> Send Message
                        </button>
                    </div>
                </div>
                <?php echo form_close() ?>
            </div>

            <div class="offset-sm-1 col-sm-6">
                <p>
                    For more information or any queries that you may have:
                </p>
                <ul class="infolist">
                    <li>Email us: <a href="mailto:abc@xyz.site">abc@xyz.site</a></li>
                    <li>Call us: <a href="tel:+000 000 000">+000 000 000</a></li>
                </ul>
                
                <hr/>

                <style>
                    iframe{
                        background:#eeee;
                        border-collapse:collapse;
                        height:400px;
                        width:100%;
                        margin:0;
                    }
                    .mapouter {
                        position:relative;
                        text-align:right;
                    }
                </style>
                <div class="mapouter">
                    <iframe frameborder="0" src="<?php echo config_item('map_embed_url') ?>"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>