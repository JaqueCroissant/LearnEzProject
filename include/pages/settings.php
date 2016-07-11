<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/userHandler.php';

$userHandler = new UserHandler();

    //REDIGER BRUGER INFO
    ?>
        <div class="container">
            <div class="input-container">
                <div class="material_design_header"><?php echo TranslationHandler::get_static_text("INFO_HEADLINE"); ?></div>
                <form method="post" action="" url="settings.php?step=1" id="edit_info">
                    <table style="width: 100%;">
                        <tr>
                            <td class="left-col">
                                <?php echo TranslationHandler::get_static_text("INFO_FIRSTNAME"); ?>
                            </td>
                            <td class="right-col">
                                <input class="material_design_input" id="firstname" name="firstname" value="<?php echo $userHandler->_user->firstname; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="left-col">
                                <?php echo TranslationHandler::get_static_text("INFO_SURNAME"); ?>
                            </td>
                            <td class="right-col">
                                <input class="material_design_input" id="surname" name="surname" value="<?php echo $userHandler->_user->surname; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="left-col">
                                <?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?>
                            </td>
                            <td class="right-col">
                                <input class="material_design_input" id="email" name="email" value="<?php echo $userHandler->_user->email; ?>">
                            </td>
                        </tr>
                        <table style="width: 100%;">
                            <br/>
                            <tr>
                                <td class="left-col">
                                    <?php echo TranslationHandler::get_static_text("INFO_IMAGE"); ?>
                                </td>

                                <td class="right-col">
                                    <?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="left-col">
                                    <img height="180" width="180" src="<?php echo 'assets/images/profile_images/' . $userHandler->_user->image_id . '.png'; ?>"></img>
                                </td>

                                <td class="right-col">
                                    <textarea width="250px;" height="180" class="material_design_input" id="description" name="description"><?php echo $userHandler->_user->description; ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td class="left-col">
                                    <input type="button" value="<?php echo TranslationHandler::get_static_text("INFO_CHANGE_IMAGE"); ?>">
                                </td>
                            </tr>
                        </table>

                        <tr>
                            <td class="left-col">
                            </td>
                            <td class="right-col">
                                <input type="button" name="submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="submit_edit_user_info pull-right material_design_button">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>




        <div class="text-center">
            <div class="col-xs-12"><?php echo TranslationHandler::get_static_text("RESET_PASS_ENTER_MAIL"); ?></div>
                <div class="col-xs-12">
                    <form method="POST" action="" id="settings_pass" url="settings.php?step=2" name="settings_pass">
                        <input type="password" id="old_password" name="old_password" placeholder="<?php echo TranslationHandler::get_static_text("OLD_PASSWORD"); ?>"><br/>
                        <input type="password" id="password" name="password" placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>"><br/>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="<?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?>"><br/>
                        <input type="button" id="submit" name="submit" value="<?php echo TranslationHandler::get_static_text("SET_PASS_SUBMIT"); ?>" class="settings_submit_password">
                    </form>
                    </br>
                    </br>
                    </br>
                    </br>
                    </br>
                    </br>
                    </br>
                    </br>
                    </br>
                    </br>

                </div>
            </div>
        </div>
