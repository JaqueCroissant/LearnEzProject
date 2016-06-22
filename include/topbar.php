<div class="pull-left col-md-3">
    <h1>LearnEZ</h1>
</div>

<div class="pull-right col-xl-3 hidden-xs">
    <?php
        echo '  <div class="col-xl-2 menu_header pull-right">
                    <a href="#" id="notificationLink">
                        <div class="menu_text">
                            <img src="assets/images/ic_notifications_white_24dp/web/ic_notifications_white_24dp_1x.png" class="menu_icon">
                        </div>
                    </a>
                </div>';

        // check if user is logged in or not.
        echo '<div class="col-xl-5 menu_header pull-right">';
        if (true) {
            echo '  <a href="?page=login">
                        <div class="menu_text">
                            <img src="assets/images/ic_account_box_white_24dp/web/ic_account_box_white_24dp_1x.png" class="menu_icon">
                            Login
                        </div>
                    </a>';
        } else {
            echo '  <a href="?page=settings">
                        <div class="menu_text">
                            <img src="assets/images/ic_settings_white_24dp/web/ic_settings_white_24dp_1x.png" class="menu_icon">
                            Settings
                        </div>
                    </a>';
        }
        echo '</div>';
        
        echo '  <div class="col-xl-5 menu_header pull-right">
                    <a href="?page=support">
                        <div class="menu_text">
                            <img src="assets/images/ic_help_white_24dp/web/ic_help_white_24dp_1x.png" class="menu_icon">
                            Support
                        </div>
                    </a>
                </div>';
        
        
    ?>
</div>