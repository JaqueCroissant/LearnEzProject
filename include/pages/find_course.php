<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';
$courseHandler = new CourseHandler();

?>

<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" id="find_course_header"><a href="#find_course_tab" class="my_tab_header" id="find_course_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("FIND_COURSE"); ?></a></li>
                    <li role="presentation" id="find_lecture_header"><a href="#find_lecture_tab" class="my_tab_header" id="find_lecture_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("FIND_LECTURE"); ?></a></li>
                    <li role="presentation" id="find_test_header"><a href="#find_test_tab" class="my_tab_header" id="find_test_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("FIND_TEST"); ?></a></li>
                </ul>

                <div class="my_tab_content">
                    
                    <div class="my_fade my_tab" id="find_course_tab">
                        <div class="widget-body">
                            <table id="default-datatable" class="table dataTable datatable_1" style="margin:20px 0px 25px 0px !important;" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>,columnDefs:[{orderable: false, targets: [5]}], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}" data-plugin="DataTable" role="grid" aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("TITLE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("DESCRIPTION"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("OS"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_LECTURES"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_TESTS"); ?></th>
                                        <?php if (RightsHandler::has_user_right("COURSE_ADMINISTRATE")) { ?>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <th hidden></th>
                                    </tr>
                                </thead>
                                <tfoot class="hidden">
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("TITLE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("DESCRIPTION"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("OS"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_LECTURES"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("AMOUNT_OF_TESTS"); ?></th>
                                        <?php if (RightsHandler::has_user_right("COURSE_ADMINISTRATE")) { ?>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <?php } ?>
                                        <th hidden></th>
                                    </tr>
                                </tfoot>

                                <tbody>
                                    <?php
                                    if (RightsHandler::has_user_right("COURSE_FIND")) {
                                        $courseHandler->get_multiple(0, "course");
                                        foreach ($courseHandler->courses as $value) {
                                            ?>
                                            <tr class="clickable_row account_tr_id_<?php echo $value->id; ?>">
                                                <td class="click_me" data-search="<?php echo $value->title; ?>"><?php echo (strlen($value->title) > 20 ? substr($value->title, 0, 20) : $value->title); ?></td>
                                                <td class="click_me" data-search="<?php echo $value->description; ?>"><?php echo (strlen($value->description) > 30 ? substr($value->description, 0, 30) : $value->description); ?></td>
                                                <td class="click_me" data-search="<?php echo $value->description; ?>"><?php echo $value->os_title; ?></td>
                                                <td class="click_me" align="center"><?php echo $value->amount_of_lectures; ?></td>
                                                <td class="click_me" align="center"><?php echo $value->amount_of_tests; ?></td>
                                                <?php if (RightsHandler::has_user_right("COURSE_ADMINISTRATE")) { ?>
                                                    <td align="center">
                                                        <div>
                                                            <i class="zmdi zmdi-hc-lg zmdi-edit edit_account m-r-xs change_page" style="display: inline-block;" page="course_edit" args="&type=course&id=<?php echo $value->id; ?>" id="course_edit"></i>
                                                            <?php if (RightsHandler::has_user_right("COURSE_DELETE")) { ?>
                                                            <form style="display: inline-block;" method="post" id="click_alert_form_<?php echo $value->id; ?>" url="course.php?step=delete">
                                                                <input type="hidden" name="id" value="<?php echo $value->id; ?>">
                                                                <input type="hidden" name="type" value="course">
                                                                <i class="zmdi zmdi-hc-lg zmdi-delete btn_delete_course" delete_type="course" current_datatable="datatable_1" element_id="<?php echo $value->id; ?>" id="click_alert_btn" style=""></i>
                                                                <input type="hidden" name="submit" value="submit"></input>
                                                            </form>
                                                            <?php } ?>
                                                        </div>
                                                    </td>

                                                <?php } ?>
                                                <td class="hidden"><?php echo $value->id; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="my_fade my_tab" id="find_lecture_tab">
                        <div class="widget-body">
                            <table id="default-datatable" class="table dataTable datatable_2" style="margin:20px 0px 25px 0px !important;" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>,columnDefs:[{orderable: false, targets: [4]}], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}" data-plugin="DataTable" role="grid"
                                   aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("TITLE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("DESCRIPTION"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("COURSE"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("DURATION"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <th hidden></th>
                                    </tr>
                                </thead>
                                <tfoot class="hidden">
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("TITLE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("DESCRIPTION"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("COURSE"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("DURATION"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <th hidden></th>
                                    </tr>
                                </tfoot>

                                <tbody>
                                    <?php
                                    if (RightsHandler::has_user_right("COURSE_FIND")) {
                                        $courseHandler->get_multiple(0, "lecture");
                                        foreach ($courseHandler->lectures as $value) {
                                            ?>
                                            <tr class="clickable_row account_tr_id_<?php echo $value->id; ?>">
                                                <td class="click_me" data-search="<?php echo $value->title; ?>"><?php echo (strlen($value->title) > 40 ? substr($value->title, 0, 40) : $value->title); ?></td>
                                                <td class="click_me" data-search="<?php echo $value->description; ?>"><?php echo (strlen($value->description) > 50 ? substr($value->description, 0, 50) : $value->description); ?></td>
                                                <td class="click_me" data-search="<?php echo $value->course_title; ?>"><?php echo (strlen($value->course_title) > 20 ? substr($value->course_title, 0, 20) : $value->course_title); ?></td>
                                                <td class="click_me" align="center"><?php echo $value->time_length; ?></td>
                                                <?php if (RightsHandler::has_user_right("COURSE_ADMINISTRATE")) { ?>
                                                    <td align="center">
                                                        <div>
                                                            <i class="zmdi zmdi-hc-lg zmdi-edit edit_account m-r-xs change_page" style="display: inline-block;" page="course_edit" args="&type=lecture&id=<?php echo $value->id; ?>" id="course_edit"></i>
                                                            <?php if (RightsHandler::has_user_right("COURSE_DELETE")) { ?>
                                                            <form style="display: inline-block;" method="post" id="click_alert_form_<?php echo $value->id; ?>" url="course.php?step=delete">
                                                                <input type="hidden" name="id" value="<?php echo $value->id; ?>">
                                                                <input type="hidden" name="type" value="lecture">
                                                                <i class="zmdi zmdi-hc-lg zmdi-delete btn_delete_course" delete_type="lecture" current_datatable="datatable_2" element_id="<?php echo $value->id; ?>" id="click_alert_btn" style=""></i>
                                                                <input type="hidden" name="submit" value="submit"></input>
                                                            </form>
                                                            <?php } ?>
                                                        </div>
                                                    </td>

                                                <?php } ?>
                                                <td class="hidden"><?php echo $value->id; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="my_fade my_tab" id="find_test_tab">
                        <div class="widget-body">
                            <table id="default-datatable" class="table dataTable datatable_3" style="margin:20px 0px 25px 0px !important;" cellspacing="0" data-options="{pageLength: <?php echo SettingsHandler::get_settings()->elements_shown; ?>,columnDefs:[{orderable: false, targets: [4]}], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}" data-plugin="DataTable" role="grid"
                                   aria-describedby="default-datatable_info">
                                <thead>
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("TITLE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("DESCRIPTION"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("COURSE"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("TOTAL_STEPS"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <th hidden></th>
                                    </tr>
                                </thead>
                                <tfoot class="hidden">
                                    <tr role="row">
                                        <th><?php echo TranslationHandler::get_static_text("TITLE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("DESCRIPTION"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("COURSE"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("TOTAL_STEPS"); ?></th>
                                        <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("EDIT"); ?></th>
                                        <th hidden></th>
                                    </tr>
                                </tfoot>

                                <tbody>
                                    <?php
                                    if (RightsHandler::has_user_right("COURSE_FIND")) {
                                        $courseHandler->get_multiple(0, "test");
                                        foreach ($courseHandler->tests as $value) {
                                            ?>
                                            <tr class="clickable_row account_tr_id_<?php echo $value->id; ?>">
                                                <td class="click_me" data-search="<?php echo $value->title; ?>"><?php echo (strlen($value->title) > 40 ? substr($value->title, 0, 40) : $value->title); ?></td>
                                                <td class="click_me" data-search="<?php echo $value->description; ?>"><?php echo (strlen($value->description) > 50 ? substr($value->description, 0, 50) : $value->description); ?></td>
                                                <td class="click_me" data-search="<?php echo $value->course_title; ?>"><?php echo (strlen($value->course_title) > 20 ? substr($value->course_title, 0, 20) : $value->course_title); ?></td>
                                                <td class="click_me" align="center"><?php echo $value->total_steps; ?></td>
                                                <?php if (RightsHandler::has_user_right("COURSE_ADMINISTRATE")) { ?>
                                                    <td align="center">
                                                        <div>
                                                            <i class="zmdi zmdi-hc-lg zmdi-edit edit_account m-r-xs change_page" style="display: inline-block;" page="course_edit" args="&type=test&id=<?php echo $value->id; ?>" id="course_edit"></i>
                                                            <?php if (RightsHandler::has_user_right("COURSE_DELETE")) { ?>
                                                            <form style="display: inline-block;" method="post" id="click_alert_form_<?php echo $value->id; ?>" url="course.php?step=delete">
                                                                <input type="hidden" name="id" value="<?php echo $value->id; ?>">
                                                                <input type="hidden" name="type" value="test">
                                                                <i class="zmdi zmdi-hc-lg zmdi-delete btn_delete_course" delete_type="test" current_datatable="datatable_3" element_id="<?php echo $value->id; ?>" id="click_alert_btn" style=""></i>
                                                                <input type="hidden" name="submit" value="submit"></input>
                                                            </form>
                                                            <?php } ?>
                                                        </div>
                                                    </td>

                                                <?php } ?>
                                                <td class="hidden"><?php echo $value->id; ?></td>
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
            </div>
        </div>
    </div>
</div>

<div style="display:none;" id="delete_course"><?php echo TranslationHandler::get_static_text("CONFIRM_DELETE_COURSE"); ?></div>
<div style="display:none;" id="delete_lecture"><?php echo TranslationHandler::get_static_text("CONFIRM_DELETE_LECTURE"); ?></div>
<div style="display:none;" id="delete_test"><?php echo TranslationHandler::get_static_text("CONFIRM_DELETE_TEST"); ?></div>

<div id="click_alertbox" class="panel panel-danger alert_panel hidden" >
    <div class="panel-heading"><h4 class="panel-title"><?php echo TranslationHandler::get_static_text("ALERT"); ?></h4></div>
    <div class="panel-body">
    </div>
    <div class="panel-footer p-h-sm">
        <p class="m-0">
            <input class="btn btn-default btn-sm p-v-lg accept_click_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>">
            <input class="btn btn-default btn-sm p-v-lg cancel_click_alertbox_btn" id="" type="button" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>">
        </p>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
