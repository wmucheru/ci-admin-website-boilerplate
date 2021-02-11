<div class="clearfix">
	<button class="btn btn-success btn-sm" data-toggle="modal" data-target="#auModal">
		<i class="fa fa-plus"></i> Add User
	</button>

	<div class="modal" id="auModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Add User to Organization</h4>
				</div>
				<div class="modal-body clearfix">
					<?php $this->load->view('admin/users/user-form') ; ?>
				</div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
</div>
