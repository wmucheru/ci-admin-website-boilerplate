<div class="clearfix">
    <div class="page-header">
		<h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
		<ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard') ?>">Home</a></li>
			<li class="active"><?php echo isset($page_title) ? $page_title : '' ?></li>
		</ol>
	</div>

    <div class="page-content">
		<div class="action-bar">
			<?php echo anchor('admin/users/new', '<i class="fa fa-plus"></i> Add User', 'class="btn btn-success btn-sm"') ?>
		</div>

		<div class="col-md-8" style="padding-left:0;">
			<?php
				$this->site_model->setFlashdataMessages('users');

				if(!empty($users)){
                    # var_dump($users);
			?>
			<table class="table table-bordered table-responsive dt">
				<thead>
				<tr>
					<th>Id</th>
					<th>Name</th>
					<th>Email</th>
					<th>Telephone</th>
					<th>User Type</th>
					<th>Status</th>
					<th width="200px">Actions</th>
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
                                echo anchor("admin/users/edit/$u->id", 'Edit User', 'class="btn btn-xs btn-warning"');

								if($u->banned == '0'){
                                    echo anchor("admin/user/suspend/$u->id", 'Suspend', 'class="btn btn-xs btn-danger"');
								}
							}
						?>
					</td>
				</tr>
				<?php } ?>
				</tbody>
			</table>
		<?php
			}
			else{
				echo '<div class="alert alert-info">No users added</div>';
			}
		?>
		</div>
	</div>
</div>