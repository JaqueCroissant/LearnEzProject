<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$classHandler->get_all_classes();
if ($classHandler->_user->user_type_id != 1) {
    ?>
    <script>
        change_page("school_profile", "", "&school_id=<?php echo $classHandler->_user->school_id; ?>");
    </script>
    <?php
    die();
} elseif ($classHandler->_user->user_type_id = 1) {
    $schoolHandler->get_all_schools(true);
}
$targets = RightsHandler::has_user_right("SCHOOL_EDIT") ? ", targets: [5]" : "";
?>

<?php
switch ($classHandler->_user->user_type_id) {
    case "1":
        $schoolHandler->get_soon_expiring_schools();
        ?>
        <div class="row">
            <div class="col-md-9 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title no-transform">
                            <i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg m-r-md"></i>
                            <?php echo TranslationHandler::get_static_text("OPEN_P") . " " . strtolower(TranslationHandler::get_static_text("SCHOOLS")); ?>
                        </h4>
                    </div>
                    <hr class="widget-separator m-0">
                    <div class="panel-body">
                        <table id="classes" class="table display table-hover" data-options="{pageLength: 5, columnDefs:[{orderable: false<?= $targets ?>}]}" data-plugin="DataTable">
                            <thead>
                                <tr>
                                    <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("CITY"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("START"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("END"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("ACCOUNTS"); ?></th>
                                    <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                        <th class="center"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($schoolHandler->all_schools as $value) {
                                    if ($value->open == "1") {
                                        ?>
                                        <tr>
                                            <td class="change_page a" page="school_profile" step="" args="&school_id=<?php echo $value->id ?>"><?php echo strlen($value->name) < 23 ? $value->name : substr($value->name, 0, 22); ?></td>
                                            <td class="change_page a" page="school_profile" step="" args="&school_id=<?php echo $value->id ?>"><?php echo strlen($value->city) < 16 ? $value->city : substr($value->city, 0, 15); ?></td>
                                            <td class="change_page a" page="school_profile" step="" args="&school_id=<?php echo $value->id ?>"><?php echo $value->subscription_start; ?></td>
                                            <td class="change_page a" page="school_profile" step="" args="&school_id=<?php echo $value->id ?>"><?php echo $value->subscription_end; ?></td>
                                            <td class="change_page a" page="school_profile" step="" args="&school_id=<?php echo $value->id ?>"><?php echo $value->current_students . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $value->max_students; ?></td>
                                            <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                                <td>
                                                    <div class="center">
                                                        <i class="zmdi zmdi-hc-lg zmdi-edit change_page a" page="edit_school" step="" args="&school_id=<?php echo $value->id; ?>" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("SCHOOL_EDIT") ?>"></i>
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
            <div class="col-md-3 col-sm-12">
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
                            <?php foreach ($schoolHandler->soon_expiring_schools as $value) { ?>
                                <div class="sl-item <?php echo $value->remaining_days <= 30 ? "sl-danger" : "sl-primary" ?> p-b-md">
                                    <div class="sl-content">
                                        <div class="m-t-0 change_page a <?php echo $value->remaining_days <= 30 ? "text-danger animate-twice animated headShake" : "text-primary" ?>" page='school_profile' step='' args='&school_id=<?php echo $value->id; ?>'>
                                            <?php echo $value->name; ?>
                                        </div>
                                        <p class="text-muted"><?php echo $value->remaining_days == 0 ? TranslationHandler::get_static_text("TODAY") : $value->remaining_days == 1 ? TranslationHandler::get_static_text("TOMORROW") : $value->remaining_days . " " . strtolower(TranslationHandler::get_static_text("DAYS_REMAINING")); ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case "2":
        ?>
        <div class="row">
            <div class="col-md-9 col-sm-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title no-transform">
                                    <i class="zmdi-hc-fw zmdi zmdi-library zmdi-hc-lg m-r-md"></i>
                                    <?php echo TranslationHandler::get_static_text("OPEN_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?>
                                </h4>
                            </div>
                            <hr class="widget-separator m-0">
                            <div class="panel-body">
                                <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
                                    <thead>
                                        <tr>
                                            <th><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                                            <?php if ($classHandler->_user->user_type_id == "1") { ?>
                                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                            <?php } ?>
                                            <th><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                            <th><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                            <th><?php echo TranslationHandler::get_static_text("TEACHERS"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($classHandler->classes as $value) {
                                            if ($value->open == "1") {
                                                ?>
                                                <tr>
                                                    <td><?php echo $value->title; ?></td>
                                                    <?php if ($classHandler->_user->user_type_id == "1") { ?>
                                                        <td><?php echo $value->school_name; ?></td>
                                                    <?php } ?>
                                                    <td><?php echo $value->class_year; ?></td>
                                                    <td><?php echo $value->number_of_students; ?></td>
                                                    <td><?php echo $value->number_of_teachers; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title no-transform">
                                    <i class="zmdi-hc-fw zmdi zmdi-accounts zmdi-hc-lg m-r-md"></i>
                                    <?php echo TranslationHandler::get_static_text("STUDENTS"); ?>
                                </h4>
                            </div>
                            <hr class="widget-separator m-0">
                            <div class="panel-body">
                                <table id="students" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
                                    <thead>
                                        <tr>
                                            <th><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                                            <?php if ($classHandler->_user->user_type_id == "1") { ?>
                                                <th><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></th>
                                            <?php } ?>
                                            <th><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                            <th><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                            <th><?php echo TranslationHandler::get_static_text("TEACHERS"); ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title no-transform change_page a" page='school_profile' step='' args='&school_id=<?php echo $classHandler->_user->school_id; ?>'>
                                    <i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg m-r-md"></i>
                                    <?php echo $schoolHandler->school->name; ?>
                                </h4>
                            </div>
                            <hr class="widget-separator m-0">
                            <div class="panel-body">
                                <div class="col-sm-10">
                                    <h6 class="text-muted"><?php echo $schoolHandler->school->address; ?></h6>
                                    <h6 class="text-muted"><?php echo $schoolHandler->school->zip_code . " " . $schoolHandler->school->city; ?></h6>
                                </div>
                                <div class="col-sm-2">
                                    <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                        <div class="p-t-xs">
                                            <i class = "fa fa-edit fa-fw fa-2x edit_school m-r-md a" school_id="<?php echo $schoolHandler->school->id; ?>"></i>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        break;
    case "3": case "4":
        ?>
        <div class="row">
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title no-transform change_page a" page='school_profile' step='' args='&school_id=<?php echo $classHandler->_user->school_id; ?>'>
                                    <i class="zmdi-hc-fw zmdi zmdi-city zmdi-hc-lg m-r-md"></i>
                                    <?php echo $schoolHandler->school->name; ?>
                                </h4>
                            </div>
                            <hr class="widget-separator m-0">
                            <div class="panel-body">
                                <div class="col-sm-10">
                                    <h6 class='text-muted'><?php echo $schoolHandler->school->address; ?></h6>
                                    <h6 class='text-muted'><?php echo $schoolHandler->school->zip_code . ' ' . $schoolHandler->school->city; ?></h6>
                                </div>
                                <div class="col-sm-2">
                                    <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                                        <div class="p-t-xs">
                                            <i class = "fa fa-edit fa-fw fa-2x edit_school m-r-md a" school_id="<?php echo $schoolHandler->school->id; ?>"></i>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
}
?>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>$(document).ready(function(){$("[data-toggle='tooltip']").tooltip()});</script>
