<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$userHandler = new UserHandler();

if (isset($_GET['school_id'])) {
    if ($schoolHandler->_user->user_type_id == 1) {
        $schoolHandler->get_school_by_id($_GET['school_id']);
    } else {
        $schoolHandler->get_school_by_id($schoolHandler->_user->school_id);
    }
    $userHandler->get_by_school_id($_GET['school_id']);
}
?>
<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="row">
    <div class="col-md-9 col-sm-12">
        <div class="row">


            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            <?php echo TranslationHandler::get_static_text("ALL") . " " . TranslationHandler::get_static_text("STUDENTS"); ?>
                            -
                            <?php echo isset($_GET['school_id']) ? $schoolHandler->school->current_students . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $schoolHandler->school->max_students : ""; ?>
                        </h4>
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
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            loollllll
                        </h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="widget-body">

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            loollllll
                        </h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="widget-body">

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title">
                            loollllll
                        </h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="widget-body">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-12">
        <div class="widget">
            <div class="widget-header">
                <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                    <div class="pull-right">
                        <i class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a" page="edit_school" step="" args="&school_id=<?php echo $schoolHandler->school->id; ?>"></i>
                    </div>
                <?php } ?>
                <h4 class="widget-title"><?php echo isset($_GET['school_id']) ? $schoolHandler->school->name : ""; ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="widget-body">
                <?php echo isset($_GET['school_id']) ? $schoolHandler->school->address : ""; ?>
                <br/>
                <?php echo isset($_GET['school_id']) ? $schoolHandler->school->zip_code : ""; ?>
                <?php echo isset($_GET['school_id']) ? $schoolHandler->school->city : ""; ?>
                <br/>
                <br/>
                <p>
                    <?php echo TranslationHandler::get_static_text("STUDENTS") . ": "; ?>
                    <?php echo isset($_GET['school_id']) ? $schoolHandler->school->current_students . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $schoolHandler->school->max_students : ""; ?>
                </p>

            </div>
            <div class="widget-body <?php echo isset($_GET['school_id']) && $schoolHandler->school->remaining_days < "10" ? ($schoolHandler->school->remaining_days == "0" ? "hidden" : "danger animated headShake" ) : ""; ?>">
                <h4 class="widget-title <?php echo isset($_GET['school_id']) && $schoolHandler->school->remaining_days < "10" ? ($schoolHandler->school->remaining_days == "0" ? "" : "animated flash animate-twice" ) : ""; ?>"><?php echo isset($_GET['school_id']) ? TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END") . ": " . $schoolHandler->school->remaining_days . " " . TranslationHandler::get_static_text("DATE_DAYS") : ""; ?></h4>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>