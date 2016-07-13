<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';
$classHandler = new ClassHandler();
if ($classHandler->_user->user_type_id != 1) {
    $classHandler->get_classes_by_school_id($classHandler->_user->school_id);
} else {
    $classHandler->get_all_classes();
}
?>
<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <!-- tabs list -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab-1" aria-controls="tab-3" role="tab" data-toggle="tab" 
                                                              aria-expanded="true"><?php echo TranslationHandler::get_static_text("FIND_CLASS"); ?></a></li>
                    <li role="presentation" class=""><a href="#tab-2" aria-controls="tab-1" role="tab" data-toggle="tab" 
                                                        aria-expanded="false"><?php echo TranslationHandler::get_static_text("EDIT_CLASS_GENERIC"); ?></a></li>
                </ul><!-- .nav-tabs -->

                <!-- Tab panes -->
                <div class="tab-content p-md">
                    <div role="tabpanel" class="tab-pane fade active in" id="tab-1">
                        <div class="widget-body">
                            <div class="tab-content p-md">
                                <div role="tabpanel" class="tab-pane fade active in" id="tab-1">
                                    <div class="widget-body">
                                        <form method="POST" action="" id="find_class" class="form-horizontal" url="create_account.php?step=1" name="create_single_user">
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>