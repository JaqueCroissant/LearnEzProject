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
                    </head>
                    <body>
                        <table>
                            <?php
                            
                            foreach($users_data as $user)
                            {
                                echo '<tr>';
                                echo '<td>Name: ' . $user['firstname'] . " " . $user['surname'] .'</td>';
                                echo '</tr>';
                                
                                echo '<tr>';
                                echo '<td>Username: ' . $user['username'] . '</td>';
                                echo '<td> Password: ' . $user_info[$user['id']] . '</td>';
                                echo '</tr>';
                                
                                echo '<tr><td> <td></tr>';
                                echo '<tr><td> <td></tr>';
                                echo '<tr><td> <td></tr>';
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
                asssssssssDirect access not permitted
            </div>
        </div>

    <?php
    }
}
catch(Exception $ex)
{
    
}
?>







