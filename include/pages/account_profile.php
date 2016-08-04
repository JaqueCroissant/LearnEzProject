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
                <span class="cover-icon"><i class="fa fa-heart-o"></i></span>
            </div>
            <div>
                <div class="avatar avatar-xl avatar-circle">
                    <img class="img-responsive" src="assets/images/profile_images/<?= $current_user->image_id; ?>.png" alt="avatar">
                </div>
            </div>
            <div>
                <span class="cover-icon"><i class="fa fa-envelope-o"></i></span>
            </div>
        </div>
        <div class="text-center">
            <h4 class="profile-info-name m-b-lg"><span class="title-color"><?= $current_user->firstname . " " . $current_user->surname; ?></span></h4>
            <div>
                <span style="padding-right:10px;"><i class="zmdi-hc-fw zmdi p-r-lg zmdi-device-hub zmdi-hc-lg" style="line-height: 0.4em !important;"></i> <?= $current_user->user_type_title; ?></span>
                <span><i class="zmdi-hc-fw zmdi p-r-lg zmdi-city zmdi-hc-lg" style="line-height: 0.4em !important;"></i><?= !empty($current_user->school_id) ? $current_school->name : "LearnEZ"; ?></span>
            </div>
        </div>
    </div><!-- .profile-cover -->

    <div class="promo-footer">
        <div class="row no-gutter">
            <div class="col-sm-2 col-sm-offset-3 col-xs-6 promo-tab">
                <div class="text-center">
                    <small>Experience</small>
                    <h4 class="m-0 m-t-xs">+2 years</h4>
                </div>
            </div>
            <div class="col-sm-2 col-xs-6 promo-tab">
                <div class="text-center">
                    <small>Hourly Rate</small>
                    <h4 class="m-0 m-t-xs">12$ - 25$</h4>
                </div>
            </div>
            <div class="col-sm-2 col-xs-12 promo-tab">
                <div class="text-center">
                    <small>Reviews</small>
                    <div class="m-t-xs">
                        <i class="text-primary fa fa-star"></i>
                        <i class="text-primary fa fa-star"></i>
                        <i class="text-primary fa fa-star"></i>
                        <i class="text-primary fa fa-star"></i>
                        <i class="text-primary fa fa-star-o"></i>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- .promo-footer -->
</div>