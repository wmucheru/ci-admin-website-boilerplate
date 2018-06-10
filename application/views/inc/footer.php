	</div>
</div>

<footer>
    <div class="container clearfix">
        <p>&copy; <?php echo date('Y'); ?> My Site. All Rights Reserved.</p>
    </div>
</footer>
<?php
    # $this->output->enable_profiler(TRUE);
    
	$scripts = array(
        'assets/js/jquery.min.js',
        'assets/js/bootstrap.min.js',
        'assets/plugins/bxslider/jquery.bxslider.min.js',
		'assets/js/custom.js'
	);
    
    foreach($scripts as $script){
        echo '<script src="' . base_url($script) . '"></script>';
    }
?>
</body>
</html>
