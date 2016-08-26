<!DOCTYPE html>
<?php
session_start();

require_once '../extra/global.function.php';
require_once '../class/orm.class.php';
require_once '../class/user.class.php';
require_once '../handler/handler.php';
require_once '../handler/errorHandler.php';
require_once '../handler/dbHandler.php';
require_once '../handler/sessionKeyHandler.php';
require_once '../handler/rightsHandler.php';
require_once '../handler/translationHandler.php';

try
{
    if(RightsHandler::has_user_right("ACCOUNT_EDIT") && SessionKeyHandler::session_exists("new_passwords"))
    {
        $user_info = SessionKeyHandler::get_from_session("new_passwords", true);
        SessionKeyHandler::remove_from_session("new_passwords");
        if(count($user_info > 0))
        {

                $user_list = array();
                
                foreach($user_info as $key=>$value)
                {
                    $user_list[] = $value;
                }  
                ?>
                <html>
                    <head>
                        <meta charset="UTF-8">
                        <title></title>
                        <style>
                            table {
                                font-family: arial, sans-serif;
                                border-collapse: collapse;
                                margin: 0px auto;
                            }

                            tr:nth-child(4n+3) td.content{
                                background-color: #e6e6e6;
                            }


                        </style>
                    </head>
                    <body>
                        <table>
                            <?php

                            if(count($user_list > 0))
                            {
                                for($i = 0; $i < count($user_list); $i+= 2)
                                {   
                                    
                                    if(count($user_list)-$i > 1)
                                    {
                                    ?>
                                        <tr>
                                            <td class="content" style="padding: 13px;border: 1px solid #000000;">
                                                <div style="margin-bottom: 8px"><?php echo '<b>Name:</b> ' . $user_list[$i]->firstname . " " . $user_list[$i]->surname; ?></div>
                                                <div><span style="padding-right: 20px"><?php echo '<b>Username:</b> ' . $user_list[$i]->username ?></span><?php echo '<b>Password:</b> ' . $user_list[$i]->unhashed_password ?></div>
                                            </td>
                                            <td style="padding: 4px !important;"></td>
                                            <td class="content" style="padding: 13px;border: 1px solid #000000;">
                                                <div style="margin-bottom: 8px"><?php echo '<b>Name:</b> ' . $user_list[$i+1]->firstname . " " . $user_list[$i+1]->surname; ?></div>
                                                <div><span style="padding-right: 20px"><?php echo '<b>Username:</b> ' . $user_list[$i+1]->username ?></span><?php echo '<b>Password:</b> ' . $user_list[$i+1]->unhashed_password ?></div>
                                            </td>
                                        </tr>

                                        <tr><td style="padding: 4px !important;"></td></tr>
                                    <?php
                                    }
                                    else
                                    {
                                    ?>
                                        <tr>
                                            <td class="content" style="padding: 13px;border: 1px solid #000000;">
                                                <div style="margin-bottom: 8px"><?php echo '<b>Name:</b> ' . $user_list[$i]->firstname . " " . $user_list[$i]->surname; ?></div>
                                                <div><span style="padding-right: 20px"><?php echo '<b>Username:</b> ' . $user_list[$i]->username ?></span><?php echo '<b>Password:</b> ' . $user_list[$i]->unhashed_password; ?></div>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                }
                            }
                            ?>
                        </table>
                    </body>
                </html> 
    <?php
        }
    }
    else
    {
    ?>

        <div class="row">
            <div class="col-md-12">
                Direct access not permitted
            </div>
        </div>

    <?php
    }
}
catch(Exception $ex)
{
?>
    <div class="row">
            <div class="col-md-12">
                Something went wrong
            </div>
        </div>
<?php
}
?>







