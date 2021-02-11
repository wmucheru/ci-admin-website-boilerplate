<div class="clearfix">
	<button class="btn btn-success btn-sm" data-toggle="modal" data-target="#apModal">
		<i class="fa fa-plus"></i> Add Permission
	</button>

	<div class="modal" id="apModal" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Add Permission</h4>
				</div>
				<div class="modal-body clearfix">
					<?php
						$options = array('class'=>'col-sm-12 form-horizontal', 'role'=>'form');
						echo form_open('admin/users/permissions/add', $options);
					?>
					<div class="form-group clearfix">
						<label class="col-sm-4 control-label">Permission name</label>
						<div class="col-sm-8">
							<input type="hidden" class="form-control" id="perm_id"
								value="<?php echo set_value('perm_id'); ?>" name="perm_id"/>
							<input type="hidden" class="form-control" id="permtype" name="permtype"/>
							<input type="text" class="form-control" id="permname"
								value="<?php echo set_value('permname'); ?>" name="permname"  required/>
							<?php echo form_error('permname'); ?>
						</div>
					</div>
					<div class="form-group clearfix">
						<label class="col-sm-4 control-label">Description</label>
						<div class="col-sm-8">
							<textarea class="form-control" id="permdescription" name="permdescription" required></textarea>
						</div>
						<?php echo form_error('permdescription'); ?>
					</div>
					<div class="form-group clearfix">
						<div class="col-sm-offset-4 col-sm-8">
						<button type="submit" class="btn btn-info">Add permission</button>
					</div>
					<?php echo form_close(); ?>
				</div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
</div>
