<?php
require_once 'require.php';
require_once '../../include/handler/pageHandler.php';
$pageHandler = new PageHandler(true);
$rightsHandler = new RightsHandler();

if (RightsHandler::has_user_right("RIGHTS")) {

    $ordered_pages = $pageHandler->fetch_ordered_pages();
    $rights_categories = $pageHandler->fetch_rights_page_categories();
    $rightsHandler->get_all_rights();
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="widget">
                <div class="m-b-lg nav-tabs-horizontal">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" id="SUPER_ADMIN"><a href="#tab-1" class="my_tab_header" data-toggle="tab"><?php echo TranslationHandler::get_static_text("SUPER_ADMIN"); ?></a></li>
                        <li role="presentation" id="ADMIN"><a href="#tab-2"  class="my_tab_header" data-toggle="tab"><?php echo TranslationHandler::get_static_text("ADMIN"); ?></a></li>
                        <li role="presentation" id="TEACHER"><a href="#tab-3"  class="my_tab_header" data-toggle="tab"><?php echo TranslationHandler::get_static_text("TEACHER"); ?></a></li>
                        <li role="presentation" id="STUDENT"><a href="#tab-4"  class="my_tab_header" data-toggle="tab"><?php echo TranslationHandler::get_static_text("STUDENT"); ?></a></li>
                    </ul>
                    <div class="my_tab_content p-md">

                        <?php
                        for ($i = 1; $i < 5; $i++) {
                            ?>
                            <div class="my_fade my_tab" id="tab-<?php echo $i; ?>">
                                <?php
                                if ($pageHandler->get_page_rights($i) && $rightsHandler->get_user_type_rights($i)) {
                                    ?>

                                    <header class="widget-header">
                                        <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("PAGE_RIGHTS"); ?></h4>
                                    </header>
                                    <hr class="widget-separator">
                                    <div class="widget-body">
                                        <form method="POST" action="" id="page_rights_form_<?php echo $i; ?>" url="rights.php" class="" name="rights">
                                            <input type="hidden" name="user_type_id" value="<?php echo $i; ?>">
                                            <div style="display: inline-block; vertical-align: top; min-width:230px;">
                                                <ul class="treeview">
                                                    <?php
                                                    $counter = 0;
                                                    $last_page_subpage_level = array();

                                                    foreach ($ordered_pages as $page) {
                                                        if ($page->hide_in_backend) {
                                                            continue;
                                                        }


                                                        if (empty($page->master_page_id)) {
                                                            $counter = $counter + 1 + $page->total_children;
                                                        }

                                                        $has_subpage = (count($page->children) > 0) ? true : false;

                                                        if (count($last_page_subpage_level) > 0) {
                                                            $bool = true;
                                                            while ($bool) {
                                                                if (empty($last_page_subpage_level)) {
                                                                    $bool = false;
                                                                    break;
                                                                }

                                                                $last_subpage_id = array_pop((array_slice($last_page_subpage_level, -1)));

                                                                if ($last_subpage_id != $page->master_page_id) {
                                                                    echo '</ul><div style="height:20px"></div>';
                                                                    array_shift($last_page_subpage_level);
                                                                } else {
                                                                    $bool = false;
                                                                }
                                                            }
                                                        }
                                                        if (empty($last_page_subpage_level) && $counter > 10) {
                                                            echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:230px;"><ul class="treeview">';
                                                            $counter = $page->total_children;
                                                        }
                                                        if ($has_subpage) {

                                                            array_push($last_page_subpage_level, $page->id);
                                                        }


                                                        echo '<li><div class="checkbox" ' . ($has_subpage ? 'style="margin-top:0px"' : '') . '>
                                                                <input type="checkbox" name="page_rights[]" ' . (empty($page->master_page_id) ? 'class="check_all_specific" checkbox_id="' . $page->id . '"' : 'class="master_check_box_' . array_shift(array_values($last_page_subpage_level)) . '"') . ' value="' . $page->id . '" id="page_' . $page->pagename . '' . $page->step . '" ' . (array_key_exists($page->id, $pageHandler->page_rights) ? 'checked' : '') . '>
                                                                <label for="page_' . $page->pagename . '' . $page->step . '">' . (($has_subpage && count($last_page_subpage_level) < 2) || !($page->master_page_id > 0) ? '<b>' : '') . '' . $page->title . '' . (($has_subpage && count($last_page_subpage_level) < 2) || !($page->master_page_id > 0) ? '</b>' : '') . '</label>
                                                          </div>';
                                                        echo $has_subpage ? '<ul>' : '</li>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div style="clear:both;"></div>

                                            <input type="button" id="submit_button" name="submit" class="submit_change_rights pull-right btn btn-default btn-sm" value="<?php echo TranslationHandler::get_static_text("SAVE_CHANGES"); ?>"> 
                                        </form>
                                    </div>
                                    <header class="widget-header">
                                        <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("GENERAL_RIGHTS"); ?></h4>
                                    </header>
                                    <hr class="widget-separator">

                                    <div class="widget-body">
                                        <form method="POST" action="" id="rights_form_<?php echo $i; ?>" url="rights.php" class="" name="rights">
                                            <input type="hidden" name="user_type_id" value="<?php echo $i; ?>">
                                            <input type="hidden" name="rights_type" value="1">
                                            <div style="display: inline-block; vertical-align: top; min-width:230px;">
                                                <ul class="treeview">
                                                    <?php
                                                    $counter = 0;
                                                    foreach ($rights_categories as $category) {
                                                        if (!isset($rightsHandler->category_rights[$category->id]) || !is_array($rightsHandler->category_rights[$category->id]) || count($rightsHandler->category_rights[$category->id]) < 1) {
                                                            $counter = $counter + 1;
                                                            if ($counter > 11) {
                                                                echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:230px;"><ul class="treeview">';
                                                                $counter = 0;
                                                            }
                                                            echo '<li><div class="checkbox">
                                                                <input type="checkbox" id="rights_category_' . $category->id . '" checked>
                                                                <label for="rights_category_' . $category->id . '"><b>' . $category->title . '</b></label>
                                                              </div></li>';
                                                            continue;
                                                        }

                                                        $counter = $counter + 1 + count($rightsHandler->category_rights[$category->id]);
                                                        if ($counter > 11) {
                                                            echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:230px;"><ul class="treeview">';
                                                            $counter = 1 + count($rightsHandler->category_rights[$category->id]);
                                                        }

                                                        $all_checked = true;
                                                        $display_rights = "";
                                                        foreach ($rightsHandler->category_rights[$category->id] as $right) {
                                                            if (!array_key_exists($right->id, $rightsHandler->user_type_rights)) {
                                                                $all_checked = false;
                                                            }
                                                            $display_rights .= '<li><div class="checkbox">
                                                                                <input type="checkbox" class="master_check_box_' . $category->id . '" name="rights[]" value="' . $right->id . '" id="right_' . $right->id . '" ' . (array_key_exists($right->id, $rightsHandler->user_type_rights) ? 'checked' : '') . '>
                                                                                <label for="right_' . $right->id . '">' . $right->title . '</label>
                                                                            </div>';
                                                        }
                                                        echo '<li><div class="checkbox">
                                                            <input type="checkbox" class="check_all_specific" checkbox_id="' . $category->id . '" id="rights_category_' . $category->id . '" ' . ($all_checked ? 'checked' : '') . '>
                                                            <label for="rights_category_' . $category->id . '"><b>' . $category->title . '</b></label>
                                                          </div><ul>';
                                                        echo $display_rights;
                                                        echo '</ul><div style="height:20px"></div>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div style="clear:both;"></div>

                                            <input type="button" id="submit_button" name="submit" class="submit_change_rights pull-right btn btn-default btn-sm" value="<?php echo TranslationHandler::get_static_text("SAVE_CHANGES"); ?>"> 
                                        </form>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <?php
    } elseif (RightsHandler::has_user_right("SCHOOL_RIGHTS")) {
        $rights_categories = $pageHandler->fetch_rights_page_categories();
        $rightsHandler->get_all_rights(true);
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="widget">
                <div class="m-b-lg nav-tabs-horizontal">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" id="ADMIN"><a href="#tab-2"  class="my_tab_header" data-toggle="tab"><?php echo TranslationHandler::get_static_text("ADMIN"); ?></a></li>
                        <li role="presentation" id="TEACHER"><a href="#tab-3"  class="my_tab_header" data-toggle="tab"><?php echo TranslationHandler::get_static_text("TEACHER"); ?></a></li>
                        <li role="presentation" id="STUDENT"><a href="#tab-4"  class="my_tab_header" data-toggle="tab"><?php echo TranslationHandler::get_static_text("STUDENT"); ?></a></li>
                    </ul>
                    <div class="my_tab_content p-md">

                        <?php
                        for ($i = 2; $i < 5; $i++) {
                            ?>
                            <div class="my_fade my_tab" id="tab-<?php echo $i; ?>">
                                <?php
                                if ($rightsHandler->get_school_rights($i)) {
                                    ?>

                                    <div class="widget-body">
                                        <form method="POST" action="" id="rights_form_<?php echo $i; ?>" url="rights.php" class="" name="rights">
                                            <input type="hidden" name="user_type_id" value="<?php echo $i; ?>">
                                            <input type="hidden" name="school_rights" value="1">
                                            <div style="display: inline-block; vertical-align: top; min-width:230px;">
                                                <ul class="treeview">
                                                    <?php
                                                    $counter = 0;
                                                    foreach ($rights_categories as $category) {
                                                        if (!isset($rightsHandler->category_rights[$category->id]) || !is_array($rightsHandler->category_rights[$category->id]) || count($rightsHandler->category_rights[$category->id]) < 1) {
                                                            $counter = $counter + 1;
                                                            if ($counter > 11) {
                                                                echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:230px;"><ul class="treeview">';
                                                                $counter = 0;
                                                            }
                                                            continue;
                                                        }

                                                        $counter = $counter + 1 + count($rightsHandler->category_rights[$category->id]);
                                                        if ($counter > 11) {
                                                            echo '</ul></div><div style="display: inline-block; vertical-align: top;  min-width:230px;"><ul class="treeview">';
                                                            $counter = 1 + count($rightsHandler->category_rights[$category->id]);
                                                        }

                                                        $all_checked = true;
                                                        $display_rights = "";
                                                        foreach ($rightsHandler->category_rights[$category->id] as $right) {
                                                            $right_exists = array_key_exists($right->id, $rightsHandler->school_rights) && $rightsHandler->school_rights[$right->id] == $i;
                                                            if(!$right_exists) {
                                                                $all_checked = false;
                                                            }
                                                            $display_rights .= '<li><div class="checkbox">
                                                                                <input type="checkbox" class="master_check_box_' . $category->id . '" name="rights[]" value="' . $right->id . '" id="right_' . $right->id . '" ' . ($right_exists ? 'checked' : '') . '>
                                                                                <label for="right_' . $right->id . '">' . $right->title . '</label>
                                                                            </div>';
                                                        }
                                                        echo '<li><div class="checkbox">
                                                            <input type="checkbox" class="check_all_specific" checkbox_id="' . $category->id . '" id="rights_category_' . $category->id . '" ' . ($all_checked ? 'checked' : '') . '>
                                                            <label for="rights_category_' . $category->id . '"><b>' . $category->title . '</b></label>
                                                          </div><ul>';
                                                        echo $display_rights;
                                                        echo '</ul><div style="height:20px"></div>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <div style="clear:both;"></div>

                                            <input type="button" id="submit_button" name="submit" class="submit_change_rights pull-right btn btn-default btn-sm" value="<?php echo TranslationHandler::get_static_text("SAVE_CHANGES"); ?>"> 
                                        </form>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
}
?>
<script src="js/my_tab.js" type="text/javascript"></script>