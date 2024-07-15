<div class="clearfix">
	<button type="button" class="btn btn-success btn-sm btn-new-group"
		data-bs-toggle="modal" data-bs-target="#agModal">
		<i class="ion-md-add"></i> New Group
	</button>

	<div class="modal" id="agModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal-title-group">New Group</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body clearfix">
					<?php
						echo form_open('admin/users/saveGroup', 'class="form-horizontal"');

						echo "<input type=\"hidden\" name=\"id\" value=\"". set_value('id') ."\" />";

						form_box_label([
							'name'=>'name',
							'label'=>'Group Name',
							'type'=>'text',
							'required'=>true
						]);

						form_box_label([
							'name'=>'definition',
							'label'=>'Definition',
							'type'=>'textarea',
							'required'=>true,
							'attrs'=>'rows="4"'
						]);

						form_box_button('Save');

						echo form_close()
					?>
				</div>
			</div>
		</div>
	</div>
</div>
