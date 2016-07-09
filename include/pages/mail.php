<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
require_once '../../include/handler/mailHandler.php';

$mailHandler = new MailHandler();
//var_dump($mailHandler->get_mails());
?>

<div class="row">
    <div class="col-md-2">
        <div class="app-action-panel" id="inbox-action-panel">
            <div class="action-panel-toggle" data-toggle="class" data-target="#inbox-action-panel" data-class="open">
                <i class="fa fa-chevron-right"></i>
                <i class="fa fa-chevron-left"></i>
            </div>

            <div class="m-b-lg">
                <a href="#" type="button" data-toggle="modal" data-target="#composeModal" class="btn action-panel-btn btn-default btn-block"><?php echo TranslationHandler::get_static_text("WRITE_NEW"); ?></a>
            </div>
            
            <hr class="m-0 m-b-md" style="border-color: #ddd;">

            <div class="app-actions-list scrollable-container ps-container ps-theme-default" data-ps-id="0ba7452c-1106-b6f8-5070-56e24cb65638">
                <div class="list-group">
                    <?php
                    if($mailHandler->get_folders()) {
                        foreach($mailHandler->folders as $value) {
                            echo '<a href="javascript:void(0)" class="change_page text-color list-group-item" page="mail" args="'. ($value->folder_name == "inbox" ? "" : $value->folder_name) . '" id="mail"><i class="m-r-sm fa '. $value->icon_class .'"></i>'. $value->title .'</a>';
                        }
                    }
                    ?>
                </div>

                <div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></div><!-- .app-actions-list -->
        </div>
    </div>

    <div class="col-md-10">
        <div class="row">
            <div class="col-md-12">
                <div class="mail-toolbar m-b-lg">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("ORDER_BY"); ?> <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="#"><?php echo TranslationHandler::get_static_text("NEWEST"); ?></a></li>
                            <li><a href="#"><?php echo TranslationHandler::get_static_text("OLDEST"); ?></a></li>
                        </ul>
                    </div>
                    
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo TranslationHandler::get_static_text("FILTER"); ?> <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="#"><?php echo TranslationHandler::get_static_text("UNREAD"); ?></a></li>
                            <li><a href="#"><?php echo TranslationHandler::get_static_text("READ"); ?></a></li>
                        </ul>
                    </div>

                    <div class="btn-group" role="group">
                        <a href="#" class="btn btn-default"><i class="fa fa-trash"></i></a>
                        <a href="#" class="btn btn-default"><i class="fa fa-exclamation-circle"></i></a>
                    </div>

                    <div class="btn-group pull-right" role="group">
                        <a href="#" class="btn btn-default"><i class="fa fa-chevron-left"></i></a>
                        <a href="#" class="btn btn-default"><i class="fa fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mail-list">
                <tbody><tr>
                        <td>
                            <!-- a single mail -->
                            <div class="mail-item">
                                <table class="mail-container">
                                    <tbody><tr>
                                            <td class="mail-left">
                                                <div class="avatar avatar-lg avatar-circle">
                                                    <a href="#"><img src="../assets/images/208.jpg" alt="sender photo"></a>
                                                </div>
                                            </td>
                                            <td class="mail-center">
                                                <div class="mail-item-header">
                                                    <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">Welcome To Dashboard</a></h4>
                                                    <a href="#"><span class="label label-success">client</span></a>
                                                    <a href="#"><span class="label label-primary">work</span></a>
                                                </div>
                                                <p class="mail-item-excerpt">Welcome To your dashboard. here you can manage and coordinate any activities</p>
                                            </td>
                                            <td class="mail-right">
                                                <p class="mail-item-date">2 hours ago</p>
                                                <p class="mail-item-star starred">
                                                    <a href="#"><i class="zmdi zmdi-star"></i></a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </div><!-- END mail-item -->

                            <!-- a single mail -->
                            <div class="mail-item">
                                <table class="mail-container">
                                    <tbody><tr>
                                            <td class="mail-left">
                                                <div class="avatar avatar-lg avatar-circle">
                                                    <a href="#"><img src="../assets/images/209.jpg" alt="sender photo"></a>
                                                </div>
                                            </td>
                                            <td class="mail-center">
                                                <div class="mail-item-header">
                                                    <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">Account Activity</a></h4>
                                                    <a href="#"><span class="label label-warning">personal</span></a>
                                                </div>
                                                <p class="mail-item-excerpt">A login activity detected from unusual location. please check this mail</p>
                                            </td>
                                            <td class="mail-right">
                                                <p class="mail-item-date">1 minute ago</p>
                                                <p class="mail-item-star">
                                                    <a href="#"><i class="zmdi zmdi-star-outline"></i></a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </div><!-- END mail-item -->

                            <!-- a single mail -->
                            <div class="mail-item">
                                <table class="mail-container">
                                    <tbody><tr>
                                            <td class="mail-left">
                                                <div class="avatar avatar-lg avatar-circle">
                                                    <a href="#"><img src="../assets/images/210.jpg" alt="sender photo"></a>
                                                </div>
                                            </td>
                                            <td class="mail-center">
                                                <div class="mail-item-header">
                                                    <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">Sales Report 2014</a></h4>
                                                    <a href="#"><span class="label label-primary">work</span></a>
                                                </div>
                                                <p class="mail-item-excerpt">Lorem ipsum. ipsum dolor sit amet, consectetur adipisicing elit. Eveniet, accusamus</p>
                                            </td>
                                            <td class="mail-right">
                                                <p class="mail-item-date">2 hours ago</p>
                                                <p class="mail-item-star">
                                                    <a href="#"><i class="zmdi zmdi-star-outline"></i></a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </div><!-- END mail-item -->

                            <!-- a single mail -->
                            <div class="mail-item">
                                <table class="mail-container">
                                    <tbody><tr>
                                            <td class="mail-left">
                                                <div class="avatar avatar-lg avatar-circle">
                                                    <a href="#"><img src="../assets/images/211.jpg" alt="sender photo"></a>
                                                </div>
                                            </td>
                                            <td class="mail-center">
                                                <div class="mail-item-header">
                                                    <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">Sales Report 2014</a></h4>
                                                    <a href="#"><span class="label label-danger">business</span></a>
                                                </div>
                                                <p class="mail-item-excerpt">Lorem ipsum. ipsum dolor sit amet, consectetur adipisicing elit. Eveniet, accusamus</p>
                                            </td>
                                            <td class="mail-right">
                                                <p class="mail-item-date">Just now</p>
                                                <p class="mail-item-star starred">
                                                    <a href="#"><i class="zmdi zmdi-star"></i></a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </div><!-- END mail-item -->

                            <!-- a single mail -->
                            <div class="mail-item">
                                <table class="mail-container">
                                    <tbody><tr>
                                            <td class="mail-left">
                                                <div class="avatar avatar-lg avatar-circle">
                                                    <a href="#"><img src="../assets/images/212.jpg" alt="sender photo"></a>
                                                </div>
                                            </td>
                                            <td class="mail-center">
                                                <div class="mail-item-header">
                                                    <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">Sales Report 2014</a></h4>
                                                    <a href="#"><span class="label label-warning">personal</span></a>
                                                </div>
                                                <p class="mail-item-excerpt">Lorem ipsum. ipsum dolor sit consectetur adipisicing elit. Eveniet, accusamus</p>
                                            </td>
                                            <td class="mail-right">
                                                <p class="mail-item-date">a minute ago</p>
                                                <p class="mail-item-star">
                                                    <a href="#"><i class="zmdi zmdi-star-outline"></i></a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </div><!-- END mail-item -->

                            <!-- a single mail -->
                            <div class="mail-item">
                                <table class="mail-container">
                                    <tbody><tr>
                                            <td class="mail-left">
                                                <div class="avatar avatar-lg avatar-circle">
                                                    <a href="#"><img src="../assets/images/213.jpg" alt="sender photo"></a>
                                                </div>
                                            </td>
                                            <td class="mail-center">
                                                <div class="mail-item-header">
                                                    <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">Sales Report 2012</a></h4>
                                                    <a href="#"><span class="label label-primary">work</span></a>
                                                </div>
                                                <p class="mail-item-excerpt">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet, accusamus</p>
                                            </td>
                                            <td class="mail-right">
                                                <p class="mail-item-date">10 days ago</p>
                                                <p class="mail-item-star starred">
                                                    <a href="#"><i class="zmdi zmdi-star"></i></a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </div><!-- END mail-item -->

                            <!-- a single mail -->
                            <div class="mail-item">
                                <table class="mail-container">
                                    <tbody><tr>
                                            <td class="mail-left">
                                                <div class="avatar avatar-lg avatar-circle">
                                                    <a href="#"><img src="../assets/images/214.jpg" alt="sender photo"></a>
                                                </div>
                                            </td>
                                            <td class="mail-center">
                                                <div class="mail-item-header">
                                                    <h4 class="mail-item-title"><a href="mail-view.html" class="title-color">Sales Report 2011</a></h4>
                                                    <a href="#"><span class="label label-success">client</span></a>
                                                </div>
                                                <p class="mail-item-excerpt">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet, accusamus</p>
                                            </td>
                                            <td class="mail-right">
                                                <p class="mail-item-date">2 years ago</p>
                                                <p class="mail-item-star">
                                                    <a href="#"><i class="zmdi zmdi-star-outline"></i></a>
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </div><!-- END mail-item -->
                        </td>
                    </tr>
                </tbody></table>
        </div>
    </div><!-- END column -->
</div>
