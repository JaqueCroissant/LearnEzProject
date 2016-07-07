<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/translationHandler.php';
    
    $userHandler = SessionKeyHandler::get_from_session("user_handler", true);
    $translationHandler = SessionKeyHandler::get_from_session("translation_handler", true);
    


    ?>
        <!--GRUNDLÆGGENDE INFO + TILHØRSFORHOLD FOR OPRETTELSE AF ENKELT BRUGER-->
        <div class="text-center">
            <div class="col-xs-12"><h2><?php echo $translationHandler->get_static_text("CREATE_NEW_PROFILE"); ?></h2></div>
                <div class="col-xs-12">
                    <form method="POST" action="" id="create_single" url="createprofile.php" name="create_single">
                        <table style="width: 30%;">
                            <tr>
                                <td class="left-col">
                                    <div class="col-xs-12"><?php echo $translationHandler->get_static_text("CREATE_BASIC INFO"); ?></div>
                                </td>
                                <td class="right-col">
                                    <div class="col-xs-12"><?php echo $translationHandler->get_static_text("CREATE_AFFILIATIONS"); ?></div>
                                </td>
                            </tr>

                            <tr>
                                <td class="left-col">
                                    <input type="text" id="firstname" name="firstname" placeholder="<?php echo $translationHandler->get_static_text("INFO_FIRSTNAME"); ?>"><br/>
                                </td>
                                <td class="right-col">
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
                                </td>
                            </tr>

                            <tr>
                                <td class="left-col">
                                    <input type="text" id="surname" name="surname" placeholder="<?php echo $translationHandler->get_static_text("INFO_SURNAME"); ?>"><br/>
                                </td>
                                <td class="right-col">
                                    <?php
                                        if($userHandler->_user->user_type_id==1)
                                        {
                                            echo '<input type="text" id="school" name="school" placeholder="' . $translationHandler->get_static_text("SCHOOL_NAME") . '"><br/>';
                                        }
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td class="left-col">
                                    <input type="text" id="email" name="email" placeholder="<?php echo $translationHandler->get_static_text("INFO_EMAIL"); ?>"><br/>
                                </td>
                                <td class="right-col">
                                    <input type="text" id="class_name" name="class_name" placeholder="<?php echo $translationHandler->get_static_text("CLASS_NAME"); ?>"><br/>
                                </td>
                            </tr>

                            <tr>
                                <td class="left-col">
                                    <input type="password" id="password" name="password" placeholder="<?php echo $translationHandler->get_static_text("PASSWORD"); ?>"><br/>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="button" id="submit" name="submit" value="<?php echo $translationHandler->get_static_text("CREATE_SUBMIT"); ?>" class="create_submit_info">
                                </td>
                            </tr>
                        </table>
                    </form>
                    </br>
                    </br>
                    </br>
                </div>
            </div>
        </div>


        <!--UPLOAD-FORMULAR FOR CSV-FILER-->
        <div class="text-center">
                    <div class="col-xs-12">
                        <form method="POST" action="" id="create_import" url="createprofile.php" name="create_import">
                            <table style="width: 30%;">

                                <tr>
                                    <td>
                                        <div class="col-xs-12"><?php echo $translationHandler->get_static_text("CREATE_IMPORT_PROFILES"); ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                            if($userHandler->_user->user_type_id==1)
                                            {
                                                echo '<input type="text" id="school" name="school" placeholder="' . $translationHandler->get_static_text("SCHOOL_NAME") . '"><br/>';
                                            }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input type="file" id="csv_file" name="csv_file" accept=".csv" placeholder="<?php echo $translationHandler->get_static_text("CLASS_NAME"); ?>"><br/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="button" id="submit" name="submit" value="<?php echo $translationHandler->get_static_text("CREATE_IMPORT"); ?>" class="create_import_profiles">
                                    </td>
                                </tr>
                            </table>
                        </form>
                        </br>
                        </br>
                        </br>
                    </div>
                </div>
            </div>