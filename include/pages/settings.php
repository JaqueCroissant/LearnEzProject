<?php

    if ($loginHandler->check_login()) {
        echo '  <div class="text-center">
                    <div class="">
                        <h1>Change password</h1>
                        <form method="POST" action="" name="settings">
                            <div id="changePassword">
                                <input name="oldPassword" type="password" value="" class="form-control login_input" placeholder="Enter old password"><br/>
                                <input name="newPassword1" type="password" value="" class="form-control login_input" placeholder="Enter new password"><br/>
                                <input name="newPassword2" type="password" value="" class="form-control login_input" placeholder="Repeat new password"><br/><br/>
                                <input type="submit" class="form-control login_submit" name="submit" value="Change password" style="margin-top:2px;height: 34px;font-size:14px !important;">
                            </div>

                        </form>
                    </div>
                </div>';
        
        if (isset($_POST['submit'])) {
            if ($userHandler->change_password($_POST['oldPassword'], $_POST['newPassword1'], $_POST['newPassword2'])) {
                echo '<h3 class="text-center">Password changed</h3>';
            } else {
                echo '<h3 class="text-center">Something went wrong</h3>';
            }
        }
    } else {
        header("Location: ?page=login");
    }
?>
