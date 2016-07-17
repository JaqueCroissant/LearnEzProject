<?php
session_start();
require_once '../../include/extra/global.function.php';
require_once '../../include/extra/db.class.php';
require_once '../../include/handler/handler.php';
require_once '../../include/handler/dbHandler.php';
require_once '../../include/handler/sessionKeyHandler.php';
require_once '../../include/handler/errorHandler.php';
require_once '../../include/handler/translationHandler.php';
require_once '../../include/handler/paginationHandler.php';
require_once '../../include/handler/rightsHandler.php';

require_once '../../include/class/orm.class.php';
require_once '../../include/class/error.class.php';
require_once '../../include/class/notification.class.php';
require_once '../../include/class/page.class.php';
require_once '../../include/class/rights.class.php';
require_once '../../include/class/school.class.php';
require_once '../../include/class/school_class.class.php';
require_once '../../include/class/user.class.php';
require_once '../../include/class/mail.class.php';
require_once '../../include/class/mail_folder.class.php';
require_once '../../include/class/mail_tag.class.php';

if($_POST) {
    /// TEMPORARY
    try {
    if(isset($_POST["create_right"])) {
        $right_category = (isset($_POST["right_category"]) ? $_POST["right_category"] : null);
        $right_prefix = (isset($_POST["right_prefix"]) ? $_POST["right_prefix"] : null);
        $danish = (isset($_POST["danish"]) ? $_POST["danish"] : null);
        $english = (isset($_POST["english"]) ? $_POST["english"] : null);
        $right_page_id = (isset($_POST["right_page_id"]) ? $_POST["right_page_id"] : 0);

        if(empty($right_category) ||empty($right_prefix) || empty($danish) ||empty($english)) {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = "FILL FIRST FOUR FIELDS";
            echo json_encode($jsonArray);
            throw new Exception();
        }
        
        if(DbHandler::get_instance()->count_query("SELECT * FROM rights WHERE prefix = :prefix", $right_prefix) > 0) {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = "PREFIX ALREADY EXISTS";
            echo json_encode($jsonArray);
            throw new Exception();
        }
        
        DbHandler::get_instance()->query("INSERT INTO rights (prefix, page_right_id, page_category_id) VALUES (:prefix, :page_right_id, :page_category_id)", $right_prefix, $right_page_id, $right_category);
        $right_id = DbHandler::get_instance()->last_inserted_id();
        DbHandler::get_instance()->query("INSERT INTO translation_rights (language_id, rights_id, title) VALUES ('1', :right_id, :danish)", $right_id, $danish);
        DbHandler::get_instance()->query("INSERT INTO translation_rights (language_id, rights_id, title) VALUES ('2', :right_id, :danish)", $right_id, $english);
        $jsonArray['status_value'] = true;
        echo json_encode($jsonArray);
    }
    } catch(Exception $ex) {
        echo $ex->getMessage();
    }
    /// TEMPORARY
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <form method="POST" action="" id="create_right_form"  name="create_right">
                <input type="hidden" name="create_right" value="1"/>
                <div class="panel-body">

                    <div class="form-group m-b-sm">
                        <label for="right_category1" class="control-label">Rettighedkategori</label>
                        <select id="right_category1" name="right_category" class="form-control">

                            <?php
                            $data = DbHandler::get_instance()->return_query("SELECT page.id, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id WHERE backend_category = '1' AND translation_page.language_id = :lang_id", TranslationHandler::get_current_language());
                            foreach ($data as $category) {
                                echo '<option value="' . $category["id"] . '">' . $category["title"] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group m-b-sm">
                        <input type="text" class="form-control" placeholder="PREFIX" name="right_prefix"/>
                    </div>
                    
                    <div class="form-group m-b-sm">
                        <input type="text" class="form-control" placeholder="DANSK OVERSÆTTELSE" name="danish"/>
                    </div>
                    
                    <div class="form-group m-b-sm">
                        <input type="text" class="form-control" placeholder="ENGELSK OVERSÆTTELSE" name="english"/>
                    </div>
                    
                    <div class="form-group m-b-sm">
                        <label for="right_category2" class="control-label">Påknyttet side (SKJULER RETTIGHEDEN - TILDELES AUTOMATISK HVIS BRUGEREN HAR RETTIGHED TIL SIDEN)</label>
                        <select id="right_category2" name="right_page_id" class="form-control">
                            <option value="0">Ingen valgt.</option>
                            <?php
                            $data = DbHandler::get_instance()->return_query("SELECT page.id, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id WHERE hide_in_backend = '0' AND master_page_id = '0' AND translation_page.language_id = :lang_id ORDER BY page.backend_sort_order ASC", TranslationHandler::get_current_language());
                            foreach ($data as $category) {
                                echo '<option value="' . $category["id"] . '">' . $category["title"] . '</option>';
                                
                                $data2 = DbHandler::get_instance()->return_query("SELECT page.id, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id WHERE hide_in_backend = '0' AND master_page_id = :master_page_id AND translation_page.language_id = :lang_id ORDER BY page.backend_sort_order ASC", $category["id"], TranslationHandler::get_current_language());
                                foreach ($data2 as $category2) {
                                    echo '<option value="' . $category2["id"] . '">----' . $category2["title"] . '</option>';
                                    
                                    $data3 = DbHandler::get_instance()->return_query("SELECT page.id, translation_page.title FROM page INNER JOIN translation_page ON translation_page.page_id = page.id WHERE hide_in_backend = '0' AND master_page_id = :master_page_id AND translation_page.language_id = :lang_id ORDER BY page.backend_sort_order ASC", $category2["id"], TranslationHandler::get_current_language());
                                    foreach ($data3 as $category3) {
                                        echo '<option value="' . $category3["id"] . '">---------' . $category3["title"] . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <input type="submit" name="submit" value="opret" class="submit_change_rights"/>
            </form>
        </div>
    </div>
</div>