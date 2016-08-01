<?php
session_start();

require_once '../extra/db.class.php';
require_once '../extra/global.function.php';
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
                $user_ids = array();

                foreach(array_keys($user_info) as $key)
                {
                    $user_ids[] = $key;
                }

                $query = "SELECT id, username, firstname, surname FROM users WHERE id IN (" . generate_in_query($user_ids) . ")";
                $users_data = DbHandler::get_instance()->return_query($query);
                
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

                            for($i = 0; $i < count($users_data); $i++)
                            {

                            }
                            foreach($users_data as $user)
                            {
                                ?>
                                <tr>
                                    <td class="content" style="padding: 13px;border: 1px solid #000000;">
                                        <div style="margin-bottom: 8px"><?php echo '<b>Name:</b> ' . $user['firstname'] . " " . $user['surname']; ?></div>
                                        <div><span style="padding-right: 20px"><?php echo '<b>Username:</b> ' . $user['username'] ?></span><?php echo '<b>Password:</b> ' . $user_info[$user['id']]; ?></div>
                                    </td>
                                    <td style="padding: 4px !important;"></td>
                                    <td class="content" style="padding: 13px;border: 1px solid #000000;">
                                        <div style="margin-bottom: 8px"><?php echo '<b>Name:</b> ' . $user['firstname'] . " " . $user['surname']; ?></div>
                                        <div><span style="padding-right: 20px"><?php echo '<b>Username:</b> ' . $user['username'] ?></span><?php echo '<b>Password:</b> ' . $user_info[$user['id']]; ?></div>
                                    </td>
                                </tr>

                                <tr><td style="padding: 4px !important;"></td></tr>
                                <?php
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







