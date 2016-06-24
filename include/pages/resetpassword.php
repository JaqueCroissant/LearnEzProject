<?php

    if(isset($_GET['val']))
    {
        //GIV MULIGHED FOR AT ÆNDRE PASSWORD
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
                echo "An email has been sent, with a reset link!";
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