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
                            }

                            td, th {
                                border: 1px solid #000000;
                                text-align: left;
                                padding: 8px;
                                display: block;
                            }

                            tr:nth-child(even) {
                                background-color: #e6e6e6;
                            }

                            div {
                                margin-top: 5px;
                                margin-bottom: 5px;
                            }
                        </style>
                    </head>
                    <body>
                        <table>
                            <?php
                            foreach($users_data as $user)
                            {
                                ?>
                                <tr>
                                    <td>
                                        <div><?php echo '<b>Name:</b> ' . $user['firstname'] . " " . $user['surname']; ?></div>
                                        <div><?php echo '<b>Username:</b> ' . $user['username'] . ' <b>Password:</b> ' . $user_info[$user['id']]; ?></div>
                                    </td>
                                </tr>
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







