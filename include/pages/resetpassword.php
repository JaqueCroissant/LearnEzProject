<?php
    echo "lol2";
    if(isset($_GET['id']) && isset($_GET['code']))
    {
        if(!$loginHandler->validate_reset_password($_GET['id'],$_GET['code']))
        {
            echo $loginHandler->error->title;
        }
        elseif(isset($_POST['submit'])) {
            
            if(!$userHandler->change_password($_GET['id'],$_GET['code'],$_POST['password'],$_POST['password_confirm']))
            {
                echo $userHandler->error->title;
            }
            else
            {
                echo "Your password has been reset!";
            }
        }
        else
        {
            ?>
            
            <div class="text-center">
                <div class="col-xs-12"><h2>Enter and confirm your new password below!</h2></div>
    
                <div class="col-xs-12">
                    <form method="POST" action="" id="" name="login">
                        <input type="password" id="password" name="password" placeholder="Password" class="login_input"><br/>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirm" class="login_input"><br/>
                        <input type="submit" id="submit" name="submit" value="Reset my password" class="login_submit">
                    </form>
                </div>
            </div>
            
            <?php
        }
    }
    else
    {
        if(isset($_POST['submit']))
        {
            if(!$loginHandler->reset_password($_POST['email'])) 
            {
                echo $loginHandler->error->title;
            } 
            else 
            {
                $temp = DbHandler::get_instance()->return_query("SELECT * FROM users WHERE email = :email", $_POST['email']);
                echo "Temporary (will be sent to email): <br />"
                . "   Click this link to confirm your password reset: <a href='index.php?page=resetpassword&id=". reset($temp)["id"]."&code=". reset($temp)["validation_code"]."'>Click here</a>";
            }
        }
        else
        {
            ?>
        
            <div class="text-center">
                <div class="col-xs-12"><h2>Enter your email below, to reset your password!</h2></div>
    
                <div class="col-xs-12">
                    <form method="POST" action="" id="" name="login">
                        <input type="text" id="email" name="email" placeholder="Email" class="login_input"><br/>
                        <input type="submit" id="submit" name="submit" value="Reset my password" class="login_submit">
                    </form>
                </div>
            </div>

            <?php
        }
    }

?>