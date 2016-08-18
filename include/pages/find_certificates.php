<?php
require_once 'require.php';
    
?>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("FIND_CERTIFICATE"); ?></h4>
            </div>
            <hr class="widget-separator">
            <div class="widget-body center">
                <form method="POST" id="find_certificate" url="certificate.php?step=find_certificate" name="find_certificate">
                    <div class="row certificate_info">
                        <span><p><?= TranslationHandler::get_static_text("CERTIFICATE_INFO") ?></p></span>
                    </div>
                    <div class="certificate_form_top row">
                        <input type="text" name="1" class="certificate_input form-control">
                        <span class="certificate_input_span">-</span>
                        <input type="text" name="2" class="certificate_input form-control">
                        <span class="certificate_input_span">-</span>
                        <input type="text" name="3" class="certificate_input form-control">
                        <span class="certificate_input_span">-</span>
                        <input type="text" name="4" class="certificate_input form-control">
                        <span class="certificate_input_span">-</span>
                        <input type="text" name="5" class="certificate_input form-control" maxlength="4">
                    </div>
                    <div class="certificate_form_bottom row m-t-sm">
                        <input type="button" name="submit" value="<?= TranslationHandler::get_static_text("CLEAR") ?>" class="certificate_reset btn btn-default" style="width:188px;vertical-align:initial !important;">
                        <input type="button" name="submit" value="<?= TranslationHandler::get_static_text("FIND") ?>" class="certificate_submit btn btn-default m-l-sm" style="width:188px;vertical-align:initial !important;">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive certificate_item" style="display:none;">
    <table class="table mail-list"><tbody>
            <tr>
                <td>
                    <div class="mail-item" style="height:130px;">
                        <div class="certificate_color" style="position:absolute;height:112px !important;margin-left:100px;margin-top:-7px !important;border-right: 10px solid black;">
                        </div>
                        <div class="">
                            <table class="mail-container"><tbody>
                                <tr>

                                    <td class="mail-left">
                                        <div class="avatar avatar-lg" style="margin-top: 12px !important;width: 70px !important; height: 70px !important;">
                                            <img class="certificate_image" src="" >
                                        </div>
                                    </td>
                                    <td style="padding-left:36px;">
                                        <div>
                                            <div class="pull-right">
                                                <p class="mail-item-date certificate_doneby" style="font-weight: 600;text-align: right !important;"></p>
                                                <div class="mail-item-date certificate_date" style="text-align: right !important;"></div>
                                            </div>
                                            
                                            <div class="mail-item-header">
                                                <h4 class="mail-item-title"><p class="title-color certificate_title"></p></h4>
                                            </div>
                                            <div class="mail-item-excerpt certificate_description"></div>
                                            
                                        </div>                                                            
                                    </td>
                                </tr>
                            </tbody></table>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>