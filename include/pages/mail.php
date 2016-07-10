<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
require_once '../../include/handler/mailHandler.php';

$current_page = isset($_GET['step']) && !empty($_GET['step']) ? $_GET['step'] : null;
$current_filter = isset($_GET['filter']) && !empty($_GET['filter']) ? $_GET['filter'] : 0;
$current_order = isset($_GET['order']) && !empty($_GET['order']) ? $_GET['order'] : 0;
$current_page_number = isset($_GET['p']) && !empty($_GET['p']) ? $_GET['p'] : 1;
$mail_id = isset($_GET['mail_id']) && !empty($_GET['mail_id']) ? $_GET['mail_id'] : 0;

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
                <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#composeModal" class="change_page btn action-panel-btn btn-default btn-block" page="mail" step="create_mail" id="mail"><?php echo TranslationHandler::get_static_text("WRITE_NEW"); ?></a>
            </div>
            <hr class="m-0 m-b-md" style="border-color: #ddd;">
            
            <?php
                }
            ?>

            <div class="app-actions-list scrollable-container ps-container ps-theme-default" data-ps-id="0ba7452c-1106-b6f8-5070-56e24cb65638">
                <div class="list-group">
                    <?php
                        foreach($mailHandler->folders as $value) {
                            echo '<a href="javascript:void(0)" class="change_page text-color list-group-item" page="mail" step="'. ($value->folder_name == "inbox" ? "" : $value->folder_name) . '" id="mail"><i class="m-r-sm fa '. $value->icon_class .'"></i>'. $value->title .'</a>';
                        }
                    ?>
                </div>
                
                <hr class="m-0 m-b-md" style="border-color: #ddd;">
                <div class="list-group">
                    <a href="javascript:void(0)" class="change_page text-color list-group-item" page="mail" step="search" id="mail"><i class="m-r-sm fa fa-search"></i><?php echo TranslationHandler::get_static_text("SEARCH"); ?></a>
                    <a href="javascript:void(0)" class="change_page text-color list-group-item" page="settings" step="preferences" id="settings"><i class="m-r-sm fa fa-gear"></i><?php echo TranslationHandler::get_static_text("SETTINGS"); ?></a>
                </div>

                <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div>  
            </div>
        </div>
    </div>

    <div class="col-md-10">
        
        <?php
        
            switch($current_page) {
                case 'create_mail':
                ?>
        
            <div class="row">
                <div class="col-md-12">
                        <div class="panel panel-default new-message">						
                            <form action="#">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <input type="text" class="form-control input-sm" placeholder="<?php echo TranslationHandler::get_static_text("RECEIVER"); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <input type="text" class="form-control input-sm" placeholder="<?php echo TranslationHandler::get_static_text("SUBJECT"); ?>">
                                    </div>
                                    
                                    
                                    <div class="form-group" style="margin: -10px 0px 0px 0px !important;">
                                        <div class="checkbox" style="float:left;">
                                            <input class="input-lg" type="checkbox" id="checkbox-enable-reply" checked> <label for="checkbox-enable-reply"></label>
                                        </div>
                                        <div><?php echo TranslationHandler::get_static_text("MAIL_ARROW_RECEIVER_TO_REPLY"); ?></div>
                                        <div style="clear:both;"></div>
                                    </div>
                                        

                                    <textarea name="new_message_body" class="form-control input-sm full-wysiwyg"></textarea>
                                </div>

                                <div class="panel-footer clearfix">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-success"><i class="fa fa-save"></i></button>
                                        <button type="button" class="btn btn-primary"><?php echo TranslationHandler::get_static_text("SEND"); ?> <i class="fa fa-send" style="margin-left:3px;"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>  
                </div>
                <?php
                    break;
                
                case 'search':
                ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mail-toolbar m-b-lg">
                                <div class="btn-group" style="float:right;margin-right: 0px !important;"  role="group">
                                    <a href="javascript:void(0)" page="mail" step="search" args="<?php echo '&filter=0&order='.$current_order.'&p='.$paginationHandler->get_last_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo /*$paginationHandler->is_first_page()*/ true ? 'disabled' : '';?>><i class="fa fa-chevron-left"></i></a>
                                    <a href="javascript:void(0)" page="mail" step="search" args="<?php echo '&filter=0&order='.$current_order.'&p='.$paginationHandler->get_next_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo /*$paginationHandler->is_last_page()*/ true ? 'disabled' : '';?>><i class="fa fa-chevron-right"></i></a>
                                </div>

                                <div class="btn-group" style="float:right;" role="group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("FILTER"); ?> <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo '&filter=0&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("ALL"); ?></a></li>
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo '&filter=1&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("UNREAD"); ?></a></li>
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo '&filter=2&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("READ"); ?></a></li>
                                    </ul>
                                </div>

                                <div class="btn-group" style="float:right;"  role="group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("ORDER_BY"); ?> <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo '&filter='.$current_filter.'&order=0&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("NEWEST"); ?></a></li>
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo '&filter='.$current_filter.'&order=1&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("OLDEST"); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    break;
                
                
                
                case 'show_mail':
                    if($mailHandler->get_mail($mail_id)) {
                        $current_mail = $mailHandler->current_mail;
                    ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mail-toolbar m-b-lg">
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0)" page="mail" id="mail" step="<?php echo $current_mail->folder_name == "inbox" ? "" : $current_mail->folder_name; ?>" class="change_page btn btn-default"><i class="fa fa-arrow-left"></i></a>
                                    </div>
                                    
                                    <div class="btn-group" role="group">
                                    <?php
                                    switch($current_mail->folder_name) {  
                                        case 'important':
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="inbox" title="Remove from important"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="spam" title="Spam"><i class="fa fa-exclamation-circle"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="trash" title="Trash"><i class="fa fa-trash"></i></a>';
                                            break;

                                        case "sent":
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="delete" title="Delete"><i class="fa fa-times"></i></a>';
                                            break;
                                        
                                        case "drafts":
                                            break;

                                        case "spam":
                                        echo '<a href="javascript:void(0)" title="Remove from spam" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="inbox"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)" title="Trash" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="trash"><i class="fa fa-trash"></i></a>';
                                            break;

                                        case 'trash':
                                        echo '<a href="javascript:void(0)" title="Remove from trash" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'"  step="inbox"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)" title="Delete" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="delete"><i class="fa fa-times"></i></a>';
                                            break;


                                        default:
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="important" title="Bookmark"><i class="fa fa-bookmark"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="spam" title="Spam"><i class="fa fa-exclamation-circle"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->id.'" current_folder="'.$current_mail->folder_name.'" step="trash" title="Trash" name="submit"><i class="fa fa-trash"></i></a>';
                                            break;
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mail-view">
                            <div class="divid" style="margin: 0px 0px 16px 0px !important"></div>
                            <div class="media">
                                <div class="media-left">
                                    <div class="avatar avatar-lg avatar-circle">
                                        <img class="img-responsive" src="assets/images/221.jpg" alt="avatar">
                                    </div>
                                </div>

                                <div class="media-body">
                                    <div class="m-b-sm">
                                        <h4 class="m-0 inline-block m-r-lg">
                                            <a href="#" class="title-color"><?php echo $current_mail->firstname . " " . $current_mail->surname; ?></a>
                                        </h4>
                                    </div>
                                    
                                    <?php 
                                    $dateTime = datetime::createfromformat("Y-m-d H:i:s", $current_mail->date);
                                    echo "<p><b>" . TranslationHandler::get_static_text("DATE_DATE") . ":</b> " . $dateTime->format('d-m-Y H:i') . "</p>"; 
                                    echo "<p><b>" . TranslationHandler::get_static_text("FOLDER") . ":</b> " . $mailHandler->folders[$current_mail->folder_name]->title . "</p>";
                                    ?>
                                </div>
                            </div>
                            <div class="divid"></div>

                            <h4 class="m-0" style="margin-top:10px !important;"><?php echo $current_mail->title; ?></h4>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="m-h-lg lh-xl">
                                        <p><?php echo $current_mail->text; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        if($current_mail->folder_id == 3) {
                            echo '
                                <div class="divid"></div>
                                <div style="text-align:center;">'. TranslationHandler::get_static_text("MAIL_CANT_RESPOND_TO_MAIL") . '</div>';
                        } else {
                        ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default new-message">
                                        <div class="panel-heading text-muted" style="padding: 16px 16px 8px 16px !important;">
                                            <?php echo TranslationHandler::get_static_text("RECEIVER") . ": " . $current_mail->firstname . " " . $current_mail->surname; ?>
                                        </div>								
                                        <div class="panel-body p-0">
                                            <textarea name="new_message_body" id="new-message-body" style="padding: 8px 16px 16px 16px !important;" placeholder="<?php echo TranslationHandler::get_static_text("WRITE_YOUR_ANSWER"); ?>"></textarea>
                                        </div>
                                        <div class="panel-footer">
                                            <button type="button" class="btn btn-primary pull-right"><?php echo TranslationHandler::get_static_text("REPLY"); ?> <i class="fa fa-send" style="margin-left:3px;"></i></button>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
        
                <?php
                    } else {
                        echo $mailHandler->error->title;
                    }
                    break;
                
                
                
                default:
                    $fetch_successful = $mailHandler->get_mails($current_page_number, $current_order, $current_filter);
                    $mails = $paginationHandler->run_pagination($mailHandler->mails, $current_page_number, 5);
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mail-toolbar m-b-lg">
                                <div class="btn-group" role="group">
                                    <a href="javascript:void(0)" target_form="mail_form" class="check_all btn btn-default"><i class="fa fa-square-o"></i></a>'
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
                                    <a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter=0&order='.$current_order.'&p='.$paginationHandler->get_last_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo $paginationHandler->is_first_page() ? 'disabled' : '';?>><i class="fa fa-chevron-left"></i></a>
                                    <a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter=0&order='.$current_order.'&p='.$paginationHandler->get_next_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo $paginationHandler->is_last_page() ? 'disabled' : '';?>><i class="fa fa-chevron-right"></i></a>
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
                                                <li><a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter=0&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("ALL"); ?></a></li>
                                                <li><a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter=1&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("UNREAD"); ?></a></li>
                                                <li><a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter=2&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("READ"); ?></a></li>
                                            </ul>
                                        </div>
                                
                                <?php
                                    break;
                                    }
                                ?>

                                <div class="btn-group" style="float:right;"  role="group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("ORDER_BY"); ?> <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter='.$current_filter.'&order=0&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("NEWEST"); ?></a></li>
                                        <li><a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter='.$current_filter.'&order=1&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("OLDEST"); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
        
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
                                                        <div class="mail-item mail_number_'. $value->id.' '.($value->is_read ? 'mail-item-read' : "") .'" style="padding: 0px !important;">
                                                            <div class="mail_element_checkbox">
                                                                <div style="display:table-cell;vertical-align:middle;height:100%;padding:0px 16px">
                                                                    <div class="checkbox">
                                                                        <input type="checkbox" id="checkbox-enable-reply" name="mail[]" value="'.$value->id.'"><label for="checkbox-enable-reply"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="change_page mail_element_content" page="mail" id="mail" step="show_mail" args="&mail_id='. $value->id .'">
                                                                <table class="mail-container"><tbody>
                                                                    <tr>

                                                                        <td class="mail-left">
                                                                            <div class="avatar avatar-lg avatar-circle">
                                                                                <img src="assets/images/221.jpg" alt="' . $value->firstname . ' ' . $value->surname .'" title="' . $value->firstname . ' ' . $value->surname .'">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div>
                                                                                <p class="mail-item-date" style="float:right;margin-top:-6px;">' . $date_to_string["value"] . ' ' . TranslationHandler::get_static_text($date_to_string["prefix"]) .' ' . TranslationHandler::get_static_text("DATE_AGO") . '</p>
                                                                                <div class="mail-item-header">
                                                                                    <h4 class="mail-item-title"><p class="title-color">' . $value->title .'</p></h4>
                                                                                </div>
                                                                                <div class="mail-item-excerpt">' . (strlen($value->text) > 100 ? substr($value->text, 0, 100) . '...' : $value->text) .'</p>
                                                                            </div>                                                            
                                                                        </td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </div>
                                                            <div style="clear:both;"></div>
                                                        </div>';
                                                }
                                            } else {
                                                echo '<div class="mail-item" style="text-align:center">' .TranslationHandler::get_static_text("NO_MAILS_IN_THIS_FOLDER") .'</div>';
                                            }
                                        } else {
                                            echo $mailHandler->error->title;
                                        }
                                    ?>
                                </td>
                            </tr>
                        </tbody></table>
                        </form>
                    </div>
        <?php
            break;
        }     
        ?>
    </div>
</div>
