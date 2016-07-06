<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/translationHandler.php';
    
    $userHandler = SessionKeyHandler::get_from_session("user_handler", true);
    $translationHandler = SessionKeyHandler::get_from_session("translation_handler", true);
    

    if($userHandler->_user->user_type_id<3)
    {
        //GENEREL PÅKRÆVET INFO:
        ?>
        <div class="text-center">
            <div class="col-xs-12"><?php echo $translationHandler->get_static_text("CREATE_NEW_PROFILE"); ?></div>
                <div class="col-xs-12">
                    <form method="POST" action="" id="create_single" url="createprofile.php" name="create_single">
                        <input type="text" id="firstname" name="firstname" placeholder="<?php echo $translationHandler->get_static_text("INFO_FIRSTNAME"); ?>"><br/>
                        <input type="text" id="surname" name="surname" placeholder="<?php echo $translationHandler->get_static_text("INFO_SURNAME"); ?>"><br/>
                        <input type="text" id="email" name="email" placeholder="<?php echo $translationHandler->get_static_text("INFO_EMAIL"); ?>"><br/>
                        <input type="button" id="submit" name="submit" value="<?php echo $translationHandler->get_static_text("CREATE_SUBMIT"); ?>" class="create_submit_info">
                    </form>
                </div>
            </div>
        </div>


        <div class="text-center">
                <div class="col-xs-12"><?php echo $translationHandler->get_static_text("CREATE_AFFILIATIONS"); ?></div>
                    <div class="col-xs-12">
                        <form method="POST" action="" id="create_single" url="createprofile.php" name="create_single">

                            <select>
                            <?php
                                if($userHandler->_user->user_type_id==1)
                                {
                                    echo '<option value="SA">' . $translationHandler->get_static_text("SUPER_ADMIN") . '</option>';
                                    echo '<option value="A">' . $translationHandler->get_static_text("ADMIN") . '</option>';        
                                }
                            ?>
                                <option value="T"><?php echo $translationHandler->get_static_text("TEACHER"); ?></option>'
                                <option value="S"><?php echo $translationHandler->get_static_text("STUDENT"); ?></option>'
                            </select>
                            </br>
                            <?php
                                if($userHandler->_user->user_type_id==1)
                                {
                                    echo '<input type="text" id="school" name="school" placeholder="' . $translationHandler->get_static_text("SCHOOL_NAME") . '"><br/>';
                                }
                            ?>
                            <input type="text" id="surname" name="surname" placeholder="<?php echo $translationHandler->get_static_text("CLASS_NAME"); ?>"><br/>
                            <input type="button" id="submit" name="submit" value="<?php echo $translationHandler->get_static_text("CREATE_SUBMIT"); ?>" class="create_submit_info">
                        </form>
                    </div>
                </div>
            </div>
        <?php

    }
    ?>