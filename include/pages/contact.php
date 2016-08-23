<?php

require_once 'require.php';
require_once '../../include/handler/contactHandler.php';

$contactHandler = new ContactHandler();

if($contactHandler->is_logged_in())
{
?>
    <div class="row">
        <div class="col-md-12 ">
            <div class="widget main_login">
                <div class="widget-header">
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CONTACT"); ?></h4>
                </div>
                <hr class="widget-separator">
                <div class="widget-body">
                    <form method="POST" action="" id="contact_submit_info" url="contact.php?step=login" class="form-horizontal" name="reset_pass_submit_email">

                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("NAME"); ?></label>
                            <div class="col-sm-4">
                                <input disabled class="form-control contact_name" type="text" name="name" placeholder="<?php echo $contactHandler->_user->firstname . " " . $contactHandler->_user->surname; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?></label>
                            <div class="col-sm-4">
                                <input disabled class="form-control contact_email" type="text" name="email" placeholder="<?php echo $contactHandler->_user->email; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("CONTACT_CONTEXT"); ?></label>
                            <div class="col-sm-4">
                                <select name="context" class="form-control" data-plugin="select2">
                                    <option value="1"><?= TranslationHandler::get_static_text("TECH_ISSUE"); ?></option>
                                    <option value="2"><?= TranslationHandler::get_static_text("ACCOUNT_ISSUE"); ?></option>
                                    <option value="3"><?= TranslationHandler::get_static_text("OTHER_ISSUE"); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("SUBJECT"); ?></label>
                            <div class="col-sm-4">
                                <input class="form-control contact_subject" type="text" name="subject" placeholder="<?php echo TranslationHandler::get_static_text("SUBJECT"); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("MESSAGE"); ?></label>
                            <div class="col-sm-4">
                                <textarea rows="4" cols="50" class="form-control contact_message" name="message" placeholder="<?php echo TranslationHandler::get_static_text("MESSAGE"); ?>"></textarea>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-sm-2 col-sm-offset-4 m-b-sm">
                            <input type="reset" name="submit" value="<?= TranslationHandler::get_static_text("CLEAR") ?>" cleartype="login" class="btn btn-default contact_btn_clear" style="width:100%;">
                        </div>
                        
                        <div class="col-sm-2  m-b-sm">
                            <input type="button" name="submit" value="<?= TranslationHandler::get_static_text("INFO_SUBMIT") ?>" class="btn btn-default contact_submit_info" style="width:100%;">
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}
else
{
?>
    <div class="row">
        <div class="col-md-12 ">
            <div class="widget main_login">
                <div class="widget-header">
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CONTACT"); ?></h4>
                </div>
                <hr class="widget-separator">
                <div class="widget-body">
                    <form method="POST" action="" id="contact_submit_no_login_form" url="contact.php?step=nologin" class="form-horizontal" name="contact_submit_no_login">

                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("NAME"); ?></label>
                            <div class="col-sm-4">
                                <input class="form-control contact_name" type="text" name="name" placeholder="<?php echo TranslationHandler::get_static_text("NAME"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?></label>
                            <div class="col-sm-4">
                                <input class="form-control contact_email" type="text" name="email" placeholder="<?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("CONTACT_CONTEXT"); ?></label>
                            <div class="col-sm-4">
                                <select name="context" class="form-control" data-plugin="select2">
                                    <option value="1"><?= TranslationHandler::get_static_text("TECH_ISSUE"); ?></option>
                                    <option value="2"><?= TranslationHandler::get_static_text("ACCOUNT_ISSUE"); ?></option>
                                    <option value="3"><?= TranslationHandler::get_static_text("OTHER_ISSUE"); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("SUBJECT"); ?></label>
                            <div class="col-sm-4">
                                <input class="form-control reset_pass_email contact_subject" type="text" name="subject" placeholder="<?php echo TranslationHandler::get_static_text("SUBJECT"); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("MESSAGE"); ?></label>
                            <div class="col-sm-4">
                                <textarea rows="4" cols="50" class="form-control contact_message" name="message" placeholder="<?php echo TranslationHandler::get_static_text("MESSAGE"); ?>"></textarea>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-sm-2 col-sm-offset-4 m-b-sm">
                            <input type="reset" name="submit" value="<?= TranslationHandler::get_static_text("CLEAR") ?>" cleartype="nologin" class="btn btn-default contact_btn_clear" style="width:100%;">
                        </div>
                        
                        <div class="col-sm-2  m-b-sm">
                            <input type="button" name="submit" value="<?= TranslationHandler::get_static_text("INFO_SUBMIT") ?>" class="btn btn-default contact_submit_info" style="width:100%;">
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

<script src="assets/js/include_app.js" type="text/javascript"></script>
