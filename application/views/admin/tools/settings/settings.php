<div class="page-header">
    <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/dashboard'); ?>">Home</a></li>
        <li class="active"><?php echo isset($page_title) ? $page_title : ''; ?></li>
    </ol>
</div>

<div class="page-content clearfix">
    <div class="col-sm-9">
        <?php
            if(PERM_IS_ADMIN){
                echo '<div class="action-bar">';
                
                echo anchor('admin/tools/setting/new', '<i class="fa fa-plus"></i> Add Setting', 
                    'class="btn btn-success btn-sm"');

                echo '</div>';
            }

            $this->site_model->setFlashdataMessages('setting');

            if(empty($settings)){
                blank_state('No settings found');
            }
            else{
        ?>
        <table class="table table-bordered table-striped table-condensed dt">
        <thead>
            <tr>
                <th>#</th>
                <th>Setting</th>
                <th>Description</th>
                <th>Value</th>
                <th>Tag</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($settings as $s){
            ?>
            <tr>
                <td><?php echo $s->id ?></td>
                <td><?php echo $s->setting ?></td>
                <td><?php echo $s->description ?></td>
                <td><?php echo !empty($s->value) ? $s->value : '-' ?></td>
                <td><?php echo "<span class=\"label label-primary\">$s->tag</span>"; ?></td>
                <td><?php echo anchor('admin/tools/settings/'. $s->id, 'View', 'class="btn btn-xs btn-warning"'); ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
        </table>
        <?php
            }
        ?>
    </div>
</div>
