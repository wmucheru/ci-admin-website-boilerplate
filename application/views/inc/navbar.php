<?php

# session info
$fname = $this->nativesession->get('fname');
$mname = $this->nativesession->get('mname');
$lname = $this->nativesession->get('lname');

?>
<header class="navbar">
	<div class="container-fluid expanded-panel">
		<div class="row">
			<div id="logo" class="col-xs-12 col-sm-2">
				<a href="<?php echo site_url('dashboard'); ?>">Portal</a>
			</div>
			<div id="top-panel" class="col-xs-12 col-sm-10">
				<div class="row">
					<div class="col-xs-8 col-sm-4" style="display:none;">
						<div id="search">
							<input type="text" placeholder="search"/>
							<i class="fa fa-search"></i>
						</div>
					</div>
					<div class="col-xs-4 col-sm-12 top-panel-right">
						<a href="<?php echo site_url('dashboard/about'); ?>" class="about" style="display:none;">about</a>
						<ul class="nav navbar-nav pull-right panel-menu">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle account" data-toggle="dropdown">
									<div class="avatar">
										<img src="<?php echo base_url('assets/img/avatar.png'); ?>" class="img-circle" alt="avatar" />
									</div>
									<i class="fa fa-angle-down pull-right"></i>
									<div class="user-mini pull-right">
										<span class="welcome">Welcome,</span>
										<span><?php echo $fname . ' ' . $lname; ?></span>
									</div>
								</a>
								<ul class="dropdown-menu">
									<li>
										<a href="<?php echo site_url(); ?>">
											<i class="fa fa-user"></i>
											<span>Profile</span>
										</a>
									</li>
									<li>
										<a href="<?php echo site_url('auth/logout'); ?>">
											<i class="fa fa-power-off"></i>
											<span>Logout</span>
										</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
