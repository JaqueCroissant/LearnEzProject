<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/statisticsHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$classHandler->get_all_classes();
$userHandler = new UserHandler();
$statisticsHandler = new StatisticsHandler();

if ($classHandler->_user->user_type_id != 1) {
    $schoolHandler->get_school_by_id($classHandler->_user->school_id);
}

if (isset($_GET['class_id'])) {
    $classHandler->get_class_by_id($_GET['class_id']);
    $userHandler->get_by_class_id($_GET['class_id']);
    $statisticsHandler->get_average_progress_for_class($_GET['class_id']);
}
?>

<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="col-md-9 col-sm-12">
    <div class="col-sm-12 col-md-6 p-l-0">
        <div class="widget">
            <div class='widget-header'>
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("ALL") . " " . TranslationHandler::get_static_text("STUDENTS"); ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="widget-body">
                <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
                    <thead>
                        <tr>
                            <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                            <th><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        echo "Venter på en funktion fra Jake til at hente elever ud fra klasse id";
                        foreach ($userHandler->users as $value) {
                            ?>
                            <tr class = "a change_page" page = "account_profile" step = "" args = "&user_id=<?php echo $value->id; ?>">
                                <td><?php echo $value->firstname . " " . $value->surname; ?></td>
                                <td><?php echo $value->email; ?></td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-sm-12 col-md-6 p-r-0">
        <div class="widget">
            <div class='widget-header'>
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("TOP") . " xx " . TranslationHandler::get_static_text("STUDENTS") . " - Husk at tjekke om brugertype = 4"; ?> </h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="widget-body">
                <div class="streamline m-l-lg">
                    <div class="sl-item p-b-md">
                        <div class="sl-avatar avatar avatar-sm avatar-circle">
                            <img class="img-responsive" src="assets/images/profile_images/5.png">
                        </div>
                        <div class="sl-content">
                            <h5 class="m-t-0">
                                <a class="m-r-xs theme-color" href="javascript:void(0)">John Doe</a>
                            </h5>
                            <p>9001 points</p>
                        </div>
                    </div>
                    <div class="sl-item p-b-md">
                        <div class="sl-avatar avatar avatar-sm avatar-circle">
                            <img class="img-responsive" src="assets/images/profile_images/5.png">
                        </div>
                        <div class="sl-content">
                            <h5 class="m-t-0">
                                <a class="m-r-xs theme-color" href="javascript:void(0)">John Doe</a>
                                <small class="text-muted fz-sm"><-- THIS IS YOU</small>
                            </h5>
                            <p>2001 points</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 p-r-0">
        <div class="widget">
            <div class='widget-header'>
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("HOMEWORK"); ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="widget-body">

            </div>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-12">
    <div class="widget">
        <div class="widget-header">
            <?php if (RightsHandler::has_user_right("CLASS_EDIT")) { ?>
                <div class="pull-right">
                    <i class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a" page="edit_class" step="" args="&class_id=<?php echo $value->id; ?>"></i>
                </div>
            <?php } ?>
            <h4 class="widget-title"><?php echo isset($_GET['class_id']) ? $classHandler->school_class->title . " - " . $classHandler->school_class->class_year : ""; ?></h4>

        </div>
        <hr class="widget-separator m-0">
        <div class="widget-body">
            <?php echo isset($_GET['class_id']) ? $classHandler->school_class->description : ""; ?>

        </div>

        <div class="widget-body <?php echo isset($_GET['class_id']) && $classHandler->school_class->remaining_days < "10" ? ($classHandler->school_class->remaining_days == "0" ? "hidden" : "danger animated headShake" ) : ""; ?>">
            <h4 class="widget-title <?php echo isset($_GET['class_id']) && $classHandler->school_class->remaining_days < "10" ? ($classHandler->school_class->remaining_days == "0" ? "" : "animated flash animate-twice" ) : ""; ?>"><?php echo isset($_GET['class_id']) ? TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END") . ": " . $classHandler->school_class->remaining_days . " " . TranslationHandler::get_static_text("DATE_DAYS") : ""; ?></h4>
        </div>
    </div>
    <div class="widget">
        <div class='widget-header'>
            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("AVERAGE") . " " . strtolower(TranslationHandler::get_static_text("PROGRESS")); ?></h4>
        </div>
        <hr class="widget-separator m-0">
        <div class="widget-body">
            <div class="pull-left">
                <div class="pieprogress text-danger" data-value="<?php echo isset($_GET['class_id']) ? $statisticsHandler->class_average : ""; ?>" data-plugin="circleProgress" data-options='{fill: {color: "#e23636"}, thickness: 10}'>
                    <strong>%<span class="counter" data-plugin="counterUp"><?php echo isset($_GET['class_id']) ? $statisticsHandler->class_average * 100 : ""; ?></span></strong>
                </div>
            </div>
        </div>
    </div>
    <div class="widget">
        <div class='widget-header'>
            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("HOMEWORK"); ?></h4>
        </div>
        <hr class="widget-separator m-0">
        <div class="widget-body">

        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>