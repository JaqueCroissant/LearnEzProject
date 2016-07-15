<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
$pageHandler = new PageHandler(true);
?>


<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab-1"  role="tab" data-toggle="tab"><?php echo TranslationHandler::get_static_text("SUPER_ADMIN"); ?></a></li>
                    <li role="presentation"><a href="#tab-2"  role="tab" data-toggle="tab"><?php echo TranslationHandler::get_static_text("ADMIN"); ?></a></li>
                    <li role="presentation"><a href="#tab-3"  role="tab" data-toggle="tab"><?php echo TranslationHandler::get_static_text("TEACHER"); ?></a></li>
                     <li role="presentation"><a href="#tab-4"  role="tab" data-toggle="tab"><?php echo TranslationHandler::get_static_text("STUDENT"); ?></a></li>
                </ul>
                <div class="tab-content p-md">
                    <div role="tabpanel" class="tab-pane in active fade" id="tab-1">
                        <?php
                            if($pageHandler->get_page_rights(1)) {
                        ?>
                        <form method="POST" action="" id="rights_form_1" url="rights.php" class="" name="rights">
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PAGE_RIGHTS"); ?></h4>
                        </header>
                        <hr class="widget-separator">
                        <div class="widget-body">
                        
                            
                            <input type="hidden" name="user_type_id" value="1">
                            <div style="display: inline-block; vertical-align: top; min-width:200px;">
                            <ul class="treeview">
                            <?php
                            $counter = 0;
                            $last_page_subpage_level = array();

                            foreach($pageHandler->fetch_ordered_pages() as $page) {
                                if($page->hide_in_backend) {
                                    continue;
                                }
                                
                                $counter = $counter + 1 + count($page->children);
                                
                                $has_subpage = (count($page->children) > 0) ? true : false;

                                if(count($last_page_subpage_level) > 0) {
                                    $bool = true;
                                    while($bool) {
                                        if(empty($last_page_subpage_level)) {
                                            $bool = false;
                                            break;
                                        }

                                        $last_subpage_id = array_pop((array_slice($last_page_subpage_level, -1)));

                                        if($last_subpage_id != $page->master_page_id) {
                                            echo '</ul><div style="height:20px"></div>';
                                            array_shift($last_page_subpage_level);
                                        } else {
                                            $bool = false;
                                        }
                                    }
                                }
                                
                                if($has_subpage) {
                                    if(empty($last_page_subpage_level) && $counter > 9) {
                                       echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:200px;"><ul class="treeview">';
                                       $counter = 0; 
                                    }
                                    array_push($last_page_subpage_level, $page->id);
                                }


                                echo '
                                    <li>
                                        <div class="checkbox" '. ($has_subpage ? 'style="margin-top:0px"' : '') .'>
                                            <input type="checkbox" name="page_rights[]" value="'. $page->id .'" id="page_'.$page->pagename.''.$page->step.'" '. (array_key_exists($page->id, $pageHandler->page_rights) ? 'checked' : '') .'>
                                            <label for="page_'.$page->pagename.''.$page->step.'">'. (($has_subpage && count($last_page_subpage_level) < 2) || !($page->master_page_id > 0) ? '<b>' : '') .''.$page->title.''. (($has_subpage && count($last_page_subpage_level) < 2) || !($page->master_page_id > 0) ? '</b>' : '') .'</label>
                                        </div>';
                                echo $has_subpage ? '<ul>' : '</li>';
                            }
                            ?>
                        </ul>
                        </div>
                        <div style="clear:both;"></div>
                        
                        
                        </div>
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("GENERAL_RIGHTS"); ?></h4>
                        </header>
                        <hr class="widget-separator">
                        <div class="widget-body">

                        <input type="button" id="submit_button" name="submit" class="submit_change_rights" value="change"> 
                        </div>
                        </form>
                        
                        <?php
                            }
                        ?>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="tab-2">
                        <?php
                            if($pageHandler->get_page_rights(2)) {
                        ?>
                        <form method="POST" action="" id="rights_form_2" url="rights.php" class="" name="rights">
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PAGE_RIGHTS"); ?></h4>
                        </header>
                        <hr class="widget-separator">
                        <div class="widget-body">
                        
                            
                            <input type="hidden" name="user_type_id" value="2">
                            <div style="display: inline-block; vertical-align: top; min-width:200px;">
                            <ul class="treeview">
                            <?php
                            $counter = 0;
                            $last_page_subpage_level = array();

                            foreach($pageHandler->fetch_ordered_pages() as $page) {
                                if($page->hide_in_backend) {
                                    continue;
                                }
                                
                                $counter = $counter + 1 + count($page->children);
                                
                                $has_subpage = (count($page->children) > 0) ? true : false;

                                if(count($last_page_subpage_level) > 0) {
                                    $bool = true;
                                    while($bool) {
                                        if(empty($last_page_subpage_level)) {
                                            $bool = false;
                                            break;
                                        }

                                        $last_subpage_id = array_pop((array_slice($last_page_subpage_level, -1)));

                                        if($last_subpage_id != $page->master_page_id) {
                                            echo '</ul><div style="height:20px"></div>';
                                            array_shift($last_page_subpage_level);
                                        } else {
                                            $bool = false;
                                        }
                                    }
                                }
                                
                                if($has_subpage) {
                                    if(empty($last_page_subpage_level) && $counter > 8) {
                                       echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:200px;"><ul class="treeview">';
                                       $counter = 0; 
                                    }
                                    array_push($last_page_subpage_level, $page->id);
                                }


                                echo '
                                    <li>
                                        <div class="checkbox" '. ($has_subpage ? 'style="margin-top:0px"' : '') .'>
                                            <input type="checkbox" name="page_rights[]" value="'. $page->id .'" id="page_'.$page->pagename.''.$page->step.'" '. (array_key_exists($page->id, $pageHandler->page_rights) ? 'checked' : '') .'>
                                            <label for="page_'.$page->pagename.''.$page->step.'">'. ($has_subpage || !($page->master_page_id > 0) ? '<b>' : '') .''.$page->title.''. ($has_subpage || !($page->master_page_id > 0) ? '</b>' : '') .'</label>
                                        </div>';
                                echo $has_subpage ? '<ul>' : '</li>';
                            }
                            ?>
                        </ul>
                        </div>
                        <div style="clear:both;"></div>
                        
                        
                        </div>
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("GENERAL_RIGHTS"); ?></h4>
                        </header>
                        <hr class="widget-separator">
                        <div class="widget-body">

                        <input type="button" id="submit_button" name="submit" class="submit_change_rights" value="change"> 
                        </div>
                        </form>
                        
                        <?php
                            }
                        ?>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane fade" id="tab-3">
                        <?php
                            if($pageHandler->get_page_rights(3)) {
                        ?>
                        <form method="POST" action="" id="rights_form_3" url="rights.php" class="" name="rights">
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PAGE_RIGHTS"); ?></h4>
                        </header>
                        <hr class="widget-separator">
                        <div class="widget-body">
                        
                            
                            <input type="hidden" name="user_type_id" value="3">
                            <div style="display: inline-block; vertical-align: top; min-width:200px;">
                            <ul class="treeview">
                            <?php
                            $counter = 0;
                            $last_page_subpage_level = array();

                            foreach($pageHandler->fetch_ordered_pages() as $page) {
                                if($page->hide_in_backend) {
                                    continue;
                                }
                                
                                $counter = $counter + 1 + count($page->children);
                                
                                $has_subpage = (count($page->children) > 0) ? true : false;

                                if(count($last_page_subpage_level) > 0) {
                                    $bool = true;
                                    while($bool) {
                                        if(empty($last_page_subpage_level)) {
                                            $bool = false;
                                            break;
                                        }

                                        $last_subpage_id = array_pop((array_slice($last_page_subpage_level, -1)));

                                        if($last_subpage_id != $page->master_page_id) {
                                            echo '</ul><div style="height:20px"></div>';
                                            array_shift($last_page_subpage_level);
                                        } else {
                                            $bool = false;
                                        }
                                    }
                                }
                                
                                if($has_subpage) {
                                    if(empty($last_page_subpage_level) && $counter > 8) {
                                       echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:200px;"><ul class="treeview">';
                                       $counter = 0; 
                                    }
                                    array_push($last_page_subpage_level, $page->id);
                                }


                                echo '
                                    <li>
                                        <div class="checkbox" '. ($has_subpage ? 'style="margin-top:0px"' : '') .'>
                                            <input type="checkbox" name="page_rights[]" value="'. $page->id .'" id="page_'.$page->pagename.''.$page->step.'" '. (array_key_exists($page->id, $pageHandler->page_rights) ? 'checked' : '') .'>
                                            <label for="page_'.$page->pagename.''.$page->step.'">'. ($has_subpage || !($page->master_page_id > 0) ? '<b>' : '') .''.$page->title.''. ($has_subpage || !($page->master_page_id > 0) ? '</b>' : '') .'</label>
                                        </div>';
                                echo $has_subpage ? '<ul>' : '</li>';
                            }
                            ?>
                        </ul>
                        </div>
                        <div style="clear:both;"></div>
                        
                        
                        </div>
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("GENERAL_RIGHTS"); ?></h4>
                        </header>
                        <hr class="widget-separator">
                        <div class="widget-body">

                        <input type="button" id="submit_button" name="submit" class="submit_change_rights" value="change"> 
                        </div>
                        </form>
                        
                        <?php
                            }
                        ?>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane fade" id="tab-4">
                        <?php
                            if($pageHandler->get_page_rights(4)) {
                        ?>
                        <form method="POST" action="" id="rights_form_4" url="rights.php" class="" name="rights">
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PAGE_RIGHTS"); ?></h4>
                        </header>
                        <hr class="widget-separator">
                        <div class="widget-body">
                        
                            
                            <input type="hidden" name="user_type_id" value="4">
                            <div style="display: inline-block; vertical-align: top; min-width:200px;">
                            <ul class="treeview">
                            <?php
                            $counter = 0;
                            $last_page_subpage_level = array();

                            foreach($pageHandler->fetch_ordered_pages() as $page) {
                                if($page->hide_in_backend) {
                                    continue;
                                }
                                
                                $counter = $counter + 1 + count($page->children);
                                
                                $has_subpage = (count($page->children) > 0) ? true : false;

                                if(count($last_page_subpage_level) > 0) {
                                    $bool = true;
                                    while($bool) {
                                        if(empty($last_page_subpage_level)) {
                                            $bool = false;
                                            break;
                                        }

                                        $last_subpage_id = array_pop((array_slice($last_page_subpage_level, -1)));

                                        if($last_subpage_id != $page->master_page_id) {
                                            echo '</ul><div style="height:20px"></div>';
                                            array_shift($last_page_subpage_level);
                                        } else {
                                            $bool = false;
                                        }
                                    }
                                }
                                
                                if($has_subpage) {
                                    if(empty($last_page_subpage_level) && $counter > 8) {
                                       echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:200px;"><ul class="treeview">';
                                       $counter = 0; 
                                    }
                                    array_push($last_page_subpage_level, $page->id);
                                }


                                echo '
                                    <li>
                                        <div class="checkbox" '. ($has_subpage ? 'style="margin-top:0px"' : '') .'>
                                            <input type="checkbox" name="page_rights[]" value="'. $page->id .'" id="page_'.$page->pagename.''.$page->step.'" '. (array_key_exists($page->id, $pageHandler->page_rights) ? 'checked' : '') .'>
                                            <label for="page_'.$page->pagename.''.$page->step.'">'. ($has_subpage || !($page->master_page_id > 0) ? '<b>' : '') .''.$page->title.''. ($has_subpage || !($page->master_page_id > 0) ? '</b>' : '') .'</label>
                                        </div>';
                                echo $has_subpage ? '<ul>' : '</li>';
                            }
                            ?>
                        </ul>
                        </div>
                        <div style="clear:both;"></div>
                        
                        
                        </div>
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("GENERAL_RIGHTS"); ?></h4>
                        </header>
                        <hr class="widget-separator">
                        <div class="widget-body">

                        <input type="button" id="submit_button" name="submit" class="submit_change_rights" value="change"> 
                        </div>
                        </form>
                        
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
