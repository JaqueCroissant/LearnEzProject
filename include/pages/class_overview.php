<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';

$classHandler = new ClassHandler();
$classHandler->get_all_classes();
?>

<?php
switch ($classHandler->_user->user_type_id) {
    case "1":
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
    case "2":
        ?>
        <div class="col-md-3 pull-right">
            <div class="widget">
                <div class="widget-header">
                    <h4 class="widget-title">Shortcuts and general information</h4>
                </div>
                <hr class="widget-separator">
                <div class="widget-body">
                    dsafdsafdsafdsa
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("OPEN_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?></h4>
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">
                        <div class="table-responsive">
                            <table id="classes" class="table display" data-plugin="DataTable" role="grid">
                                <thead>
                                <th colspan="2"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></th>
                                <th></th>
                                <th><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("STUDENTS"); ?></th>
                                <th><?php echo TranslationHandler::get_static_text("TEACHERS"); ?></th>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($classHandler->classes as $value) {
                                        if ($value->open == "1") {
                                            ?>
                                            <tr>
                                                <td colspan="2" class="text-primary"><?php echo $value->title; ?></td>
                                                <td></td>
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
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("OPEN_P") . " " . strtolower(TranslationHandler::get_static_text("CLASSES")); ?></h4>
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">

                    </div>
                </div>
            </div>
        </div>
        <?php
        break;
    case "3":
        break;
    case "4":
        break;
}
?>
<script src="assets/js/include_library.js" type="text/javascript"></script>
<script src="assets/js/include_app.js" type="text/javascript"></script>
