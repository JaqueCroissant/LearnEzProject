<?php
require_once 'require.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/mailHandler.php';

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

<div class="profile-header" style="margin: -1.5rem -1.5rem 1.5rem -1.5rem !important;">
    <div class="profile-cover">
        <div class="cover-user m-b-lg">
            <div>
                <a href="#achievements" style="color: #6a6c6f !important;"><span class="cover-icon" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("ACHIEVEMENTS") ?>" style="cursor:pointer"><i class="fa fa-star" style="font-size:16px !important;margin-left:2px;"></i></span></a>
            </div>
            <div>
                <div class="avatar avatar-xl avatar-circle">
                    <img class="img-responsive" src="assets/images/profile_images/<?= $current_user->image_id; ?>.png" alt="avatar">
                </div>
            </div>
            <div>
                <?php if(MailHandler::can_send_to_receiver($user_id)) { ?>
                    <span class="cover-icon change_page" id="mail" page="mail" step="create_mail" args="&receiver_id=USER_ANY_<?= $current_user->id; ?>" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("SEND_MAIL") ?>" style="cursor:pointer;line-height:38px !important;"><i class="fa fa-envelope" style="margin-left:1px;"></i></span>
                <?php } else { ?>
                    <span class="cover-icon"  data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("SEND_MAIL") ?>" style="cursor:not-allowed;line-height:38px !important;"><i class="fa fa-envelope" style="margin-left:1px;"></i></span>
                <?php } ?>
            </div>
        </div>
        <div class="text-center">
            <h4 class="profile-info-name m-b-lg"><span class="title-color"><?= ucwords($current_user->firstname . " " . $current_user->surname); ?></span></h4>
            <div class="text-primary">
                <span style="padding-right:10px;"><i class="zmdi-hc-fw zmdi p-r-lg zmdi-device-hub zmdi-hc-lg" style="line-height: 0.4em !important;"></i> <?= htmlentities($current_user->user_type_title); ?></span>
                <span data-toggle="tooltip" title="<?= !empty($current_user->school_id) ? htmlspecialchars($current_school->name) : "LearnEZ"; ?>"><i class="zmdi-hc-fw zmdi p-r-lg zmdi-city zmdi-hc-lg" style="line-height: 0.4em !important;"></i><?= !empty($current_user->school_id) ? (strlen(htmlspecialchars($current_school->name)) > 40 ? substr(htmlspecialchars($current_school->name), 0, 40) : htmlspecialchars($current_school->name)) : "LearnEZ"; ?></span>
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
                    
                    <h4 class="m-0 m-t-xs"><?= $current_user->last_login == 0 ? TranslationHandler::get_static_text("NEVER") : $last_login["value"] . ' ' . TranslationHandler::get_static_text($last_login["prefix"]) . ' ' . TranslationHandler::get_static_text("DATE_AGO"); ?></h4>
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

<div class="row">
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md" >
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-account zmdi-hc-lg" style="padding-right:26px;"></i><?= TranslationHandler::get_static_text("USER_DESCRIPTION") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body user-description">
                <div class="center description" ><?= empty(htmlspecialchars($current_user->description)) ? TranslationHandler::get_static_text("NO_DESCRIPTION") : htmlspecialchars(nl2br($current_user->description)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md">
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-info-outline zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("INFORMATION") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body user-information">
                <table class="profile_information_table">
                    <tr>
                        <td><?= TranslationHandler::get_static_text("NAME") ?>:</td>
                        <td style="text-align:right;"><?= ucwords(htmlspecialchars($current_user->firstname) . " " . htmlspecialchars($current_user->surname)); ?></td>
                    </tr>
                     <tr>
                        <td><?= TranslationHandler::get_static_text("USERNAME") ?>:</td>
                        <td style="text-align:right;"><?= $current_user->username; ?></td>
                    </tr>
                     <tr>
                        <td><?= TranslationHandler::get_static_text("USER_TYPE") ?>:</td>
                        <td style="text-align:right;"><?= htmlspecialchars($current_user->user_type_title); ?></td>
                    </tr>
                    <tr>
                        <td><?= TranslationHandler::get_static_text("AFFILIATION") ?>:</td>
                        <td style="text-align:right;"><?= !empty($current_user->school_id) ? (strlen(htmlspecialchars($current_school->name)) > 40 ? substr(htmlspecialchars($current_school->name), 0, 40) : htmlspecialchars($current_school->name)) : "LearnEZ " . strtolower(TranslationHandler::get_static_text("STAFF")); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row" id="achievements">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md" >
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-star zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("ACHIEVEMENTS") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body user-achievements">
                <div class="center achievements-text" style="margin-top: 20px; margin-bottom: 20px;"><?= TranslationHandler::get_static_text("NO_ACHIEVEMENTS") ?></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading p-h-lg p-v-md" >
                <h4 class="panel-title" style="text-transform: none !important;"><i class="zmdi-hc-fw zmdi zmdi-trending-up zmdi-hc-lg" style="padding-right:30px;"></i><?= TranslationHandler::get_static_text("COURSE_PROGRESS") ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="panel-body user-progress">
                <div data-plugin="chart" data-options="{
                    tooltip : {
                        trigger: 'axis'
                    },
                    legend: {
                    data:['<?= TranslationHandler::get_static_text("LECTURES") ?>','<?= TranslationHandler::get_static_text("TESTS") ?>']
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type : 'category',
                            boundaryGap : false,
                            data : [
                            <?php
                            for($i = date('m')-4; $i < date('m')+1; $i++) {
                                echo "'" . TranslationHandler::get_static_text(strtoupper(month_num_to_string($i))) . "'";
                                if($i != date('m')) {
                                    echo ",";
                                }
                            }
                            ?>
                            ]
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series : [
                        {
                            name:'<?= TranslationHandler::get_static_text("LECTURES") ?>',
                            type:'line',
                            smooth:true,
                            itemStyle: {normal: {areaStyle: {type: 'default'}}},
                            data:[10, 12, 21, 54, 260]
                        },
                        {
                            name:'<?= TranslationHandler::get_static_text("TESTS") ?>',
                            type:'line',
                            smooth:true,
                            itemStyle: {normal: {areaStyle: {type: 'default'}}},
                            data:[30, 182, 434, 791, 390]
                        }
                    ]
                    }" style="height: 300px;"></div>
                <!--<div class="center progress-text" style="margin-top: 20px; margin-bottom: 20px;"><?= TranslationHandler::get_static_text("NO_COURSE_PROGRESS") ?></div>-->
            </div>
        </div>
    </div>
</div>
    
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    if($(".user-description").height() > $(".user-information").height()) {
        $(".user-information").height($(".user-description").height());
    } else {
        var padding = Math.floor(($(".user-information").height() - $('.description').height()) / 2);
        $('.description').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
        $(".user-description").height($(".user-information").height());
    }
    
    if($(".user-achievements").height() > $(".user-progress").height()) {
        if($('.progress-text').length) {
            var padding = Math.floor(($(".user-achievements").height() - $('.progress-text').height()) / 2);
            $('.progress-text').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
        }
        $(".user-progress").height($(".user-achievements").height());
    } else {
        if($('.achievements-text').length) {
            var padding = Math.floor(($(".user-progress").height() - $('.achievements-text').height()) / 2);
            $('.achievements-text').attr("style", "padding-top: " + padding + "px;padding-bottom:" + padding + "px");
        }
        $(".user-achievements").height($(".user-progress").height());
    }
});
</script>