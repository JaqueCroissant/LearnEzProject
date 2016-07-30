<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();

if(isset($_POST)) {
    $step = isset($_GET["step"]) ? $_GET["step"] : null;
    
    switch($step) {
        case "create_course":
            $os_id = isset($_POST["os"]) ? $_POST["os"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $color = isset($_POST["color"]) ? $_POST["color"] : null;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $thumbnail = isset($_POST["thumbnail"]) ? $_POST["thumbnail"] : 0;
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->create_course($os_id, $points, $color, $sort_order, $thumbnail, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("COURSE_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "create_lecture":
            $course_id = isset($_POST["course_id"]) ? $_POST["course_id"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $difficulty = isset($_POST["difficulty"]) ? $_POST["difficulty"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->create_lecture($course_id, $points, $sort_order, $difficulty, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("LECTURE_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "create_test":
            $course_id = isset($_POST["course_id"]) ? $_POST["course_id"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $difficulty = isset($_POST["difficulty"]) ? $_POST["difficulty"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->create_test($course_id, $points, $sort_order, $difficulty, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("TEST_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "delete":
            $type = isset($_POST["type"]) ? $_POST["type"] : null;
            $id = isset($_POST["id"]) ? $_POST["id"] : 0;
            if($courseHandler->delete($id, $type)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text(strtoupper($type)."_DELETED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "upload_thumbnail":
            $file = isset($_FILES["thumbnail_upload"]) ? $_FILES["thumbnail_upload"] : null;
            if($courseHandler->upload_thumbnail($file)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("THUMBNAIL_UPLOADED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
    }
}

if(isset($_GET["get_lectures"]) && isset($_GET["course_id"])) {
    if($courseHandler->get_lectures($_GET["course_id"])) {
        $jsonArray["lectures"] = "";
        for($i = 0; $i < count($courseHandler->lectures); $i++) {
            $jsonArray["lectures"] .= '<option value="'.$courseHandler->lectures[$i]->sort_order.'">'.($i + 1).'. '.$courseHandler->lectures[$i]->title.'</option>';
        }
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}

if(isset($_GET["get_tests"]) && isset($_GET["course_id"])) {
    if($courseHandler->get_tests($_GET["course_id"])) {
        $jsonArray["tests"] = "";
        for($i = 0; $i < count($courseHandler->tests); $i++) {
            $jsonArray["tests"] .= '<option value="'.$courseHandler->tests[$i]->sort_order.'">'.($i + 1).'. '.$courseHandler->tests[$i]->title.'</option>';
        }
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}

if(isset($_GET["get_thumbnails"])) {
    $thumbnails = $courseHandler->get_thumbnails();
    $selected_thumbnail = isset($_GET["selected_thumbnail"]) ? $_GET["selected_thumbnail"] : 0;
    if(!empty($thumbnails)) {
        $jsonArray["thumbnails"] = "";
        foreach($thumbnails as $value) {
            $jsonArray["thumbnails"] .= '<div class="avatar avatar-xl thumbnail_element" thumbnail_id="' . $value['id'] . '" style="cursor:pointer;z-index:10;'. ($selected_thumbnail > 0 && $selected_thumbnail == $value['id'] ? '' : ($selected_thumbnail > 0 ? 'opacity: 0.5' : '')) .'"><div class="set_default_thumbnail '. (!$value["default_thumbnail"] ? 'hidden' : '') .'" '. ($value["default_thumbnail"] ? 'default_thumbnail="1"' : '') .' title="'.TranslationHandler::get_static_text("DEFAULT_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-home" style="display:initial !important;"></i></div><div class="delete_thumbnail hidden" title="'.TranslationHandler::get_static_text("DELETE_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-close" style="display:initial !important;"></i></div><img src="assets/images/thumbnails/' . $value['filename'] . '"/><div class="active_thumbnail '. ($selected_thumbnail > 0 && $selected_thumbnail == $value['id'] ? '' : 'hidden') .'" title="'.TranslationHandler::get_static_text("PICK_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-check" style="display:initial !important;"></i></div></div>';
        }
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}

if(isset($_GET["set_default_thumbnail"]) && isset($_GET["thumbnail_id"])) {
    if($courseHandler->set_default_thumbnail($_GET["thumbnail_id"])) {
        $jsonArray['success'] = TranslationHandler::get_static_text("THUMBNAIL_DEFAULT_SET");
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}

if(isset($_GET["delete_thumbnail"]) && isset($_GET["thumbnail_id"])) {
    if($courseHandler->delete_thumbnail($_GET["thumbnail_id"])) {
        $jsonArray['success'] = TranslationHandler::get_static_text("THUMBNAIL_DELETED");
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}



if(isset($_GET["update_progress"])) {
    $type = $_GET["update_progress"];
    $progress = isset($_GET["progress"]) ? $_GET["progress"] : 0;
    $table_id = isset($_GET["table_id"]) ? $_GET["table_id"] : 0;
    $id = isset($_GET["action_id"]) ? $_GET["action_id"] : 0;
    $is_complete = isset($_GET["is_complete"]) ? $_GET["is_complete"] : 0;
    if ($courseHandler->update_progress($type, $progress, $is_complete, $table_id, $id)) {
        $jsonArray['last_inserted_id'] = $courseHandler->last_inserted_id;
        $jsonArray['status_value'] = true;
    }
    else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}
    