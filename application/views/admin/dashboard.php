    <div class="page-header">
        <h1><?php echo isset($page_title) ? $page_title : ''; ?></h1>
        <div class="top-navigation">
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url('admin/dashboard'); ?>">Home</a></li>
                <li class="active"><?php echo isset($page_title) ? $page_title : ''; ?></li>
            </ol>
        </div>
    </div>
    <div class="page-content clearfix">
        <div class="row">
            <div class="col-sm-6">
                <h4 style="margin-top:0;">Recent Events</h4>
                <div class="row">
                <?php
                    # var_dump($events);

                    if(!empty($events) && count($events) > 0){

                        foreach($events as $event){
                ?>
                <div class="clearfix event-img col-sm-4 event-box">
                    <a href="<?php echo site_url('admin/events/view/' . $event->id); ?>" class="clearfix">
                        <img 
                            src="<?php echo base_url('content/uploads/posters/' . $event->poster); ?>"
                            class="img-responsive"
                            alt="" />
                        <h5 class="event-title" title="<?php echo $event->name; ?>"><?php echo $event->name; ?></h5>
                    </a>
                </div>

                <?php
                        } # ENDFOREACH: Loop through events
                    }
                    else{
                        echo '<div class="alert alert-warning">No recent events found</div>';
                    }
                ?>
                </div>
            </div>
            <div class="col-sm-6 event-calendar">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
