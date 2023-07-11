<div class="clearfix">
    <div class="page-header">
		<h1><?php echo isset($page_title) ? $page_title : '' ?></h1>
		<ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard') ?>">Home</a></li>
			<li class="active"><?php echo isset($page_title) ? $page_title : '' ?></li>
		</ol>
	</div>

    <div class="col-sm-9 page-content">
		<div class="action-bar">
			<?php echo anchor('admin/users/new', '<i class="ion-md-add"></i> New User', 'class="btn btn-success btn-sm"') ?>
		</div>
		<?php
			$this->site_model->setFlashdataMessages('users');

			if(empty($users)){
				blank_state('No users added');
			}
			else{
				# var_dump($users);
		?>
		<table class="table table-bordered table-responsive dt">
			<thead>
			<tr>
				<th width="50px">Id</th>
				<th>Name</th>
				<th>Email</th>
				<th>Telephone</th>
				<th>User Type</th>
				<th>Status</th>
				<th width="140px">Actions</th>
			</tr>
			</thead>
			<tbody>
			<?php 
				foreach($users as $u){
					$status = $u->banned == '0' ? 
						'<span class="label label-success">ACTIVE</span>' :
						'<span class="label label-warning">SUSPENDED</span>'
			?>
			<tr>
				<td><?php echo $u->id ?></td>
				<td><?php echo $u->name ?></td>
				<td><?php echo $u->email ?></td>
				<td><?php echo $u->mobile ?></td>
				<td><?php echo $u->group ?></td>
				<td><?php echo $status ?></td>
				<td>
					<?php 
						if(PERM_USER_MANAGEMENT){
							echo anchor("admin/users/edit/$u->id", '<i class="ion-md-create"></i> Edit', 
								'class="btn btn-xs btn-warning"');

							if($u->banned == '0'){
								echo anchor("admin/users/suspendUser/$u->id", '<i class="ion-md-alert"></i> Suspend', 
									'class="btn btn-xs btn-danger"');
							}
							else{
								echo anchor("admin/users/restoreUser/$u->id", '<i class="ion-md-alert"></i> Reinstate', 
									'class="btn btn-xs btn-primary"');
							}
						}
					?>
				</td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php } ?>
	</div>
</div>