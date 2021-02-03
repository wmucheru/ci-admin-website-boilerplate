<div class="container">
	<div class="page-header">
		<h1>Contact</h1>
	</div>

	<div class="clearfix">
        <div class="col-sm-5">
            <?php
                echo form_open('contact/message', 'class="form form-horizontal"');

                # Bots will fill this in as usual, so this will be used to validate
                echo form_hidden('other');
            ?>
            <div class="form-group">
                <?php $this->site_model->setFlashdataMessages('contact'); ?>
            </div>

            <div class="form-group">
                <label class="control-label">Name</label>
                <div class="clearfix">
                    <input type="text" name="name" class="form-control" 
                        value="<?php echo set_value('name');  ?>" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">E-mail</label>
                <div class="clearfix">
                    <input type="email" name="email" class="form-control" 
                        value="<?php echo set_value('email');  ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">Message</label>
                <div class="clearfix">
                    <textarea name="message" rows="6" class="form-control" 
                        required><?php echo set_value('message');  ?></textarea>
                </div>
            </div>
            <hr/>

            <div class="form-group">
                <div class="clearfix">
                    <div class="g-recaptcha" data-sitekey="6Le1aq4UAAAAAE7k3R2Hifv9WNgTosqBZuJVkt7T"></div>
                    <br>
                    <button class="btn btn-block btn-lg btn-success">Send Message</button>
                </div>
            </div>
            <?php
                echo form_close();
            ?>
        </div>

        <div class="col-sm-6 col-sm-offset-1">
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
                <iframe frameborder="0"
                    src="https://maps.google.com/maps?q=nairobi&t=&z=15&ie=UTF8&iwloc=&output=embed"></iframe>
                <a href="https://www.emojilib.com"></a>
            </div>
        </div>
    </div>
</div>