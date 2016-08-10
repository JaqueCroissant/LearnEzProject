<?php
$begin = microtime(true);
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/statisticsHandler.php';
require_once '../../include/handler/courseHandler.php';
require_once '../../include/handler/homeworkHandler.php';

$schoolHandler = new SchoolHandler();
$classHandler = new ClassHandler();
$classHandler->get_all_classes();
$userHandler = new UserHandler();
$courseHandler = new CourseHandler();
$statisticsHandler = new StatisticsHandler();
$homeworkHandler = new HomeworkHandler();

$courses_average = 0;
$courses_completed = 0;
?>

<style>
    .dataTables_filter, .dataTables_length, .dataTables_info { display: none !important;}
</style>
<div class="row">
    
    <?php
    switch($userHandler->_user->user_type_id)
    {
        case "1":

            $schoolHandler->get_all_schools();
            $statisticsHandler->get_top_students();
            ?>

            <div class="col-md-9 col-sm-12 p-v-0">
                <div class="col-sm-12">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title a change_page" page="find_school" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("FIND_SCHOOL")?>"><?php echo TranslationHandler::get_static_text("SCHOOLS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5, columnDefs:[{orderable: false, targets: [4]}]}">
                                            <thead>
                                                <tr>
                                                    <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("CITY"); ?></th>


                                                    <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STATUS"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if(count($schoolHandler->all_schools) > 0)
                                                    {
                                                        foreach ($schoolHandler->all_schools as $value) {

                                                        ?>
                                                        <tr class = "a change_page" page="school_profile" step = "" args = "&school_id=<?php echo $value->id; ?>">
                                                            <td><?php echo (strlen($value->name) > 20 ? substr($value->name, 0, 20) : $value->name); ?></td>
                                                            <td><?php echo (strlen($value->address) > 20 ? substr($value->address, 0, 20) : $value->address); ?></td>
                                                            <td><?php echo $value->zip_code; ?></td>
                                                            <td><?php echo (strlen($value->city) > 15 ? substr($value->city, 0, 15) : $value->city); ?></td>
                                                            
                                                            <td style="text-align: center;"><?php echo !$value->open ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("CLOSED") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("OPEN") . '"></i>'; ?></td>
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
                
                
                <div class="col-md-6 col-sm-12 ">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("TOP") . " " . TranslationHandler::get_static_text("STUDENTS") . " - Husk at tjekke om brugertype = 4"; ?> </h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <div class="streamline m-l-lg">
                                <?php
                                if(count($statisticsHandler->top_students) > 0)
                                {
                                    //var_dump($statisticsHandler->top_students);
                                    foreach($statisticsHandler->top_students as $value)
                                    { ?>
                                        <div class="sl-item p-b-md">
                                            <div class="sl-avatar avatar avatar-sm avatar-circle">
                                                <img class="img-responsive" src="<?php echo "assets/images/profile_images/" . $value['image_id'] . ".png"?>">
                                            </div>
                                            <div class="sl-content">
                                                <h5 class="m-t-0">
                                                    <a class="m-r-xs text-primary a change_page" page="account_profile" step="" args="&user_id=<?php echo $value['id']; ?>"><?php echo $value['firstname'] . " " . $value['surname']?></a>
                                                    <small class="text-muted fz-sm"><?php echo $value['username'] ?></small>
                                                </h5>
                                                <p><?php echo $value['points']; ?> points</p>
                                            </div>
                                        </div>
                                <?php
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                
            </div>
    
            <?php        
            break;
        
        //LOKAL ADMIN DASHBOARD
        case "2":
            
            $schoolHandler->get_school_by_id($userHandler->_user->school_id);
            $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
            $userHandler->get_by_school_id($userHandler->_user->school_id);
            ?>
 
            <div class="col-md-9 col-sm-12 p-v-0">
                <div class="col-sm-12">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title a change_page" page="find_class" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("FIND_CLASS")?>"><?php echo TranslationHandler::get_static_text("CLASSES"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5, columnDefs:[{orderable: false, targets: [3]}]}">
                                            <thead>
                                                <tr>
                                                    <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                                    <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                                    <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                                    <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STATUS"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if(count($classHandler->classes) > 0)
                                                    {
                                                        foreach ($classHandler->classes as $value) {

                                                        ?>
                                                        <tr class = "a change_page" page="class_profile" step = "" args = "&class_id=<?php echo $value->id; ?>">
                                                            <td><?php echo $value->title; ?></td>
                                                            <td style="text-align: center;"><?php echo $value->class_year; ?></td>
                                                            <td style="text-align: center;"><?php echo $value->number_of_students; ?></td>
                                                            <td style="text-align: center;"><?php echo !$value->open ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("CLOSED") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("OPEN") . '"></i>'; ?></td>
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
                
                
                <div class="col-sm-12">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title a change_page" page="find_account" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("FIND_ACCOUNT")?>"><?php echo TranslationHandler::get_static_text("ACCOUNTS"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
                                            <thead>
                                                <tr>
                                                    <th><?php echo TranslationHandler::get_static_text("USERNAME"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("USER_TYPE"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if(count($userHandler->users) > 0)
                                                    {
                                                        foreach ($userHandler->users as $value) 
                                                        {

                                                        ?>
                                                            <tr class = "a change_page" page="account_profile" step = "" args = "&user_id=<?php echo $value->id; ?>">
                                                                <td><?php echo $value->username; ?></td>
                                                                <td><?php echo (strlen($value->firstname . " " . $value->surname) > 20 ? substr($value->firstname . " " . $value->surname, 0, 20) : $value->firstname . " " . $value->surname); ?></td>
                                                                <td><?php echo $value->user_type_title; ?></td>
                                                                <td><?php echo (strlen($value->email) > 20 ? substr($value->email, 0, 20) : $value->email); ?></td>
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
                

                <div class="col-sm-12">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title a change_page" page="homework_overview" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("HOMEWORK_OVERVIEW")?>"><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <div class="panel-body">
                            <?php if(empty($homeworkHandler->homework)) {
                                echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;"> Du har ingen lektier i øjeblikket.</div>';
                            } else {
                            ?>
                                <div class="latest-homework">
                                <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4,5]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                    <thead>
                                        <tr>
                                            <th><?php echo TranslationHandler::get_static_text("TITLE")?></th>
                                            <th><?php echo TranslationHandler::get_static_text("CLASSES")?></th>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("END") . " " . TranslationHandler::get_static_text("DATE_DATE")?></th>
                                            <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("LECTURES")?></th>
                                            <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("TESTS")?></th>
                                            <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("STATUS")?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($homeworkHandler->homework as $value) {
                                            $classes = "";
                                            for($i = 0; $i < count($value->classes); $i++) {
                                                $classes .= $value->classes[$i]->title;
                                                $classes .= $i != count($value->classes)-1 ? ", " : "";
                                            }

                                            ?>
                                            <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                <?php
                                                if(!empty($value->lectures)) {
                                                    echo '<b>Lektioner:</b>';
                                                    foreach($value->lectures as $lecture) {
                                                        echo '<br />- ' . $lecture->title . '';
                                                    }
                                                    echo '<br />';
                                                }

                                                if(!empty($value->tests)) {
                                                    echo '<b>Tests:</b>';
                                                    foreach($value->tests as $test) {
                                                        echo '<br />- ' . $test->title . '';
                                                    }
                                                }

                                                ?>">
                                                <td><?php echo $value->title; ?></td>
                                                <td><span data-toggle="tooltip" title="<?= $classes ?>"><?= strlen($classes) > 30 ? substr($classes, 0, 30) . "..." : $classes ?></span></td>
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
            </div>
    
            <?php
            break;
        
        case "3":
            //TEACHER DASHBOARD
            
            $schoolHandler->get_school_by_id($userHandler->_user->school_id);
            $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
            $homeworkHandler->get_user_homework();
            $classHandler->get_classes_by_user_id($userHandler->_user->id);
            $courseHandler->get_courses();
            $course_count = count($courseHandler->courses);
            $courses_started = $course_count;
            ?>
            <div class="col-md-9 col-sm-12 p-v-0">
                <div class="col-sm-12">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title a change_page" page="find_class" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("FIND_CLASS")?>"><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5, columnDefs:[{orderable: false, targets: [3]}]}">
                                            <thead>
                                                <tr>
                                                    <th><?php echo TranslationHandler::get_static_text("NAME"); ?></th>
                                                    <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                                    <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                                    <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("STATUS"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if(count($classHandler->classes) > 0)
                                                    {
                                                        foreach ($classHandler->classes as $value) {

                                                        ?>
                                                        <tr class = "a change_page" page="class_profile" step = "" args = "&class_id=<?php echo $value->id; ?>">
                                                            <td><?php echo $value->title; ?></td>
                                                            <td style="text-align: center;"><?php echo $value->class_year; ?></td>
                                                            <td style="text-align: center;"><?php echo $value->number_of_students; ?></td>
                                                            <td style="text-align: center;"><?php echo !$value->open ? '<i class="zmdi-hc-fw zmdi zmdi-minus-circle zmdi-hc-lg fw-700" style="color: #f15530;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("CLOSED") . '"></i>' : '<i class="zmdi-hc-fw zmdi zmdi-check-circle zmdi-hc-lg fw-700" style="color: #36ce1c;" data-toggle="tooltip" title="' . TranslationHandler::get_static_text("OPEN") . '"></i>'; ?></td>
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


                <div class="col-sm-12">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title a change_page" page="homework_overview" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("HOMEWORK_OVERVIEW")?>"><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <div class="panel-body">
                            <?php if(empty($homeworkHandler->homework)) {
                                echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;"> Du har ingen lektier i øjeblikket.</div>';
                            } else {
                            ?>
                                <div class="latest-homework">
                                <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4,5]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                    <thead>
                                        <tr>
                                            <th><?php echo TranslationHandler::get_static_text("TITLE")?></th>
                                            <th><?php echo TranslationHandler::get_static_text("CLASSES")?></th>
                                            <th style="text-align: center;"><?php echo TranslationHandler::get_static_text("END") . " " . TranslationHandler::get_static_text("DATE_DATE")?></th>
                                            <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("LECTURES")?></th>
                                            <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("TESTS")?></th>
                                            <th style='text-align:center;'><?php echo TranslationHandler::get_static_text("STATUS")?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($homeworkHandler->homework as $value) {
                                            $classes = "";
                                            for($i = 0; $i < count($value->classes); $i++) {
                                                $classes .= $value->classes[$i]->title;
                                                $classes .= $i != count($value->classes)-1 ? ", " : "";
                                            }

                                            ?>
                                            <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                <?php
                                                if(!empty($value->lectures)) {
                                                    echo '<b>Lektioner:</b>';
                                                    foreach($value->lectures as $lecture) {
                                                        echo '<br />- ' . $lecture->title . '';
                                                    }
                                                    echo '<br />';
                                                }

                                                if(!empty($value->tests)) {
                                                    echo '<b>Tests:</b>';
                                                    foreach($value->tests as $test) {
                                                        echo '<br />- ' . $test->title . '';
                                                    }
                                                }

                                                ?>">
                                                <td><?php echo $value->title; ?></td>
                                                <td><span data-toggle="tooltip" title="<?= $classes ?>"><?= strlen($classes) > 30 ? substr($classes, 0, 30) . "..." : $classes ?></span></td>
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
            </div>
    
            <?php
            break;
            
        //STUDENT DASHBOARD    
        case "4":
            
            $schoolHandler->get_school_by_id($userHandler->_user->school_id);
            $classHandler->get_classes_by_school_id($userHandler->_user->school_id);
            $homeworkHandler->get_user_homework();
            $classHandler->get_classes_by_user_id($userHandler->_user->id);
            $courseHandler->get_courses();
            $course_count = count($courseHandler->courses);
            $courses_started = $course_count;
            $statisticsHandler->get_student_stats();
            ?>
            <div class="col-md-9 col-sm-12 p-v-0">
                <div class="col-sm-12">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title a change_page" page="course_overview" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("FIND_COURSE")?>"><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("COURSES")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
                                            <thead>
                                                <tr>
                                                    <th><?php echo TranslationHandler::get_static_text("COURSE"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("LECTURES"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("TESTS"); ?></th>
                                                    <th><?php echo TranslationHandler::get_static_text("PROGRESS"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($courseHandler->courses as $value) {

                                                    $courses_average += $value->overall_progress;
                                                    if($value->overall_progress > 99)
                                                    {
                                                        $courses_completed++;
                                                    }
                                                    else if($value->overall_progress < 1)
                                                    {
                                                        $courses_started--;
                                                    }
                                                    ?>
                                                    <tr class = "a change_page" page="course_show" step = "" args = "&course_id=<?php echo $value->id; ?>">
                                                        <td><?php echo $value->title; ?></td>
                                                        <td><?php echo $value->amount_of_lectures; ?></td>
                                                        <td><?php echo $value->amount_of_tests; ?></td>
                                                        <td><?php echo$value->overall_progress?>%</td>;
                                                    </tr>
                                                <?php }
                                                $courses_average = $course_count > 0 ? round($courses_average / $course_count,0) : 0;
                                                ?>
                                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="col-sm-12">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title a change_page" page="homework_overview" data-toggle="tooltip" data-placement="left" title="<?= TranslationHandler::get_static_text("HOMEWORK_OVERVIEW")?>"><?php echo TranslationHandler::get_static_text("MY_P") . " " . strtolower(TranslationHandler::get_static_text("HOMEWORK")); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <div class="panel-body">
                            <?php if(empty($homeworkHandler->homework)) {
                                echo '<div class="center latest-homework-empty" style="margin-top:20px;margin-bottom:20px;"> Du har ingen lektier i øjeblikket.</div>';
                            } else {
                            ?>
                                <div class="latest-homework">
                                <table id="classes" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength: 5,columnDefs:[{orderable: false, targets: [3,4,5]}], order:[], language: {url: '<?php echo TranslationHandler::get_current_language() == 1 ? "//cdn.datatables.net/plug-ins/1.10.12/i18n/Danish.json": "//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json"; ?>'}}">
                                    <thead>
                                        <tr>
                                            <th>Titel</th>
                                            <th>Klasser</th>
                                            <th>Dato slut</th>
                                            <th style='text-align:center;'>Lektioner</th>
                                            <th style='text-align:center;'>Tests</th>
                                            <th style='text-align:center;'>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($homeworkHandler->homework as $value) {
                                            $classes = "";
                                            for($i = 0; $i < count($value->classes); $i++) {
                                                $classes .= $value->classes[$i]->title;
                                                $classes .= $i != count($value->classes)-1 ? ", " : "";
                                            }

                                            ?>
                                            <tr class="a change_page" page="homework_show" args="&homework_id=<?= $value->id ?>" data-container="body" data-toggle="popover" data-delay='{"show":"100", "hide":"100"}' data-placement="top" data-trigger="hover" data-html="true" data-content="
                                                <?php
                                                if(!empty($value->lectures)) {
                                                    echo '<b>Lektioner:</b>';
                                                    foreach($value->lectures as $lecture) {
                                                        echo '<br />- ' . $lecture->title . '';
                                                    }
                                                    echo '<br />';
                                                }

                                                if(!empty($value->tests)) {
                                                    echo '<b>Tests:</b>';
                                                    foreach($value->tests as $test) {
                                                        echo '<br />- ' . $test->title . '';
                                                    }
                                                }

                                                ?>">
                                                <td><?php echo $value->title; ?></td>
                                                <td><span data-toggle="tooltip" title="<?= $classes ?>"><?= strlen($classes) > 35 ? substr($classes, 0, 35) . "..." : $classes ?></span></td>
                                                <td><?php echo $value->date_expire; ?></td>
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


                <div class="col-md-12 col-sm-12 ">
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("STATISTICS"); ?> </h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <div class="row">
                                        <div class="col-md-6 col-sm-6">
                                            <div class="user-card">
                                                    <div class="media-left">
                                                        <div class="pieprogress" data-value="<?php echo $courses_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($courses_average)?>"}, thickness: 10}' data-size="70">
                                                            <strong style="margin-top: -14px; font-size: 14px;"><span><?php echo $courses_average; ?></span>%    </strong>
                                                        </div>
                                                    </div>
                                                    <div class="media-right ">
                                                        <div style="margin-left: 25px;">
                                                            <label  class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AVERAGE") . " " . strtolower(TranslationHandler::get_static_text("COURSE")) . " ". strtolower(TranslationHandler::get_static_text("PROGRESS")); ?></label>
                                                        </div>
                                                        <div style="margin-left: 25px;">
                                                            <?php echo TranslationHandler::get_static_text("COURSES") . " " . strtolower(TranslationHandler::get_static_text("STARTED")) . ": " . $courses_started . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $course_count; ?>
                                                        </div>
                                                        <div style="margin-left: 25px;">
                                                            <?php echo TranslationHandler::get_static_text("COURSES") . " " . strtolower(TranslationHandler::get_static_text("COMPLETED")) . ": " . $courses_completed . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $course_count; ?>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="user-card">
                                                    <div class="media-left">
                                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_lecture_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_lecture_average)?>"}, thickness: 10}' data-size="70">
                                                            <strong style="margin-top: -14px; font-size: 14px;"><span><?php echo $statisticsHandler->student_lecture_average; ?></span>%    </strong>
                                                        </div>
                                                    </div>
                                                    <div class="media-right ">
                                                        <div style="margin-left: 25px;">
                                                            <label  class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AVERAGE") . " " . strtolower(TranslationHandler::get_static_text("LECTURE")) . " ". strtolower(TranslationHandler::get_static_text("PROGRESS")); ?></label>
                                                        </div>
                                                        <div style="margin-left: 25px;">
                                                            <?php echo TranslationHandler::get_static_text("LECTURES") . " " . strtolower(TranslationHandler::get_static_text("STARTED")) . ": " . $statisticsHandler->student_lectures_started . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $statisticsHandler->student_total_lectures; ?>
                                                        </div>
                                                        <div style="margin-left: 25px;">
                                                            <?php echo TranslationHandler::get_static_text("LECTURES") . " " . strtolower(TranslationHandler::get_static_text("COMPLETED")) . ": " . $statisticsHandler->student_lectures_complete . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $statisticsHandler->student_total_lectures; ?>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="user-card">
                                                    <div class="media-left">
                                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_test_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_test_average)?>"}, thickness: 10}' data-size="70">
                                                            <strong style="margin-top: -14px; font-size: 14px;"><span><?php echo $statisticsHandler->student_test_average; ?></span>%</strong>
                                                        </div>
                                                    </div>
                                                    <div class="media-right">
                                                        <div style="margin-left: 25px;">
                                                            <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("AVERAGE_TEST_PROG"); ?></label>
                                                        </div>
                                                        <div style="margin-left: 25px;">
                                                            <?php echo TranslationHandler::get_static_text("TESTS_STARTED") . ": " . $statisticsHandler->student_tests_started . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $statisticsHandler->student_total_tests; ?>
                                                        </div>
                                                        <div style="margin-left: 25px;">
                                                            <?php echo TranslationHandler::get_static_text("TESTS_COMPLETED") . ": " . $statisticsHandler->student_tests_complete . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . $statisticsHandler->student_total_tests; ?>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="user-card">
                                                    <div class="media-left">
                                                        <div class="pieprogress" data-value="<?php echo $statisticsHandler->student_test_average / 100; ?>" data-plugin="circleProgress" data-options='{fill: {color: "<?php echo get_progress_color($statisticsHandler->student_test_average)?>"}, thickness: 10}' data-size="70">
                                                            <strong style="margin-top: -14px; font-size: 14px;"><span><?php echo $statisticsHandler->student_test_average; ?></span>%</strong>
                                                        </div>
                                                    </div>
                                                    <div class="media-right">
                                                        <div style="margin-left: 25px;">
                                                            <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("ACHIEVEMENTS"); ?></label>
                                                        </div>
                                                        <div style="margin-left: 25px;">
                                                            <?php echo TranslationHandler::get_static_text("BADGES_OBTAINED") . ": " . "NULL" . " " . strtolower(TranslationHandler::get_static_text("OF")) . " " . "NULL"; ?>
                                                        </div>
                                                        <div style="margin-left: 25px;">
                                                            <?php echo TranslationHandler::get_static_text("TOTAL_POINTS") . ": " . $userHandler->_user->points;?>
                                                        </div>
                                                    </div>
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
    
    
    <!--DISPLAY USER INFO!-->
    <div class="col-md-3 col-sm-12">
        <div class="widget">

            <hr class="widget-separator m-0">
            <div class="widget-body">
                <?php if (RightsHandler::has_page_right("SETTINGS_EDIT_USER_INFO")) { ?>
                    <div class="pull-right">
                        <i class="zmdi zmdi-hc-lg zmdi-edit m-r-xs change_page a" page="settings" step="edit_user_info" args="&user_id=<?php echo $userHandler->_user->id; ?>" data-toggle="tooltip" title="<?= TranslationHandler::get_static_text("EDIT_ACCOUNT")?>"></i>
                    </div>
                <?php } ?>
                <div style="width:100%;">
                <div class="avatar avatar-circle" style="display:block !important;margin: 10px auto 10px auto !important; width: 100px; height: 100px;">
                    <img src="assets/images/profile_images/<?= $userHandler->_user->image_id; ?>.png" alt="avatar">
                </div>
                </div>
                <div class="center">
                    <b><?php echo $userHandler->_user->firstname . " " . $userHandler->_user->surname; ?></b>
                </div>
                <br/>
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USERNAME") . ":"; ?></label>
                    <span class="pull-right"><?php echo $userHandler->_user->username; ?></span>
                </div>
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("USER_TYPE") . ":"; ?></label>
                    <span class="pull-right"><?php echo $userHandler->_user->user_type_title; ?></span>
                </div>
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("OS") . ":"; ?></label>
                    <span class="pull-right">
                    <?php 
                        $os_data = $courseHandler::get_os_options();
                        foreach($os_data as $os)
                        {
                            if($os['id']==$userHandler->_user->settings->os_id)
                            {
                                echo $os['title'];
                            }
                        }
                    ?>
                    </span>
                </div>
                <div>
                    <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("INFO_EMAIL") . ":"; ?></label>
                    <span class="pull-right"><?php echo $userHandler->_user->email; ?></span>
                </div>
                
                <?php
                if($userHandler->_user->user_type_id > 1)
                {
                ?>
                    <div>
                        <label class="control-label" for="first_name"><?php echo TranslationHandler::get_static_text("SCHOOL") . ":"; ?></label>
                        
                        <a class="change_page" page="school_profile" step="" args="&school_id=<?php echo $schoolHandler->school->id ?>" href="javascript:void(0)">
                            <span class="pull-right"><?php echo $schoolHandler->school->name; ?></span>
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        
        <!--DISPLAY CLASSES!-->
        <?php
            if($userHandler->_user->user_type_id > 3)
            {
                if(count($classHandler->classes) > 0)
                {
                ?>
                    <div class="widget">
                        <div class='widget-header'>
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CLASSES"); ?></h4>
                        </div>
                        <hr class="widget-separator m-0">
                        <div class="widget-body">
                            <?php

                                    for($i=0; $i < count($classHandler->classes); $i++)
                                    {
                                        echo '<div><a class="change_page" page="class_profile" step="" args="&class_id=' . $classHandler->classes[$i]->id . '" href="javascript:void(0)">' . $classHandler->classes[$i]->title . '</a></div>';
                                    }

                            ?>
                        </div>
                    </div>
        
                <?php
                }
            }
        ?>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
    $('[data-toggle="popover"]').popover({trigger: "hover"});});</script>
