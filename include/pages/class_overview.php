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
}
?>

<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="row">
    <div class="col-md-9 col-sm-12 p-v-0">
        <div class="col-sm-12">
            <div class="widget">
                <div class="widget-header">
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("OPEN_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?></h4>
                </div>
                <hr class="widget-separator">
                <div class="widget-body">
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
                                    <tr class="a change_page" page="class_profile" step="" args="&class_id=<?php echo $value->id ?>">
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
        </div>
        <?php
        switch ($classHandler->_user->user_type_id) {
            case "1":

                break;
            case "2":
                ?>
                <div class="col-sm-12">
                    <div class="widget">
                        <div class="widget-header">
                            <h4 class="widget-title">ELEVER</h4>
                        </div>
                        <hr class="widget-separator">
                        <div class="widget-body">
                            <table id="students" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}" style="">
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

                <?php
                break;
            case "3": case "4":

                break;
        }
        ?>

    </div>
    <div class="col-sm-12 col-md-3">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title">Shortcuts and general information</h4>
            </div>
            <hr class="widget-separator">
            <div class="widget-body">
                <div class="col-sm-10">
                    <?php
                    if ($classHandler->_user->user_type_id != 1) {
                        echo '<h4>' . $schoolHandler->school->name . '</h4>';
                        echo '<h6>' . $schoolHandler->school->address . '</h6>';
                        echo '<h6>' . $schoolHandler->school->zip_code . ' ' . $schoolHandler->school->city . '</h6>';
                    }
                    ?>
                </div>
                <div class="col-sm-2">
                    <?php if ($classHandler->_user->user_type_id != 1) { ?>
                        <?php if (RightsHandler::has_user_right("SCHOOL_EDIT")) { ?>
                            <div class="p-t-xs">
                                <i class = "fa fa-edit fa-fw fa-2x edit_school m-r-md a" school_id="<?php echo $schoolHandler->school->id; ?>"></i>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if ($classHandler->_user->user_type_id != 4 && $classHandler->_user->user_type_id != 1) { ?>
            <div class="widget">
                <div class='widget-header'>
                    <h4 class="widget-title">SOON EXPIRING CLASSES</h4>
                </div>
                <hr class="widget-separator m-0">
                <div class="widget-body">
                    <div class="streamline m-l-lg">
                        <?php foreach ($classHandler->classes as $value) { ?>
                            <div class="sl-item sl-danger p-b-md">
                                <div class="sl-content">
                                    <div class="m-t-0 change_page a text-danger animate-twice animated headShake" page='class_profile' step='' args='&class_id=<?php echo $value->id; ?>'>
                                        <?php echo $value->title; ?>
                                    </div>
                                    <p><?php echo $value->end_date; ?></p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <?php } ?>
        <div class="widget">
            <div class='widget-header'>
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("HOMEWORK"); ?></h4>
            </div>
            <hr class="widget-separator m-0">
            <div class="widget-body">
                <?php
                $end = microtime(true);

                echo '<br/><br/>Time spent: <strong style="font-size: 40px;">' . floor(($end - $begin) * 1000) . '</strong>ms';
                ?>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
