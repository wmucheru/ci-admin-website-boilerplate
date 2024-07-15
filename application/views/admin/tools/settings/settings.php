<div class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php
                    breadcrumb_link('admin/dashboard', 'Home');
                    breadcrumb_active($page_title);
                ?>
            </ol>
        </nav>
        <h1><?php echo isset($page_title) ? $page_title : '' ?></h1>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-9">
            <div class="page-content">
                <?php
                    flash_messages('setting');

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
                        <td><?php echo "<span class=\"badge bg-primary\">$s->tag</span>" ?></td>
                        <td><?php 
                            if(PERM_IS_ADMIN){
                                echo anchor('admin/tools/settings/'. $s->id, 'View', 
                                    'class="btn btn-xs btn-outline-warning"');
                            }
                        ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
