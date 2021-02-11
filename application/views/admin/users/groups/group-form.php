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
					<?php
						$options = array('class'=>'col-md-12 form-horizontal', 'role'=>'form');

						echo form_open('admin/users/groups/create', $options);
					?>

					<div class="form-group">
						<label for="group-name" class="col-sm-4 control-label">Group</label>
						<div class="col-sm-8">
                        <input type="hidden" class="form-control" id="group_id"
                            value="<?php echo set_value('group_id'); ?>" name="group_id"/>
                        <input type="hidden" class="form-control" id="type"
                            value="<?php echo set_value('type'); ?>" name="type"/>
						  <input type="text" class="form-control" id="group_name"
						  	value="<?php echo set_value('group_name'); ?>" name="group_name">
						  <?php echo form_error('group_name'); ?>
						</div>
					</div>
					<div class="form-group">
						<label for="group-definition" class="col-sm-4 control-label">Group Definition</label>
						<div class="col-sm-8">
						  <textarea name="group_definition" id="group_definition" col="40" rows="4"
						  	class="form-control"><?php echo set_value('group_definition'); ?></textarea>
						  <?php echo form_error('group_definition'); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
						  <button type="submit" class="btn btn-info">Add group</button>
						</div>
					</div>
					<?php echo form_close(); ?>
				</div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
</div>
