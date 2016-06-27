<?php
    if ($loginHandler->check_login()) {
        //check if first login or first time setup complete.
        if (false) {
            // Show first time set up
        } else {
            // Show regular change settings page. 
            echo '  <div class="text-center">
                        <div class="">
                            <h1>Change settings</h1>
                            <form method="POST" action="" name="settings">
                                <div id="changePassword">
                                    <input name="oldPassword" type="password" value="" class="form-control login_input" placeholder="Enter old password"><br/>
                                    <input name="newPassword1" type="password" value="" class="form-control login_input" placeholder="Enter new password"><br/>
                                    <input name="newPassword2" type="password" value="" class="form-control login_input" placeholder="Repeat new password"><br/><br/>
                                    <input type="button" class="form-control login_submit" name="submit" value="Change password" style="margin-top:2px;height: 34px;font-size:14px !important;">
                                </div>

                            </form>
                        </div>
                    </div>';
        }
    } else {
        header("Location: ?page=login");
    }
?>
