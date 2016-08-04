<?php
require_once 'require.php';
require_once '../../include/handler/pageHandler.php';
require_once '../../include/handler/notificationHandler.php';
require_once '../../include/handler/mailHandler.php';

echo "<pre>"; var_dump($_SESSION); echo "</pre>";
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
                <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#composeModal" class="change_page btn action-panel-btn btn-default btn-block <?php echo !RightsHandler::has_user_right("MAIL_CREATE") ? 'disabled' : ''; ?>" page="mail" step="create_mail" id="mail"><?php echo TranslationHandler::get_static_text("WRITE_NEW"); ?></a>
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
                    <?php if(RightsHandler::has_user_right("MAIL_SEARCH")) { ?>
                    <a href="javascript:void(0)" class="change_page text-color list-group-item" page="mail" step="search" id="mail"><i class="m-r-sm fa fa-search"></i><?php echo TranslationHandler::get_static_text("SEARCH"); ?></a>
                    <?php } ?>
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
                            <form method="POST" action="" id="create_mail_form" url="mail.php?step=create_mail" name="create_mail">
                                <div class="panel-body">
                                    
                                    <div class="form-group m-b-sm">
                                        <label for="mail_recipiants" class="control-label"><?php echo TranslationHandler::get_static_text("RECEIVER"); ?>:</label>
                                        <select id="mail_recipiants" name="recipiants[]" class="form-control" data-plugin="select2" <?php echo RightsHandler::has_user_right("MAIL_MULTIPLE_RECEIVERS") ? 'multiple' : ''; ?>>
                                        <?php echo !RightsHandler::has_user_right("MAIL_MULTIPLE_RECEIVERS") ? '<option value="">'.TranslationHandler::get_static_text("RECEIVER").'</option>' : ''; ?>
                                            <?php
                                            foreach($mailHandler->get_receiptians() as $key => $value) {
                                                foreach($value as $inner_key => $inner_value) {
                                                    switch($key) {
                                                        case "SCHOOL":
                                                            echo '<option value="SCHOOL_ADMIN_'.$inner_value->id.'">'. $inner_value->name .': ' .TranslationHandler::get_static_text("ADMINS") .'</option>';
                                                            echo '<option value="SCHOOL_TEACHER_'.$inner_value->id.'">'. $inner_value->name .': ' .TranslationHandler::get_static_text("TEACHERS") .'</option>';
                                                            echo '<option value="SCHOOL_STUDENT_'.$inner_value->id.'">'. $inner_value->name .': ' .TranslationHandler::get_static_text("STUDENTS") .'</option>';
                                                            if(count($inner_value->classes) > 0) {
                                                                foreach(reset($inner_value->classes) as $class_key => $class_value) {
                                                                    
                                                                    echo '<option value="CLASS_TEACHER_'.$class_value->id.'">'. $inner_value->name .' - '. $class_value->title .': ' .TranslationHandler::get_static_text("TEACHERS") .'</option>'; 
                                                                    echo '<option value="CLASS_STUDENT_'.$class_value->id.'">'. $inner_value->name .' - '. $class_value->title .': ' .TranslationHandler::get_static_text("STUDENTS") .'</option>'; 
                                                                }
                                                            }
                                                            break;
                                                        
                                                        case "USERS":
                                                            echo '<option value="USER_ANY_'.$inner_value->id.'">'.$inner_value->firstname.' ' . $inner_value->surname.' ('. (empty($inner_value->school_name) ? TranslationHandler::get_static_text("SUPER_ADMIN") : $inner_value->school_name) .')</option>';
                                                            break;
                                                        
                                                        case "CLASS":
                                                            echo '<option value="CLASS_TEACHER_'.$inner_value->id.'">'. TranslationHandler::get_static_text("CLASS") .' - '. $inner_value->title .': ' .TranslationHandler::get_static_text("TEACHERS") .'</option>';
                                                            echo '<option value="CLASS_STUDENT_'.$inner_value->id.'">'. TranslationHandler::get_static_text("CLASS") .' - '. $inner_value->title .': ' .TranslationHandler::get_static_text("STUDENTS") .'</option>'; 
                                                            break;
                                                    }
                                                    
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group m-b-sm">
                                        <label for="mail_title" class="control-label"><?php echo TranslationHandler::get_static_text("SUBJECT"); ?>:</label>
                                        <input id="mail_title" type="text" name="title" class="form-control">
                                    </div>
                                    
                                    
                                    <div class="form-group">
                                        <label for="select_mail_tags" class="control-label"><?php echo TranslationHandler::get_static_text("MAIL_TAG"); ?>:</label>
                                        <select id="select_mail_tags" name="mail_tags[]" class="form-control" data-plugin="select2" multiple>
                                            <?php
                                            foreach($mailHandler->tags as $tag) {
                                                echo '<option value="'.$tag->id.'">'.$tag->title.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <?php if(RightsHandler::has_user_right("MAIL_DISABLE_REPLY")) { ?>
                                    <div class="form-group" style="margin: -10px 0px 0px 0px !important;">
                                        <div class="checkbox" style="float:left;">
                                            <input name="disable_reply" class="form-control" type="checkbox" id="checkbox-enable-reply" checked > <label for="checkbox-enable-reply"></label>
                                        </div>
                                        <div><?php echo TranslationHandler::get_static_text("MAIL_ARROW_RECEIVER_TO_REPLY"); ?></div>
                                        <div style="clear:both;"></div>
                                    </div>
                                    <?php 
                                    } else {
                                    ?>
                                    <input name="disable_reply" type="hidden" value="1" />
                                    <?php
                                    } ?>
                                        

                                    <textarea name="message" class="form-control input-sm full-wysiwyg" style="min-height:150px !important;"></textarea>
                                </div>

                                <div class="panel-footer clearfix">
                                    <div class="pull-right">
                                        <button type="button" class="btn btn-success"><i class="fa fa-save"></i></button>
                                        <button type="button" name="submit" id="submit_button" class="submit_create_mail btn btn-primary"><?php echo TranslationHandler::get_static_text("SEND"); ?> <i class="fa fa-send" style="margin-left:3px;"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>  
                </div>
                <?php
                    break;
                
                case 'search':
                    $fetch_successful = false;
                    $fetch_query = "";
                    if(isset($_GET["search_q"]) && isset($_GET["search_f"]) && isset($_GET["search_c"])) {
                        $fetch_successful = $mailHandler->search_mail($_GET["search_q"], unserialize($_GET["search_f"]), $_GET["search_c"], $current_order, $current_filter);
                        $mails = $paginationHandler->run_pagination($mailHandler->search_mails, $current_page_number, SettingsHandler::get_settings()->elements_shown);
                        $search_f = str_replace('"', '&quot;', $_GET['search_f']);
                        $fetch_query = '&search_q='.$_GET['search_q'].'&search_f='.$search_f.'&search_c='.$_GET['search_c'];
                    }
                    if($fetch_successful) {
                ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mail-toolbar m-b-lg">
                                <div class="btn-group" role="group">
                                    <a href="javascript:void(0)" target_form="mail_form" class="check_all btn btn-default" style="opacity: 0;cursor:default"><i class="fa fa-square-o" style="opacity:0"></i></a>
                                </div>
                                
                                <div class="btn-group" style="float:right;margin-right: 0px !important;"  role="group">
                                    <a href="javascript:void(0)" page="mail" step="search" args="<?php echo $fetch_query.'&filter='.$current_filter.'&order='.$current_order.'&p='.$paginationHandler->get_last_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo $paginationHandler->is_first_page() ==  true ? 'disabled' : '';?>><i class="fa fa-chevron-left"></i></a>
                                    <a href="javascript:void(0)" page="mail" step="search" args="<?php echo $fetch_query.'&filter='.$current_filter.'&order='.$current_order.'&p='.$paginationHandler->get_next_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo $paginationHandler->is_last_page() == true ? 'disabled' : '';?>><i class="fa fa-chevron-right"></i></a>
                                </div>

                                <div class="btn-group" style="float:right;" role="group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("FILTER"); ?> <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo $fetch_query.'&filter=0&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("ALL"); ?></a></li>
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo $fetch_query.'&filter=1&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("UNREAD"); ?></a></li>
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo $fetch_query.'&filter=2&order='.$current_order.'&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("READ"); ?></a></li>
                                    </ul>
                                </div>

                                <div class="btn-group" style="float:right;"  role="group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("ORDER_BY"); ?> <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo $fetch_query.'&filter='.$current_filter.'&order=0&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("NEWEST"); ?></a></li>
                                        <li><a href="javascript:void(0)" page="mail" step="search" args="<?php echo $fetch_query.'&filter='.$current_filter.'&order=1&p='.$current_page_number; ?>" id="mail" class="change_page"><?php echo TranslationHandler::get_static_text("OLDEST"); ?></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table mail-list"><tbody><tr><td>
                            <?php
                                if($fetch_successful) {
                                foreach($mails as $value) {
                                    $date_to_string = time_elapsed($value->date);
                                    echo '
                                        <div class="mail-item mail_number_'. $value->user_mail_id.' '.(!$value->is_read ? 'item_unread' : "") .' item_hover" style="height:100px;">
                                            <div class="change_page mail_element_content" page="mail" id="mail" step="show_mail" args="&mail_id='. $value->user_mail_id .'">
                                                <table class="mail-container"><tbody>
                                                    <tr>

                                                        <td class="mail-left">
                                                            <div class="avatar avatar-lg avatar-circle" data-toggle="tooltip" title="' . $value->firstname . ' ' . $value->surname .'">
                                                                <img src="assets/images/profile_images/'.$value->user_image_id.'.png">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <p class="mail-item-date" style="float:right;margin-top:-6px;">' . $date_to_string["value"] . ' ' . TranslationHandler::get_static_text($date_to_string["prefix"]) .' ' . TranslationHandler::get_static_text("DATE_AGO") . '</p>
                                                                <div class="mail-item-header">
                                                                    <h4 class="mail-item-title"><p class="title-color">' . $value->title .'</p></h4>';
                                                                    foreach($value->mail_tags as $mail_tag) {
                                                                       echo '<span class="label '. $mail_tag->color_class .'" style="margin-right: 5px !important;">'. $mail_tag->title .'</span>';
                                                                    }
                                                                    echo '
                                                                </div>
                                                                <div class="mail-item-excerpt">' . (strlen($value->text) > 85 ? substr($value->text, 0, 85) . '...' : $value->text) .'</p>
                                                            </div>                                                            
                                                        </td>
                                                    </tr>
                                                </tbody></table>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>';
                                        }
                                } else {
                                    ErrorHandler::show_error_page($mailHandler->error);
                                    die();
                                }
                            ?>
                            </td></tr></tbody></table>
                    </div>
                    <?php
                    } else {
                        if(isset($_GET["url"])) {
                            $_GET["url"] = null;
                        }
                    ?>
                    <div class="panel panel-default new-message">						
                        <form method="POST" action="" id="search_mail_form" url="mail.php?step=search" name="search_mail">
                            <input type="hidden" name="current_page" value="<?php echo $current_page; ?>">
                            <div class="panel-body">


                                <div class="form-group m-b-sm">
                                    <label for="mail_search_word" class="control-label"><?php echo TranslationHandler::get_static_text("SEARCH_WORDS"); ?>:</label>
                                    <input id="mail_search_word" type="text" name="search_word" class="form-control">
                                </div>


                                <div class="form-group">
                                    <label for="select_mail_folders" class="control-label"><?php echo TranslationHandler::get_static_text("FOLDER"); ?>:</label>
                                    <select id="select_mail_folders" name="search_folders[]" class="form-control" data-plugin="select2" multiple>
                                        <option value="ALL"><?php echo TranslationHandler::get_static_text("ALL"); ?></option>
                                        <?php
                                            foreach($mailHandler->folders as $value) {
                                                if($value->id != 3 && $value->id != 4) {
                                                    echo '<option value="'.$value->id.'">'.$value->title.'</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="select_search_content" class="control-label"><?php echo TranslationHandler::get_static_text("SEARCH_IN"); ?>:</label>
                                    <select id="select_search_content" name="search_content" class="form-control">
                                        <option value="1"><?php echo TranslationHandler::get_static_text("MESSAGE"); ?></option>
                                        <option value="2"><?php echo TranslationHandler::get_static_text("TITLE"); ?></option>
                                        <option value="3"><?php echo TranslationHandler::get_static_text("TITLE_MESSAGE"); ?></option>
                                    </select>
                                </div>

                            </div>

                            <div class="panel-footer clearfix">
                                <div class="pull-right">
                                    <button type="button" name="submit" id="submit_button" class="submit_search_mail btn btn-primary"><?php echo TranslationHandler::get_static_text("SEARCH"); ?> <i class="fa fa-search" style="margin-left:3px;"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php
                    }
                    ?>
                </div>
                    
                <?php
                    break;
                
                
                
                case 'show_mail':
                    if($mailHandler->get_mail($mail_id, isset($_GET["referer"]) ? true : false)) {
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
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="inbox" data-toggle="tooltip" title="Remove from important"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)"  class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="spam" data-toggle="tooltip" title="Spam"><i class="fa fa-exclamation-circle"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="trash" data-toggle="tooltip" title="Trash"><i class="fa fa-trash"></i></a>';
                                            break;

                                        case "sent":
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="delete" data-toggle="tooltip" title="Delete"><i class="fa fa-times"></i></a>';
                                            break;
                                        
                                        case "drafts":
                                            break;

                                        case "spam":
                                        echo '<a href="javascript:void(0)" data-toggle="tooltip" title="Remove from spam" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="inbox"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)" data-toggle="tooltip" title="Trash" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="trash"><i class="fa fa-trash"></i></a>';
                                            break;

                                        case 'trash':
                                        echo '<a href="javascript:void(0)" data-toggle="tooltip" title="Remove from trash" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'"  step="inbox"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="delete"><i class="fa fa-times"></i></a>';
                                            break;


                                        default:
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="important" data-toggle="tooltip" title="Bookmark"><i class="fa fa-bookmark"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="spam" data-toggle="tooltip" title="Spam"><i class="fa fa-exclamation-circle"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" mail_id="'.$current_mail->user_mail_id.'" current_folder="'.$current_mail->folder_name.'" step="trash" data-toggle="tooltip" title="Trash" name="submit"><i class="fa fa-trash"></i></a>';
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
                                        <img class="img-responsive" src="assets/images/profile_images/<?php echo $current_mail->user_image_id; ?>.png" alt="avatar">
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
                                        <p><?php echo nl2br($current_mail->text); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        if($current_mail->folder_id == 3 || !RightsHandler::has_user_right("MAIL_CREATE") || $current_mail->disable_reply) {
                            echo '
                                <div class="divid"></div>
                                <div style="text-align:center;">'. TranslationHandler::get_static_text("MAIL_CANT_RESPOND_TO_MAIL") . '</div>';
                        } else {
                        ?>
                            <div class="row reply_form">
                                <div class="col-md-12">
                                    <div class="panel panel-default new-message">
                                        <form method="POST" action="" id="create_mail_form" url="mail.php?step=create_mail" name="create_mail">
                                            <input type="hidden" name="recipiants[]" value="USER_ANY_<?php echo $current_mail->sender_id; ?>">
                                            <input name="disable_reply" class="form-control" type="hidden" value="1">
                                            <input type="hidden" name="title" value="RE: <?php echo $current_mail->title; ?>">
                                            <?php
                                            foreach($current_mail->mail_tags as $tag)
                                            {
                                                echo '<input type="hidden" name="mail_tags[]" value="'. $tag->id. '">';
                                            }
                                            ?>
                                            <div class="panel-heading text-muted" style="padding: 16px 16px 8px 16px !important;">
                                                <?php echo TranslationHandler::get_static_text("RECEIVER") . ": " . $current_mail->firstname . " " . $current_mail->surname; ?>
                                            </div>								
                                            <div class="panel-body p-0">
                                                <textarea name="message" id="new-message-body" style="padding: 8px 16px 16px 16px !important;" placeholder="<?php echo TranslationHandler::get_static_text("WRITE_YOUR_ANSWER"); ?>"></textarea>
                                            </div>
                                            <div class="panel-footer">
                                                <button type="button" name="submit" id="submit_button" class="submit_reply_mail btn btn-primary pull-right"><?php echo TranslationHandler::get_static_text("REPLY"); ?> <i class="fa fa-send" style="margin-left:3px;"></i></button>
                                                <div style="clear:both;"></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
        
                <?php
                    } else {
                        ErrorHandler::show_error_page($mailHandler->error);
                        die();
                    }
                    break;
                
                
                
                default:
                    $fetch_successful = $mailHandler->get_mails($current_page_number, $current_order, $current_filter);
                    $mails = $paginationHandler->run_pagination($mailHandler->mails, $current_page_number, SettingsHandler::get_settings()->elements_shown);
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
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=inbox" data-toggle="tooltip" title="Remove from important"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=spam" data-toggle="tooltip" title="Spam"><i class="fa fa-exclamation-circle"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=trash" data-toggle="tooltip" title="Trash"><i class="fa fa-trash"></i></a>';
                                            break;

                                        case "drafts":
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=delete" data-toggle="tooltip" title="Delete"><i class="fa fa-times"></i></a>';
                                            break;

                                        case "sent":
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=delete" data-toggle="tooltip" title="Delete"><i class="fa fa-times"></i></a>';
                                            break;

                                        case "spam":
                                        echo '<a href="javascript:void(0)" data-toggle="tooltip" title="Remove from spam" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=inbox"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)" data-toggle="tooltip" title="Trash" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=trash"><i class="fa fa-trash"></i></a>';
                                            break;

                                        case 'trash':
                                        echo '<a href="javascript:void(0)" data-toggle="tooltip" title="Remove from trash" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=inbox"><i class="fa fa-reply"></i></a>
                                                <a href="javascript:void(0)" data-toggle="tooltip" title="Delete" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=delete"><i class="fa fa-times"></i></a>';
                                            break;


                                        default:
                                        echo '<a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=important" data-toggle="tooltip" title="Bookmark"><i class="fa fa-bookmark"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=spam" data-toggle="tooltip" title="Spam"><i class="fa fa-exclamation-circle"></i></a>
                                                <a href="javascript:void(0)" class="assign_mail_folder btn btn-default" target_form="mail_form" args="?step=trash" data-toggle="tooltip" title="Trash" name="submit"><i class="fa fa-trash"></i></a>';
                                            break;
                                    }
                                    ?>
                                </div>
                                <div class="btn-group" style="float:right;margin-right: 0px !important;"  role="group">
                                    <a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter='.$current_filter.'&order='.$current_order.'&p='.$paginationHandler->get_last_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo $paginationHandler->is_first_page() ? 'disabled' : '';?>><i class="fa fa-chevron-left"></i></a>
                                    <a href="javascript:void(0)" page="mail" step="<?php echo $current_page ?>" args="<?php echo '&filter='.$current_filter.'&order='.$current_order.'&p='.$paginationHandler->get_next_page(); ?>" id="mail" class="change_page btn btn-default" <?php echo $paginationHandler->is_last_page() ? 'disabled' : '';?>><i class="fa fa-chevron-right"></i></a>
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
                                                        <div class="mail-item item_hover mail_number_'. $value->user_mail_id.' '.($current_page != "sent" && $current_page != "drafts" ? ($value->is_read ? '""' : "item_unread") : '') .'" style="height:100px;">
                                                            <div class="mail_element_checkbox checkbox-resize">
                                                                <div>
                                                                    <div class="checkbox">
                                                                        <input type="checkbox" id="checkbox-enable-reply" name="mail[]" value="'.$value->user_mail_id.'"><label for="checkbox-enable-reply"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="change_page mail_element_content" page="mail" id="mail" step="show_mail" args="&mail_id='. $value->user_mail_id .'">
                                                                <table class="mail-container"><tbody>
                                                                    <tr>

                                                                        <td class="mail-left">
                                                                            <div class="avatar avatar-lg avatar-circle" data-toggle="tooltip" title="' . $value->firstname . ' ' . $value->surname .'">
                                                                                <img src="assets/images/profile_images/'.$value->user_image_id.'.png" >
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div>
                                                                                <p class="mail-item-date" style="float:right;margin-top:-6px;">' . $date_to_string["value"] . ' ' . TranslationHandler::get_static_text($date_to_string["prefix"]) .' ' . TranslationHandler::get_static_text("DATE_AGO") . '</p>
                                                                                <div class="mail-item-header">
                                                                                    <h4 class="mail-item-title"><p class="title-color">' . $value->title .'</p></h4>';
                                                                                    foreach($value->mail_tags as $mail_tag) {
                                                                                       echo '<span class="label '. $mail_tag->color_class .'" style="margin-right: 5px !important;">'. $mail_tag->title .'</span>';
                                                                                    }
                                                                                    echo '
                                                                                </div>
                                                                                <div class="mail-item-excerpt">' . (strlen($value->text) > 85 ? substr($value->text, 0, 85) . '...' : $value->text) .'</p>
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
                                            ErrorHandler::show_error_page($mailHandler->error);
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
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/subpageGlobal.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>