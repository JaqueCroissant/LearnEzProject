<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/translationHandler.php';
$userHandler = SessionKeyHandler::get_from_session("user_handler", true);
$translationHandler = SessionKeyHandler::get_from_session("translation_handler", true);


    //REDIGER BRUGER INFO
    ?>
        <div class="container">
            <div class="input-container">
                <div class="material_design_header"><?php echo $translationHandler->get_static_text("INFO_HEADLINE"); ?></div>
                <form method="post" action="" url="settings.php?step=1" id="edit_info">
                    <table style="width: 100%;">
                        <tr>
                            <td class="left-col">
                                <?php echo $translationHandler->get_static_text("INFO_FIRSTNAME"); ?>
                            </td>
                            <td class="right-col">
                                <input class="material_design_input" id="firstname" name="firstname" value="<?php echo $userHandler->_user->firstname; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="left-col">
                                <?php echo $translationHandler->get_static_text("INFO_SURNAME"); ?>
                            </td>
                            <td class="right-col">
                                <input class="material_design_input" id="surname" name="surname" value="<?php echo $userHandler->_user->surname; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="left-col">
                                <?php echo $translationHandler->get_static_text("INFO_EMAIL"); ?>
                            </td>
                            <td class="right-col">
                                <input class="material_design_input" id="email" name="email" value="<?php echo $userHandler->_user->email; ?>">
                            </td>
                        </tr>
                        <table style="width: 100%;">
                            <br/>
                            <tr>
                                <td class="left-col">
                                    <?php echo $translationHandler->get_static_text("INFO_IMAGE"); ?>
                                </td>

                                <td class="right-col">
                                    <?php echo $translationHandler->get_static_text("INFO_DESCRIPTION"); ?>
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
                                    <input type="button" value="<?php echo $translationHandler->get_static_text("INFO_CHANGE_IMAGE"); ?>">
                                </td>
                            </tr>
                        </table>

                        <tr>
                            <td class="left-col">
                            </td>
                            <td class="right-col">
                                <input type="button" name="submit" value="<?php echo $translationHandler->get_static_text("INFO_SUBMIT"); ?>" class="submit_edit_user_info pull-right material_design_button">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>




                <div class="text-center">
                    <div class="col-xs-12"><?php echo $translationHandler->get_static_text("RESET_PASS_ENTER_MAIL"); ?></div>
                    <div class="col-xs-12">
                        <form method="POST" action="" id="reset_pass_submit_email" url="settings.php?step=2" name="reset_pass_submit_email">
                            <input type="text" id="email" name="email" placeholder="<?php echo $translationHandler->get_static_text("SCHOOL_EMAIL"); ?>"><br/>
                            <input type="button" id="submit" name="submit" value="<?php echo $translationHandler->get_static_text("RESET_PASS_SUBMIT"); ?>" class="reset_pass_submit_email2">
                        </form>
                    </div>
                </div>
