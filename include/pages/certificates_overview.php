<?php
require_once 'require.php';
require_once '../../include/handler/pageHandler.php';

$certificateHandler = new CertificatesHandler();
$paginationHandler = new PaginationHandler();

$current_page = isset($_GET['p']) && !empty($_GET['p']) ? $_GET['p'] : 1;
$current_filter = isset($_GET['filter']) && !empty($_GET['filter']) ? $_GET['filter'] : 0;
$current_order = isset($_GET['order']) && !empty($_GET['order']) ? $_GET['order'] : 0;

if (!$certificateHandler->get_all_certificates($current_page, $current_order, $current_filter)) {
    ErrorHandler::show_error_page($certificateHandler->error);
    die();
}


$certificates = $paginationHandler->run_pagination($certificateHandler->certificates, $current_page, SettingsHandler::get_settings()->elements_shown);
?>
<form method="POST" action="" id="certificates_form" url="download_pdf.php?step=download_multiple" name="certificate">
<div class="wait_translation hidden"><?= TranslationHandler::get_static_text("MUST_WAIT_5_SECONDS_BEFORE_DOWNLOAD"); ?></div>
<div class="row">
    <div class="col-md-12">
        
        <div class="mail-toolbar m-b-lg">
            <div class="btn-group" role="group">
                <a href="javascript:void(0)" target_form="certificates_form" class="check_all btn btn-default"><i class="fa fa-square-o"></i></a>'
            </div>

            <div class="btn-group" role="group">
                <a href="javascript:void(0)" class="download_checked_certificates btn btn-default" target_form="certificates_form" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("DOWNLOAD_PDF") ?>"><i class="fa fa-download"></i></a>
            </div>

            <div class="btn-group" style="float:right;margin-right: 0px !important;"  role="group">
                <a href="javascript:void(0)" page="certificates_overview"  args="<?php echo '&filter=' . $current_filter . '&order=' . $current_order . '&p=' . $paginationHandler->get_last_page(); ?>" id="certificates_overview" class="change_page btn btn-default" <?php echo $paginationHandler->is_first_page() ? 'disabled' : ''; ?>><i class="fa fa-chevron-left"></i></a>
                <a href="javascript:void(0)" page="certificates_overview"  args="<?php echo '&filter=' . $current_filter . '&order=' . $current_order . '&p=' . $paginationHandler->get_next_page(); ?>" id="certificates_overview" class="change_page btn btn-default" <?php echo $paginationHandler->is_last_page() ? 'disabled' : ''; ?>><i class="fa fa-chevron-right"></i></a>
            </div>

            <div class="btn-group" style="float:right;" role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("FILTER"); ?> <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="javascript:void(0)" page="certificates_overview"  args="<?php echo '&filter=0&order=' . $current_order . '&p=' . $current_page; ?>" id="certificates_overview" class="change_page"><?php echo TranslationHandler::get_static_text("ALL"); ?></a></li>
                    <li><a href="javascript:void(0)" page="certificates_overview"  args="<?php echo '&filter=1&order=' . $current_order . '&p=' . $current_page; ?>" id="certificates_overview" class="change_page"><?php echo TranslationHandler::get_static_text("INCOMPLETE_PLURAL"); ?></a></li>
                    <li><a href="javascript:void(0)" page="certificates_overview"  args="<?php echo '&filter=2&order=' . $current_order . '&p=' . $current_page; ?>" id="certificates_overview" class="change_page"><?php echo TranslationHandler::get_static_text("COMPLETE_PLURAL"); ?></a></li>
                </ul>
            </div>

            <div class="btn-group" style="float:right;"  role="group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("ORDER_BY"); ?> <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a href="javascript:void(0)" page="certificates_overview"  args="<?php echo '&filter=' . $current_filter . '&order=0&p=' . $current_page; ?>" id="certificates_overview" class="change_page"><?php echo TranslationHandler::get_static_text("NEWEST"); ?></a></li>
                    <li><a href="javascript:void(0)" page="certificates_overview"  args="<?php echo '&filter=' . $current_filter . '&order=1&p=' . $current_page; ?>" id="certificates_overview" class="change_page"><?php echo TranslationHandler::get_static_text("OLDEST"); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table mail-list"><tbody>
            <tr>
                <td>
                    <?php
                            foreach ($certificates as $value) {
                                if($value->is_completed) {
                                    $date_to_string = time_elapsed($value->completion_date);
                                }
                                echo '
                                <div class="mail-item '. ($value->is_completed ? "item_hover " : "") .'" '. ($value->is_completed ? 'element_id="'.$value->id.'"' : '') .' style="height:130px;'. ($value->is_completed ? "" : "opacity:0.3;background: #eae8e8 !important;") .'">
                                    <div style="position:absolute;height:112px !important;margin-left:120px;margin-top:-7px !important;border-right: 10px solid '. $value->course_color .';">
                                    </div>
                                    <div class="mail_element_checkbox checkbox-resize">
                                        <div>
                                            <div class="checkbox" style="margin-top: 14px !important;">
                                                <input type="checkbox" id="checkbox-enable-reply" name="certificate[]" value="' . $value->id . '" '. (!$value->is_completed ? "disabled" : "") .' ><label for="checkbox-enable-reply"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mail_element_content">
                                        <table class="mail-container"><tbody>
                                            <tr '. ($value->is_completed ? 'style="cursor:pointer;" class="download_single_certificate"' : '') .'>

                                                <td class="mail-left">
                                                    <div class="avatar avatar-lg" style="margin-top: 12px !important;width: 70px !important; height: 70px !important;">
                                                        <img src="assets/images/thumbnails/' . $value->course_image . '" >
                                                    </div>
                                                </td>
                                                <td style="padding-left:36px;">
                                                    <div>
                                                        <p class="mail-item-date" style="float:right;">' . ($value->is_completed ? ($date_to_string["value"] . ' ' . TranslationHandler::get_static_text($date_to_string["prefix"]) . ' ' . TranslationHandler::get_static_text("DATE_AGO")) : TranslationHandler::get_static_text("INCOMPLETE")) . '</p>
                                                        <div class="mail-item-header" style="float:left;margin-top: 24px !important;margin-bottom: 0px !important;">
                                                            <h4 class="mail-item-title"><p class="title-color">' . $value->course_title . '</p></h4>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                        
                                                        <div class="mail-item-excerpt" style="float:left;">' . (strlen($value->course_description) > 85 ? substr($value->course_description, 0, 85) . '...' : $value->course_description) . '</div>';
                                                    if($value->is_completed) {    
                                                        echo '
                                                        <div class="mail-item-date" style="float:right;text-align: right;margin-bottom: 3px;"><div>Validerings kode</div><div style="font-weight:600">'. $value->validation_code .'</div></div>';
                                                    }
                                                    
                                                    echo '
                                                    </div>                                                            
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>';
                            }
                    ?>
                </td>
            </tr>
        </tbody></table>
</div>
    <input type="hidden" name="submit" value="" />
    </form>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>