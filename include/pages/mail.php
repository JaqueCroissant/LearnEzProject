<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
require_once '../../include/handler/mailHandler.php';

$current_page = isset($_GET['step']) && !empty($_GET['step']) ? $_GET['step'] : null;
$current_filter = isset($_GET['filter']) && !empty($_GET['filter']) ? $_GET['filter'] : 0;
$current_order = isset($_GET['order']) && !empty($_GET['order']) ? $_GET['order'] : 0;
$current_page_number = isset($_GET['p']) && !empty($_GET['p']) ? $_GET['p'] : 1;

$mailHandler = new MailHandler($current_page);
$paginationHandler = new PaginationHandler();
?>
<div class="row">
    <div class="col-md-2">
        <div class="app-action-panel" id="inbox-action-panel">
            <div class="action-panel-toggle" data-toggle="class" data-target="#inbox-action-panel" data-class="open">
                <i class="fa fa-chevron-right"></i>
                <i class="fa fa-chevron-left"></i>
            </div>

            <?php
                if($current_page != "create_mail") {
            ?>
            
            <div class="m-b-lg">
                <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#composeModal" class="change_page btn action-panel-btn btn-default btn-block" page="mail" args="create_mail" id="mail"><?php echo TranslationHandler::get_static_text("WRITE_NEW"); ?></a>
            </div>
            <hr class="m-0 m-b-md" style="border-color: #ddd;">
            
            <?php
                }
            ?>

            <div class="app-actions-list scrollable-container ps-container ps-theme-default" data-ps-id="0ba7452c-1106-b6f8-5070-56e24cb65638">
                <div class="list-group">
                    <?php
                        foreach($mailHandler->folders as $value) {
                            echo '<a href="javascript:void(0)" class="change_page text-color list-group-item" page="mail" args="'. ($value->folder_name == "inbox" ? "" : $value->folder_name) . '" id="mail"><i class="m-r-sm fa '. $value->icon_class .'"></i>'. $value->title .'</a>';
                        }
                    ?>
                </div>
                
                <hr class="m-0 m-b-md" style="border-color: #ddd;">
                <div class="list-group">
                    <a href="javascript:void(0)" class="change_page text-color list-group-item" page="settings" args="preferences" id="settings"><i class="m-r-sm fa fa-gear"></i><?php echo TranslationHandler::get_static_text("SETTINGS"); ?></a>
                </div>

                <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div>  
            </div>
        </div>
    </div>

    <div class="col-md-10">
        
        <?php
            if($current_page != "create_mail") {
                $fetch_successful = $mailHandler->get_mails($current_page_number, $current_order, $current_filter);
                $mails = $paginationHandler->run_pagination($mailHandler->mails, $current_page_number, 5);
        ?>
        <div class="row">
                <div class="col-md-12">
                    <div class="mail-toolbar m-b-lg">
                        <div class="btn-group" role="group">
                            <a href="javascript:void(0)" target_form="mail_form" class="check_all btn btn-default"><i class="fa fa-square-o"></i></a>
                        </div>

                        <div class="btn-group" role="group">
                            <?php
                            switch($current_page) {  
                                case 'important':
                                echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=inbox" title="Remove from important"><i class="fa fa-reply"></i></a>
                                        <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=spam" title="Spam"><i class="fa fa-exclamation-circle"></i></a>
                                        <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=trash" title="Trash"><i class="fa fa-trash"></i></a>';
                                    break;
                                
                                case "drafts":
                                echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=delete" title="Delete"><i class="fa fa-times"></i></a>';
                                    break;
                                
                                case "sent":
                                echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=delete" title="Delete"><i class="fa fa-times"></i></a>';
                                    break;
                                
                                case "spam":
                                echo '<a href="javascript:void(0)" title="Remove from spam" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=inbox"><i class="fa fa-reply"></i></a>
                                        <a href="javascript:void(0)" title="Trash" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=trash"><i class="fa fa-trash"></i></a>';
                                    break;
                                    
                                case 'trash':
                                echo '<a href="javascript:void(0)" title="Remove from trash" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=inbox"><i class="fa fa-reply"></i></a>
                                        <a href="javascript:void(0)" title="Delete" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=delete"><i class="fa fa-times"></i></a>';
                                    break;
                                
                                default:
                                echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=important" title="Bookmark"><i class="fa fa-bookmark"></i></a>
                                        <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=spam" title="Spam"><i class="fa fa-exclamation-circle"></i></a>
                                        <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=trash" title="Trash" name="submit"><i class="fa fa-trash"></i></a>';
                                    break;
                                        
                            }
                            ?>
                        </div>
                        
                        <div class="btn-group" style="float:right;margin-right: 0px !important;"  role="group">
                            <a href="javascript:void(0)" page="mail" args="<?php echo $current_page ?>" extra_args="<?php echo '&filter=0&order='.$current_order.'&p='.$paginationHandler->get_last_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo $paginationHandler->is_first_page() ? 'disabled' : '';?>><i class="fa fa-chevron-left"></i></a>
                            <a href="javascript:void(0)" page="mail" args="<?php echo $current_page ?>" extra_args="<?php echo '&filter=0&order='.$current_order.'&p='.$paginationHandler->get_next_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo $paginationHandler->is_last_page() ? 'disabled' : '';?>><i class="fa fa-chevron-right"></i></a>
                        </div>
                        
                        <?php
                            switch($current_page) {  
                                case 'sent':
                                case 'drafts':
                                    break;
                                default:
                            ?>
                        <div class="btn-group" style="float:right;" role="group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("FILTER"); ?> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:void(0)" page="mail" args="<?php echo $current_page ?>" extra_args="<?php echo '&filter=0&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("ALL"); ?></a></li>
                                <li><a href="javascript:void(0)" page="mail" args="<?php echo $current_page ?>" extra_args="<?php echo '&filter=1&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("UNREAD"); ?></a></li>
                                <li><a href="javascript:void(0)" page="mail" args="<?php echo $current_page ?>" extra_args="<?php echo '&filter=2&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("READ"); ?></a></li>
                            </ul>
                        </div>
                        <?php
                            break;
                            }
                        ?>
                        
                        <div class="btn-group" style="float:right;"  role="group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("ORDER_BY"); ?> <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:void(0)" page="mail" args="<?php echo $current_page ?>" extra_args="<?php echo '&filter='.$current_filter.'&order=0&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("NEWEST"); ?></a></li>
                                <li><a href="javascript:void(0)" page="mail" args="<?php echo $current_page ?>" extra_args="<?php echo '&filter='.$current_filter.'&order=1&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("OLDEST"); ?></a></li>
                            </ul>
                        </div>
                        
                    </div>
                </div>
            </div>
        <?php
            }
        ?>
        <div class="table-responsive">
            <form method="POST" action="" id="mail_form" url="mail.php" name="mail">
            <input type="hidden" name="current_page" value="<?php echo $current_page; ?>">
            <input type="button" name="submit" style="display:none;">
            <table class="table mail-list"><tbody>
                <tr>
                    <td>
                        <?php
                            if($fetch_successful) {
                                if(count($mailHandler->mails) > 0) {
                                    foreach($mails as $value) {
                                        $date_to_string = time_elapsed($value->date);
                                        echo '
                                            <div class="mail-item mail_number_'. $value->id.'" '. ($value->is_read ? 'style="background: #eceaea;"' : "") .'>
                                                <table class="mail-container"><tbody>
                                                    <tr>
                                                        <td style="vertical-align:middle;padding:0px 6px;">
                                                            <input type="checkbox" name="mail[]" value="'.$value->id.'">                                                     
                                                        </td>
                                                        <td class="mail-left">
                                                            <div class="avatar avatar-lg avatar-circle">
                                                                <a href="#"><img src="assets/images/221.jpg" alt="' . $value->firstname . ' ' . $value->surname .'" title="' . $value->firstname . ' ' . $value->surname .'"></a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div style="cursor:pointer;">
                                                                <p class="mail-item-date" style="float:right;margin-top:-6px;">' . $date_to_string["value"] . ' ' . TranslationHandler::get_static_text($date_to_string["prefix"]) .' ' . TranslationHandler::get_static_text("DATE_AGO") . '</p>
                                                                <div class="mail-item-header">
                                                                    <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">' . $value->title .'</a></h4>
                                                                </div>
                                                                <div class="mail-item-excerpt">' . (strlen($value->text) > 100 ? substr($value->text, 0, 100) . '...' : $value->text) .'</p>
                                                            </div>                                                            
                                                        </td>
                                                    </tr>
                                                </tbody></table>
                                            </div>';
                                    }
                                } else {
                                    echo '<div class="mail-item" style="text-align:center">' .TranslationHandler::get_static_text("NO_MAILS_IN_THIS_FOLDER") .'</div>';
                                }
                            } else {
                                echo $mailHandler->error->title;
                            }
                        ?>
                        
                        <!-- END mail-item -->
                    </td>
                </tr>
            </tbody></table>
            </form>
        </div>
    </div><!-- END column -->
</div>
