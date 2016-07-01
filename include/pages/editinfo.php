<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/translationHandler.php';
$userHandler = SessionKeyHandler::get_from_session("user_handler", true);
$translationHandler = SessionKeyHandler::get_from_session("translation_handler", true);
?>

<div class="container">
    <div class="input-container">
        <div class="material_design_header"><?php echo $translationHandler->get_static_text("INFO_HEADLINE"); ?></div>
        <form method="post">
            <table style="width: 100%;">
                <tr>
                    <td class="left-col">
                        <?php echo $translationHandler->get_static_text("INFO_FIRSTNAME"); ?>
                    </td>
                    <td class="right-col">
                        <input class="material_design_input" id="firstname" name="firstname" value="<?php echo $userHandler->current_user->firstname; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="left-col">
                        <?php echo $translationHandler->get_static_text("INFO_SURNAME"); ?>
                    </td>
                    <td class="right-col">
                        <input class="material_design_input" id="surname" name="surname" value="<?php echo $userHandler->current_user->surname; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="left-col">
                        <?php echo $translationHandler->get_static_text("INFO_EMAIL"); ?>
                    </td>
                    <td class="right-col">
                        <input class="material_design_input" id="email" name="email" value="<?php echo $userHandler->current_user->email; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="left-col">
                        <?php echo $translationHandler->get_static_text("INFO_DESCRIPTION"); ?>
                    </td>
                    <td class="right-col">
                        <input class="material_design_input" id="description" name="description" value="<?php echo $userHandler->current_user->description; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="left-col">
                        <?php echo $translationHandler->get_static_text("INFO_IMAGE"); ?>
                    </td>
                    <td class="right-col">
                        <select class="material_design_select" id="image" name="image">
                            <?php
                            //foreach ($schoolHandler->school_types as $value) {
                            //    echo '<option value="'. $value['id'] .'">'. $value['title'] .'</option>';
                            //}
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="left-col">
                    </td>
                    <td class="right-col">
                        <input type="button" value="<?php echo $translationHandler->get_static_text("INFO_SUBMIT"); ?>" class="pull-right material_design_button">
                    </td>
                </tr>
            </table>
            
        </form>
    </div>
    <div class="input-container">
        <div class="progress">
            <div class="progress-bar-success progress-bar-striped"><span>33% completed</span></div>
        </div>
    </div>
</div>