<?php
require_once 'require.php';
require_once '../../include/handler/pageHandler.php';
require_once '../handler/notificationHandler.php';
$not_handler = new NotificationHandler();
$paginationHandler = new PaginationHandler();

$current_page = isset($_GET['step']) && !empty($_GET['step']) ? $_GET['step'] : "all";
$current_page_number = isset($_GET['p']) && !empty($_GET['p']) ? $_GET['p'] : 1;


$current_page != "all" ? 
    $not_handler->load_notifications_from_category($not_handler->_user->id, 0, $current_page, 100) : 
    $not_handler->load_notifications($not_handler->_user->id, 0, 100);
$notifs = $paginationHandler->run_pagination($not_handler->get_notifications(), $current_page_number, 5);
?>
<script src="js/subpageGlobal.js" type="text/javascript"></script>

<div class="row">
    <div class="col-md-2">
        <h4 class="m-b-lg"><?php echo TranslationHandler::get_static_text("NOTIFICATIONS") ?></h4>
        <hr class="m-0 m-b-md" style="border-color: #ddd;">
        <div class="app-action-panel" id="inbox-action-panel">
            <div class="action-panel-toggle" data-toggle="class" data-target="#inbox-action-panel" data-class="open">
                <i class="fa fa-chevron-right"></i>
                <i class="fa fa-chevron-left"></i>
            </div>

            <div class="app-actions-list scrollable-container ps-container ps-theme-default" data-ps-id="0ba7452c-1106-b6f8-5070-56e24cb65638">
                <div class="list-group">
                    <a href='javascript:void(0)' class='change_page text-color list-group-item' page='notifications' step='all' id='notifications'><i class='m-r-sm fa fa-bell'></i><?php echo TranslationHandler::get_static_text("ALL") ?></a>
                    <?php
                    foreach ($not_handler->get_notification_categories() as $value) {
                        echo "<a href='javascript:void(0)' class='change_page text-color list-group-item' page='notifications' step='" . $value["category_name"] . "' id='notifications'><i class='m-r-sm fa " . $value["icon_class"] . "'></i>" . $value["name"] . "</a>";
                    }
                    ?>
                </div>
                <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div>  
            </div>
        </div>
    </div>
    <div class="col-md-10">
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group m-b-lg" role="group">
                    <a href="javascript:void(0)" target_form="notification_form" class="check_all btn btn-default" title="<?php echo TranslationHandler::get_static_text("SELECT_ALL") ?>"><i class="fa fa-square-o"></i></a>
                </div>
                <div class="btn-group m-b-lg">
                    <a href="javascript:void(0)" target_form="notification_form" args="?action=delete" action="delete" class="notifs_button btn btn-default" title="<?php echo TranslationHandler::get_static_text("DELETE_CHOSEN") ?>"><i class="fa fa-trash"></i></a>
                    <a href="javascript:void(0)" target_form="notification_form" args="?action=read" action="read" class="notifs_button btn btn-default" title="<?php echo TranslationHandler::get_static_text("MARK_CHOSEN_AS_READ") ?>"><i class="fa fa-flag"></i></a>
                </div>
                <div class="btn-group" style="float:right;margin-right: 0px !important;"  role="group">
                    <a href="javascript:void(0)" page="notifications" step="<?php echo $current_page . '" args="&p=' . $paginationHandler->get_last_page(); ?>" id="notifications" class="change_page btn btn-default" <?php echo $paginationHandler->is_first_page() ? 'disabled' : ''; ?>><i class="fa fa-chevron-left"></i></a>
                    <a href="javascript:void(0)" page="notifications" step="<?php echo $current_page . '" args="&p=' . $paginationHandler->get_next_page(); ?>" id="notifications" class="change_page btn btn-default" <?php echo $paginationHandler->is_last_page() ? 'disabled' : ''; ?>><i class="fa fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <form method="POST" action="" id="notification_form" url="notifications.php" name="notif">
                        <input type="button" hidden="" name="submit">
                        <table class="table mail-list">
                            <tr>
                                <td>
                                    <?php
                                        if (count($not_handler->get_notifications()) > 0) {
                                            foreach ($notifs as $notif) {
                                                $timeString = time_elapsed($notif->datetime);
                                                echo '
                                                <div class="mail-item notif_count_' . $notif->id . " " . ($notif->isRead == 2 ? 'notif_read' : 'notif_unread') . '" style="min-height:100px;">
                                                    <table class="mail-container">
                                                        <tr>
                                                            <td class="mail-left">
                                                                <div class="notif_element_checkbox checkbox-resize">
                                                                    <div class="checkbox">
                                                                        <input type="checkbox" id="checkbox-enable-reply" name="notifications[]" value="' . $notif->id . '"><label for="checkbox-enable-reply"></label>
                                                                    </div>
                                                                </div>
                                                            </td>     
                                                            <td class="mail-center">
                                                                <div class="mail-item-header">
                                                                    <h4 class="mail-item-title">' . $notif->title . '</h4>
                                                                    <i class="fa ' . $notif->icon . '"></i>
                                                                </div>
                                                                <p class="mail-item-excerpt">' . $notif->text . '</p>
                                                            </td>
                                                            <td class="mail-right">
                                                                <p class="mail-item-date">' . $timeString["value"] . ' ' . TranslationHandler::get_static_text($timeString["prefix"]) . ' ' . TranslationHandler::get_static_text("DATE_AGO") . '</p>
                                                            </td>
                                                        </tr>
                                                    </table>			
                                                </div>';
                                            }
                                        }
                                        else {
                                             echo '<div class="mail-item" style="text-align:center">' . ($current_page != "all" ? TranslationHandler::get_static_text("NO_NOTIFICATIONS_IN_CATEGORY") : TranslationHandler::get_static_text("NO_NOTIFICATIONS_IN_ALL")) . '</div>';
                                        }
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
