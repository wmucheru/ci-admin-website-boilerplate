<div class="clearfix">
	<button class="btn btn-success btn-sm" data-toggle="modal" data-target="#agModal">
		<i class="fa fa-plus"></i> Add Group
	</button>

	<div class="modal" id="agModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Add Group</h4>
				</div>
				<div class="modal-body clearfix">
					<?php $this->load->view('admin/users/group-form'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
