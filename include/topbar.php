<div class="pull-left col-md-3 title">
    <img src="assets/images/LearnEZ-Maskot-hvid.png" class="title_icon">
    <div class="headline">Learn<span id="EZ">EZ</span></div>
</div>

<div class="pull-right col-xl-3 hidden-xs" style="height: 100%; margin-right: 0.5em;">
    <?php
        if ($loginHandler->check_login()) {
            echo '  <div class="menu_header pull-right">
                        <a href="?page=login&logout=true">
                            <div class="menu_text">
                                <img src="assets/images/ic_close_white_24dp/web/ic_close_white_24dp_1x.png" class="menu_icon">
                            </div>
                        </a>
                    </div>';
        }
        echo '  <div class="menu_header pull-right">
                    <a href="#" id="notificationLink">
                        <div class="menu_text">
                            <img src="assets/images/ic_notifications_white_24dp/web/ic_notifications_white_24dp_1x.png" class="menu_icon">
                        </div>
                    </a>
                </div>';

        // check if user is logged in or not.
        echo '<div class="menu_header pull-right">';
        if (!$loginHandler->check_login()) {
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
        
        echo '  <div class="menu_header pull-right">
                    <a href="?page=support">
                        <div class="menu_text">
                            <img src="assets/images/ic_help_white_24dp/web/ic_help_white_24dp_1x.png" class="menu_icon">
                            Support
                        </div>
                    </a>
                </div>';
        
        
    ?>
</div>