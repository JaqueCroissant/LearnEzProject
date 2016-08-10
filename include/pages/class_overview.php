<?php
$begin = microtime(true);
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$classHandler->get_all_classes();
if ($classHandler->_user->user_type_id != 1) {
    $schoolHandler->get_school_by_id($classHandler->_user->school_id);
    $targets = RightsHandler::has_user_right("CLASS_ASSIGN_USER") ? ", targets: [4]" : "";
    $classHandler->get_soon_expiring_classes($classHandler->_user->school_id);
} else {
    $targets = RightsHandler::has_user_right("CLASS_ASSIGN_USER") ? ", targets: [5]" : "";
    $classHandler->get_soon_expiring_classes();
}
?>

<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="row">
    <div class="<?php echo $classHandler->_user->user_type_id != "1" ? "col-md-9" : "" ?> col-sm-12">
        <div class="row">
            <div class="col-sm-12">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("OPEN_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?></h4>
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">
                        <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5, columnDefs:[{orderable: false<?= $targets ?>}]}">
                            <thead>
                                <tr>
                                    <th><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                                    <?php if ($classHandler->_user->user_type_id == "1") { ?>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                    <?php } ?>
                                    <th><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("TEACHERS"); ?></th>
                                    <?php if (RightsHandler::has_user_right("CLASS_ASSIGN_USER")) { ?>
                                        <th class="center"><?php echo TranslationHandler::get_static_text("ADD") . " " . strtolower(TranslationHandler::get_static_text("STUDENT")); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($classHandler->classes as $value) {
                                    if ($value->open == "1") {
                                        ?>
                                        <tr>
                                            <td class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo $value->title; ?></td>
                                            <?php if ($classHandler->_user->user_type_id == "1") { ?>
                                                <td class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo $value->school_name; ?></td>
                                            <?php } ?>
                                            <td class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo $value->class_year; ?></td>
                                            <td class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo $value->number_of_students; ?></td>
                                            <td class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo $value->number_of_teachers; ?></td>
                                            <?php if (RightsHandler::has_user_right("CLASS_ASSIGN_USER")) { ?>
                                                <td>
                                                    <div class="center">
                                                        <i class="zmdi zmdi-hc-lg zmdi-plus a" class_id="<?php echo $value->id; ?>" data-toggle="tooltip" title="<?php echo TranslationHandler::get_static_text("ADD") . " " . strtolower(TranslationHandler::get_static_text("STUDENT")); ?>"></i>
                                                    </div>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php
            switch ($classHandler->_user->user_type_id) {
                case "1":

                    break;
                case "2":
                    ?>
                    

                    <?php
                    break;
                case "3": case "4":

                    break;
            }
            ?>
        </div>
    </div>
    <div class="col-sm-12 col-md-3">
        <?php if ($classHandler->_user->user_type_id != "1") { ?>
            <div class="widget">
                <div class="widget-header">
                    <h4 class="widget-title">Shortcuts and general information</h4>
                </div>
                <hr class="widget-separator">
                <div class="widget-body">
                    <div class="col-sm-10">
                        <h4><?php echo $schoolHandler->school->name; ?></h4>
                        <h6 class="text-muted"><?php echo $schoolHandler->school->address; ?></h6>
                        <h6 class="text-muted"><?php echo $schoolHandler->school->zip_code . " " . $schoolHandler->school->city; ?></h6>
                    </div>
                    <div class="col-sm-2">
                        <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                <i class="zmdi zmdi-hc-lg zmdi-edit change_page a m-t-sm" page="edit_school" step="" args="&school_id=<?php echo $value->id; ?>" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT") ?>"></i>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($classHandler->_user->user_type_id != 4 && $classHandler->_user->user_type_id != 1) { ?>
            <div class="widget">
                <div class='widget-header'>
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("SOON_EXPIRING"); ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="widget-body">
                    <div class="streamline m-l-lg">
                        <?php foreach ($classHandler->soon_expiring_classes as $value) { ?>
                            <div class="sl-item <?php echo $value->remaining_days <= 31 ? "sl-danger" : "sl-primary" ?> p-b-md">
                                <div class="sl-content">
                                    <div class="m-t-0 change_page a <?php echo $value->remaining_days <= 31 ? "text-danger animate-twice animated headShake" : "text-primary" ?>" page='class_profile' step='' args='&class_id=<?php echo $value->id; ?>'>
                                        <?php echo $value->title; ?>
                                    </div>
                                    <p class="text-muted"><?php echo $value->remaining_days . " " . strtolower(TranslationHandler::get_static_text("DAYS_REMAINING")); ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <?php } ?>
        <?php if ($classHandler->_user->user_type_id != 1 && $classHandler->_user->user_type_id != 2) { ?>
            <div class="widget">
                <div class='widget-header'>
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("HOMEWORK"); ?></h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="widget-body">

                </div>
            </div>
        <?php } ?>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
