<div class="page-header">
    <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/dashboard'); ?>">Home</a></li>
        <li><a href="<?php echo site_url('admin/tools/settings'); ?>">Settings</a></li>
        <li class="active"><?php echo isset($page_title) ? $page_title : ''; ?></li>
    </ol>
</div>

<div class="page-content clearfix">
    <div class="col-sm-6">
        <?php
            # var_dump($setting);

            $id = '';
            $settingName = '';
            $description = '';
            $value = '';
            $tag = '';

            if(isset($setting->id)){
                $id = $setting->id;
                $settingName = $setting->setting;
                $description = $setting->description;
                $value = $setting->value;
                $tag = $setting->tag;
            }

            flash_messages('settings');

            echo form_open('admin/tools/setting/save', 'class="form-horizontal"');

            $editMode = !empty($id);

            if($editMode){
                echo form_hidden('id', $id);
            }
        ?>
        <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Setting</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="setting" required
                    value="<?php echo set_value('setting', $settingName); ?>" <?php echo $editMode ? 'disabled' : '' ?> />
                <?php echo form_error('setting'); ?>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Description</label>
            <div class="col-sm-8">
                <textarea name="description" class="form-control" rows="3" 
                    required><?php echo set_value('description', $description); ?></textarea>
                <?php echo form_error('description'); ?>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Value</label>
            <div class="col-sm-8">
                <input type="text" name="value" class="form-control" 
                    value="<?php echo set_value('value', $value); ?>" required />
                <?php echo form_error('value'); ?>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-4 col-form-label">Tag</label>
            <div class="col-sm-8">
                <?php
                    $tags = array(
                        ''=>'Select tag',
                        'sms'=>'SMS', 
                        'consignment'=>'Consignment'
                    );

                    echo form_dropdown('tag', $tags, $tag, 'class="form-control" required');

                    echo form_error('tag');
                ?>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-sm-offset-4 col-sm-8">
                <hr/>
                <button type="submit" class="btn btn-lg btn-block btn-success">Save</button>
            </div>
        </div>
        <?php 
            echo form_close();
        ?>
    </div>
</div>