<?php
$begin = microtime(true);
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/homeworkHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$homeworkHandler = new HomeworkHandler();
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
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title no-transform">
                            <i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg m-r-md"></i>
                            <?php echo TranslationHandler::get_static_text("OPEN_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?>
                        </h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="panel-body">
                        <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5, columnDefs:[{orderable: false<?= $targets ?>}]}">
                            <thead>
                                <tr>
                                    <th><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                                    <?php if ($classHandler->_user->user_type_id == "1") { ?>
                                        <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                    <?php } ?>
                                    <th class="center"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                    <th class="center"><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                    <th class="center"><?php echo TranslationHandler::get_static_text("TEACHERS"); ?></th>
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
                                            <td class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo htmlspecialchars($value->title); ?></td>
                                            <?php if ($classHandler->_user->user_type_id == "1") { ?>
                                            <td class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo htmlspecialchars($value->school_name); ?></td>
                                            <?php } ?>
                                            <td class="center"  class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo htmlspecialchars($value->class_year); ?></td>
                                            <td class="center" class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo $value->number_of_students; ?></td>
                                            <td class="center" class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>"><?php echo $value->number_of_teachers; ?></td>
                                            <?php if (RightsHandler::has_user_right("CLASS_ASSIGN_USER")) { ?>
                                                <td>
                                                    <div class="center">
                                                        <i class="zmdi zmdi-hc-lg zmdi-plus a change_page" page="add_class_students" args="&school_id=<?php echo $value->school_id;?>&class_id=<?php echo $value->id;?>" data-toggle="tooltip" title="<?php echo TranslationHandler::get_static_text("ADD") . " " . strtolower(TranslationHandler::get_static_text("STUDENT")); ?>"></i>
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
                    $homeworkHandler->get_user_homework();
                    ?>
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class='panel-heading'>
                                <h4 class="panel-title no-transform a change_page" page="homework_overview" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("HOMEWORK_OVERVIEW") ?>">
                                    <i class="zmdi-hc-fw zmdi zmdi-assignment zmdi-hc-lg m-r-md"></i>
                                    <?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK")); ?></h4>
                            </div>
                            <hr class="widget-separator m-0">
                            <div class="panel-body">
                                <div class="panel-body">
                                    <?php
                                    if (empty($homeworkHandler->homework)) {
                                        echo '<div class="center latest-homework-empty m-h-md"><?php echo TranslationHandler::get_static_text("CLASS_NO_HOMEWORK_AT_THE_MOMENT"); ?></div>';
                                    } else {
                                        ?>
                                        <div class="latest-homework">
                                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4,5]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json" : "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                                <thead>
                                                    <tr>
                                                        <th><?php echo TranslationHandler::get_static_text("TITLE") ?></th>
                                                        <th><?php echo TranslationHandler::get_static_text("CLASSES") ?></th>
                                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("END") . " " . TranslationHandler::get_static_text("DATE_DATE") ?></th>
                                                        <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("LECTURES") ?></th>
                                                        <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("TESTS") ?></th>
                                                        <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("STATUS") ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($homeworkHandler->homework as $value) {
                                                        $classes = "";
                                                        for ($i = 0; $i < count($value->classes); $i++) {
                                                            $classes .= $value->classes[$i]->title;
                                                            $classes .= $i != count($value->classes) - 1 ? ", " : "";
                                                        }
                                                        ?>
                                                        <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                        <?php
                                                        if (!empty($value->lectures)) {
                                                            echo '<b>Lektioner:</b>';
                                                            foreach ($value->lectures as $lecture) {
                                                                echo '<br />- ' . htmlspecialchars($lecture->title) . '';
                                                            }
                                                            echo '<br />';
                                                        }

                                                        if (!empty($value->tests)) {
                                                            echo '<b>Tests:</b>';
                                                            foreach ($value->tests as $test) {
                                                                echo '<br />- ' . htmlspecialchars($test->title) . '';
                                                            }
                                                        }
                                                        ?>">
                                                            <td><?php echo htmlspecialchars($value->title); ?></td>
                                                            <td><span data-toggle="tooltip" title="<?= htmlspecialchars($classes) ?>"><?= strlen(htmlspecialchars($classes)) > 30 ? substr(htmlspecialchars($classes), 0, 30) . "..." : htmlspecialchars($classes) ?></span></td>
                                                            <td style="text-align: center;"><?php echo $value->date_expire; ?></td>
                                                            <td style='text-align:center;'><?= count($value->lectures) ?></td>
                                                            <td style='text-align:center;'><?= count($value->tests) ?></td>
                                                            <td style='text-align:center;'><?= !$value->is_complete ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="Ufuldendt"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="Udført"></i>' ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    break;
            }
            ?>
        </div>
    </div>
    <div class="col-sm-12 col-md-3">
        <?php if ($classHandler->_user->user_type_id != "1") { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="panel-title no-transform">
                                <i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg m-r-md"></i>
                                <?php echo $schoolHandler->school->name; ?>
                            </h4>
                        </div>
                        <div class="col-md-4">
                            <?php if (RightsHandler::has_user_right("SCHOOL_CLOSE")) { ?>
                                <i class="zmdi zmdi-hc-lg zmdi-close-circle m-r-sm btn_alertbox a pull-right" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("CLOSE") ?>">
                                    <form method="post" id="alert_form_<?php echo $schoolHandler->school->id; ?>" action="" url="edit_school.php?state=set_availability">
                                        <input type="hidden" name="school_id" value="<?php echo $value->id; ?>">
                                        <input type="hidden" name="submit" value="submit">
                                    </form>
                                </i>
                            <?php } ?>
                            <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                <i class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a pull-right" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT") ?>" page="edit_school" step="" args="&school_id=<?php echo $classHandler->school_class->id; ?>"></i>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <hr class="widget-separator m-0">
                <div class="panel-body">
                    <h6 class="text-muted"><?php echo htmlspecialchars($schoolHandler->school->address); ?></h6>
                    <h6 class="text-muted"><?php echo htmlspecialchars($schoolHandler->school->zip_code) . " " . htmlspecialchars($schoolHandler->school->city); ?></h6>
                </div>
            </div>
        <?php } ?>
        <?php if ($classHandler->_user->user_type_id != 4 && $classHandler->_user->user_type_id != 1) { ?>
            <div class="panel panel-default">
                <div class='panel-heading'>
                    <h4 class="panel-title no-transform">
                        <i class="zmdi-hc-fw zmdi zmdi-calendar-note zmdi-hc-lg m-r-md"></i>
                        <?php echo TranslationHandler::get_static_text("SOON_EXPIRING"); ?>
                    </h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="panel-body">
                    <div class="streamline m-l-lg">
                        <?php foreach ($classHandler->soon_expiring_classes as $value) { ?>
                            <div class="sl-item <?php echo $value->remaining_days <= 31 ? "sl-danger" : "sl-primary" ?> p-b-md">
                                <div class="sl-content">
                                    <div class="m-t-0 change_page a <?php echo $value->remaining_days <= 31 ? "text-danger animate-twice animated headShake" : "text-primary" ?>" page='class_profile' step='' args='&class_id=<?php echo $value->id; ?>'>
                                        <?php echo htmlspecialchars($value->title); ?>
                                    </div>
                                    <p class="text-muted"><?php echo $value->remaining_days == 0 ? TranslationHandler::get_static_text("TODAY") : $value->remaining_days == 1 ? TranslationHandler::get_static_text("TOMORROW") : $value->remaining_days . " " . strtolower(TranslationHandler::get_static_text("DAYS_REMAINING")); ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
