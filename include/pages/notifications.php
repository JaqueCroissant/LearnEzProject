<?php
require_once '../../include/ajax/require.php';
require_once '../handler/notificationHandler.php';
$not_handler = new NotificationHandler();
?>

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
                    <a href='javascript:void(0)' class='text-color list-group-item'><i class='m-r-sm fa fa-bookmark'></i><?php echo TranslationHandler::get_static_text("ALL") ?></a>
                    <?php
                    foreach ($not_handler->get_notification_categories() as $value) {
                        echo "<a href='javascript:void(0)' class='text-color list-group-item'><i class='m-r-sm fa " . $value["icon"] . "'></i>" . $value["title"] . "</a>";
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
                    <a href="javascript:void(0)" target_form="notification_form" class="check_all btn btn-default" title="<?php echo TranslationHandler::get_current_language("SELECT_ALL") ?>"><i class="fa fa-square-o"></i></a>
                </div>
                <div class="btn-group m-b-lg">
                    <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" title="<?php echo TranslationHandler::get_current_language("DELETE_CHOSEN") ?>"><i class="fa fa-trash"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <form method="POST" action="" id="notification_form" url="" name="notif">
                        <table class="table notification_list">
                            <tr>
                                <td>
                                    <div class="mail-item" style="height:100px;">
                                        <div class="notif_element_checkbox">
                                            <div class="checkbox">
                                                <input type="checkbox" id="checkbox-enable-reply" name="notifications[]" value="<?php ?>"><label for="checkbox-enable-reply"></label>
                                            </div>
                                        </div>
                                        <table class="notification-container">
                                            <tr>
                                                <td>
                                                    <div class="avatar avatar-lg avatar-circle">
                                                        <a href="#"><img src="assets/images/208.jpg" alt="sender photo"></a>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mail-item-header">
                                                        <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">Welcome To Dashboard</a></h4>
                                                        <a href="#"><span class="label label-success">client</span></a>                                                      
                                                        <a href="#"><span class="label label-primary">work</span></a>
                                                    </div>
                                                    <p class="mail-item-excerpt">Welcome To your dashboard. here you can manage and coordinate any activities</p>
                                                </td>
                                                <td class="mail-right">
                                                    <p class="mail-item-date">2 hours ago</p>
                                                    <p class="mail-item-star starred">
                                                        <a href="#"><i class="zmdi zmdi-star"></i></a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>			
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>