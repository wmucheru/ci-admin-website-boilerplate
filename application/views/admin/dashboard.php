<div class="clearfix">
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
        <div class="row stat-row">
            <?php
                # var_dump($stats);

                if(is_default_user()){
                    stat_box_link($stats->words, 'My Words', 'admin/account/words');
                    stat_box_link($stats->phrases, 'My Phrases', 'admin/account/phrases');
                    stat_box_link($stats->quizzes, 'My Quizzes', 'admin/account/quizzes');
                }

                if(PERM_USER_MANAGEMENT){
                    stat_box_link($stats->users, 'Users', 'admin/users');
                }
            ?>
        </div>
    </div>
</div>