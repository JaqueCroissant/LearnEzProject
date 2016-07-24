<?php
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

<?php
switch ($classHandler->_user->user_type_id) {
    case "5":
        ?>
        <div class="col-md-3 pull-right">
            <div class="widget">
                <div class="widget-header">
                    Shortcuts etc
                </div>
                <hr class="widget-separator">
                <div class="widget-body">
                    Does everybody know that pig named Lorem Ipsum? She's a disgusting pig, right? I’m the best thing that ever happened to placeholder text. Lorem Ipsum's father was with Lee Harvey Oswald prior to Oswald's being, you know, shot. This placeholder text is gonna be HUGE.
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-header">
                        Test 1
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">
                        Does everybody know that pig named Lorem Ipsum? She's a disgusting pig, right? I’m the best thing that ever happened to placeholder text. Lorem Ipsum's father was with Lee Harvey Oswald prior to Oswald's being, you know, shot. This placeholder text is gonna be HUGE.

                        Lorem Ipsum is unattractive, both inside and out. I fully understand why it’s former users left it for something else. They made a good decision. Lorem Ispum is a choke artist. It chokes! I'm speaking with myself, number one, because I have a very good brain and I've said a lot of things.
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        Test 1
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">
                        Does everybody know that pig named Lorem Ipsum? She's a disgusting pig, right? I’m the best thing that ever happened to placeholder text. Lorem Ipsum's father was with Lee Harvey Oswald prior to Oswald's being, you know, shot. This placeholder text is gonna be HUGE.

                        Lorem Ipsum is unattractive, both inside and out. I fully understand why it’s former users left it for something else. They made a good decision. Lorem Ispum is a choke artist. It chokes! I'm speaking with myself, number one, because I have a very good brain and I've said a lot of things.
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        Test 1
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">
                        Does everybody know that pig named Lorem Ipsum? She's a disgusting pig, right? I’m the best thing that ever happened to placeholder text. Lorem Ipsum's father was with Lee Harvey Oswald prior to Oswald's being, you know, shot. This placeholder text is gonna be HUGE.

                        Lorem Ipsum is unattractive, both inside and out. I fully understand why it’s former users left it for something else. They made a good decision. Lorem Ispum is a choke artist. It chokes! I'm speaking with myself, number one, because I have a very good brain and I've said a lot of things.
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        Test 1
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">
                        Does everybody know that pig named Lorem Ipsum? She's a disgusting pig, right? I’m the best thing that ever happened to placeholder text. Lorem Ipsum's father was with Lee Harvey Oswald prior to Oswald's being, you know, shot. This placeholder text is gonna be HUGE.

                        Lorem Ipsum is unattractive, both inside and out. I fully understand why it’s former users left it for something else. They made a good decision. Lorem Ispum is a choke artist. It chokes! I'm speaking with myself, number one, because I have a very good brain and I've said a lot of things.
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case "1": case "2":
        ?>
        <div class="col-md-3 pull-right">
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
                                    <i class = "fa fa-edit fa-fw fa-2x edit_school_redirect m-r-md a" school_id="<?php echo $schoolHandler->school->id; ?>"></i>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-12">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("OPEN_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?></h4>
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">
                        <table id="example" class="table display table-hover" data-plugin="DataTable" data-options="{pageLength:5}">
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
            </div>

        </div>
        <?php
        break;
}
?>
<script src="assets/js/include_app.js" type="text/javascript"></script>
