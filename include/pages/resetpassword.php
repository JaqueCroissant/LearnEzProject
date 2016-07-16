<?php
require_once 'require.php';

    if(isset($_GET['id']) && isset($_GET['code']) && $loginHandler->validate_reset_password($_GET['id'],$_GET['code']))
    {
        ?>
            
            <div class="text-center">
                <div class="col-xs-12"><?php echo TranslationHandler::get_static_text("RESET_PASS_CONFIRM"); ?></div>
    
                <div class="col-xs-12">
                    <form method="POST" action="" id="reset_pass_submit_code" name="reset_pass_submit_code">
                        <input type="password" id="password" name="password" placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>" class="login_input"><br/>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="<?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?>" class="login_input"><br/>
                        <input type="button" id="submit" name="submit" value="<?php echo TranslationHandler::get_static_text("RESET_PASS_SUBMIT"); ?>" class="login_submit">
                    </form>
                </div>
            </div>
            
        <?php
    }
    else
    {
            ?>
        
            <div class="text-center">
                <div class="col-xs-12"><?php echo TranslationHandler::get_static_text("RESET_PASS_ENTER_MAIL"); ?></div>
                <div class="col-xs-12">
                    <form method="POST" action="" id="reset_pass_submit_email" url="resetpassword.php?step=1" name="reset_pass_submit_email">
                        <input type="text" id="email" name="email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>"><br/>
                        <input type="button" id="submit" name="submit" value="<?php echo TranslationHandler::get_static_text("RESET_PASS_SUBMIT"); ?>" class="reset_pass_submit_email2">
                    </form>
                </div>
            </div>

            <?php
    }

?>