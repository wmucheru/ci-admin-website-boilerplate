<div class="clearfix">
	<button class="btn btn-success btn-sm btn-new-group"
		data-toggle="modal" data-target="#agModal">
		<i class="ion-md-add"></i> New Group
	</button>

	<div class="modal" id="agModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="modal-title-group">New Group</h4>
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
