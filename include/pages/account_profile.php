<?php
require_once 'require.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/schoolHandler.php';

$userHandler = new UserHandler();
$schoolHandler = new SchoolHandler();

$user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : null;

if(!$userHandler->get_user_by_id($user_id)) {
    ErrorHandler::show_error_page();
    die();
}

$current_user = $userHandler->temp_user;
if(!empty($current_user->school_id)) {
    $schoolHandler->get_school_by_id($current_user->school_id);
    $current_school = $schoolHandler->school;
};
?>

<div class="profile-header">
    <div class="profile-cover">
        <div class="cover-user m-b-lg">
            <div>
                <span class="cover-icon" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("ACHIEVEMENTS") ?>" style="cursor:pointer"><i class="fa fa-star" style="font-size:16px !important;margin-left:2px;"></i></span>
            </div>
            <div>
                <div class="avatar avatar-xl avatar-circle">
                    <img class="img-responsive" src="assets/images/profile_images/<?= $current_user->image_id; ?>.png" alt="avatar">
                </div>
            </div>
            <div>
                <span class="cover-icon" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("SEND_MAIL") ?>" style="cursor:pointer;line-height:38px !important;"><i class="fa fa-envelope" style="margin-left:1px;"></i></span>
            </div>
        </div>
        <div class="text-center">
            <h4 class="profile-info-name m-b-lg"><span class="title-color"><?= ucwords($current_user->firstname . " " . $current_user->surname); ?></span></h4>
            <div class="text-primary">
                <span style="padding-right:10px;"><i class="zmdi-hc-fw zmdi p-r-lg zmdi-device-hub zmdi-hc-lg" style="line-height: 0.4em !important;"></i> <?= $current_user->user_type_title; ?></span>
                <span data-toggle="tooltip" title="<?= !empty($current_user->school_id) ? $current_school->name : "LearnEZ"; ?>"><i class="zmdi-hc-fw zmdi p-r-lg zmdi-city zmdi-hc-lg" style="line-height: 0.4em !important;"></i><?= !empty($current_user->school_id) ? (strlen($current_school->name) > 40 ? substr($current_school->name, 0, 40) : $current_school->name) : "LearnEZ"; ?></span>
            </div>
        </div>
    </div>

    <div class="promo-footer">
        <div class="row no-gutter">
            <div class="col-sm-2 col-sm-offset-3 col-xs-6 promo-tab">
                <div class="text-center">
                    <small>Konto oprettet</small>
                    <?php $date_created = time_elapsed($current_user->time_created); ?>
                    <h4 class="m-0 m-t-xs"><?= $date_created["value"] . ' ' . TranslationHandler::get_static_text($date_created["prefix"]) . ' ' . TranslationHandler::get_static_text("DATE_AGO"); ?></h4>
                </div>
            </div>
            <div class="col-sm-2 col-xs-6 promo-tab">
                <div class="text-center">
                    <small>Seneste login</small>
                    <?php $last_login = time_elapsed($current_user->last_login); ?>
                    <h4 class="m-0 m-t-xs"><?= $last_login["value"] . ' ' . TranslationHandler::get_static_text($last_login["prefix"]) . ' ' . TranslationHandler::get_static_text("DATE_AGO"); ?></h4>
                </div>
            </div>
            <div class="col-sm-2 col-xs-12 promo-tab">
                <div class="text-center">
                    <small>Konto status</small>
                    <h4 class="m-0 m-t-xs" style="color: <?= $current_user->open ? '#36ce1c' : '#f15530'; ?>"><?= $current_user->open ? TranslationHandler::get_static_text("OPEN") : TranslationHandler::get_static_text("CLOSED"); ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>