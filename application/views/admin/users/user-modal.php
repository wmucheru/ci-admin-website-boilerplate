<div class="clearfix">
	<button class="btn btn-success" data-toggle="modal" data-target="#advModal">
		<i class="fa fa-plus"></i> ADD USERS
	</button>

	<div class="modal" id="advModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				</div>
				<div class="modal-body clearfix">
					<?php $this->load->view('/admin/users/user-form'); ?>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>
</div>