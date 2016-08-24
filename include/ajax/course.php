<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/courseHandler.php';
require_once '../../include/handler/mediaHandler.php';
require_once '../../include/handler/certificatesHandler.php';

$courseHandler = new CourseHandler();
$mediaHandler = new MediaHandler();

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
            
        case "edit_course":
            $course_id = isset($_POST["course_id"]) ? $_POST["course_id"] : 0;
            $os_id = isset($_POST["os"]) ? $_POST["os"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $color = isset($_POST["color"]) ? $_POST["color"] : null;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $thumbnail = isset($_POST["thumbnail"]) ? $_POST["thumbnail"] : 0;
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->edit_course($course_id, $os_id, $points, $color, $sort_order, $thumbnail, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("COURSE_UPDATED");
            } else {
                $jsonArray["redirect"] = "find_course";
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
            $file_name = isset($_POST["file_name"]) ? $_POST["file_name"] : 0;
            $total_length = isset($_POST["total_length"]) ? $_POST["total_length"] : 0;

            if($courseHandler->create_lecture($course_id, $points, $difficulty, $sort_order, $title, $description, $language_ids, $file_name, $total_length)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("LECTURE_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "edit_lecture":
            $lecture_id = isset($_POST["lecture_id"]) ? $_POST["lecture_id"] : 0;
            $course_id = isset($_POST["course_id"]) ? $_POST["course_id"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $difficulty = isset($_POST["difficulty"]) ? $_POST["difficulty"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->edit_lecture($lecture_id, $course_id, $points, $difficulty, $sort_order, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("LECTURE_UPDATED");
            } else {
                $jsonArray["redirect"] = "find_lecture";
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
            $file_name = isset($_POST["file_name"]) ? $_POST["file_name"] : 0;
            $total_steps = isset($_POST["total_steps"]) ? $_POST["total_steps"] : 0;
            if($courseHandler->create_test($course_id, $points, $difficulty, $sort_order, $title, $description, $language_ids, $file_name, $total_steps)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("TEST_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "edit_test":
            $test_id = isset($_POST["test_id"]) ? $_POST["test_id"] : 0;
            $course_id = isset($_POST["course_id"]) ? $_POST["course_id"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $sort_order = isset($_POST["sort_order"]) ? $_POST["sort_order"] : 0;
            $difficulty = isset($_POST["difficulty"]) ? $_POST["difficulty"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->edit_test($test_id, $course_id, $points, $difficulty, $sort_order, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("TEST_UPDATED");
            } else {
                $jsonArray["redirect"] = "find_test";
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
            
        case "upload_test":
            $file = isset($_FILES["thumbnail_test"]) ? $_FILES["thumbnail_test"] : null;
            if($mediaHandler->upload_test($file)) {
                $jsonArray['status_value'] = true;
                $jsonArray['file_name'] = $mediaHandler->file_name;
                $jsonArray['success'] = TranslationHandler::get_static_text("TEST_UPLOADED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $mediaHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
            
        case "upload_lecture":
            $file = isset($_FILES["thumbnail_lecture"]) ? $_FILES["thumbnail_lecture"] : null;
            if($mediaHandler->upload_lecture($file)) {
                $jsonArray['status_value'] = true;
                $jsonArray['file_name'] = $mediaHandler->file_name . "." . $mediaHandler->compressed_file_type;
                $jsonArray['file_duration'] = $mediaHandler->file_duration;
                $jsonArray['success'] = TranslationHandler::get_static_text("LECTURE_UPLOADED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $mediaHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
    }
}

if(isset($_GET["get_courses"]) && isset($_GET["os_id"])) {
    if($courseHandler->get_multiple(0, "course", $_GET["os_id"])) {
        $jsonArray["courses"] = '<option value="0">'.TranslationHandler::get_static_text("BEGINNING").'</option>';
        for($i = 0; $i < count($courseHandler->courses); $i++) {
            $jsonArray["courses"] .= '<option value="'.$courseHandler->courses[$i]->sort_order.'">'.($i + 1).'. '.$courseHandler->courses[$i]->title.'</option>';
        }
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}

if(isset($_GET["get_lectures"]) && isset($_GET["course_id"])) {
    if($courseHandler->get_multiple($_GET["course_id"], "lecture")) {
        $jsonArray["lectures"] = '<option value="0">'.TranslationHandler::get_static_text("BEGINNING").'</option>';
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
    if($courseHandler->get_multiple($_GET["course_id"], "test")) {
        $jsonArray["tests"] = '<option value="0">'.TranslationHandler::get_static_text("BEGINNING").'</option>';
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
            $jsonArray["thumbnails"] .= '<div class="avatar avatar-xl thumbnail_element" thumbnail_id="' . $value['id'] . '" style="cursor:pointer;z-index:10;'. ($selected_thumbnail > 0 && $selected_thumbnail == $value['id'] ? '' : ($selected_thumbnail > 0 ? 'opacity: 0.5' : '')) .'"><div class="set_default_thumbnail '. (!$value["default_thumbnail"] ? 'hidden' : '') .'" '. ($value["default_thumbnail"] ? 'default_thumbnail="1"' : '') .' title="'.TranslationHandler::get_static_text("DEFAULT_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-home" style="display:initial !important;"></i></div><div class="delete_thumbnail delete_thumbnail_style hidden" title="'.TranslationHandler::get_static_text("DELETE_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-close" style="display:initial !important;"></i></div><img src="assets/images/thumbnails/' . $value['filename'] . '"/><div class="active_thumbnail '. ($selected_thumbnail > 0 && $selected_thumbnail == $value['id'] ? '' : 'hidden') .'" title="'.TranslationHandler::get_static_text("PICK_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-check" style="display:initial !important;"></i></div></div>';
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


if(isset($_GET["play_test"]) && isset($_GET["test_id"])) {
    if($courseHandler->get($_GET["test_id"], "test")) {
        $jsonArray['status_value'] = true;
        $jsonArray['user_course_table_id'] = $courseHandler->current_element->user_course_test_id;
        $jsonArray['current_progress'] = (($courseHandler->current_element->is_complete == 1) ? $courseHandler->current_element->total_steps : (isset($courseHandler->current_element->progress) ? $courseHandler->current_element->progress : "1"));
        $jsonArray['course_title'] = $courseHandler->current_element->course_title;
        $jsonArray['action_title'] = $courseHandler->current_element->title;
        $jsonArray['path'] = $courseHandler->current_element->path;
        $jsonArray['max_progress'] = $courseHandler->current_element->total_steps;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}

if(isset($_GET["play_lecture"]) && isset($_GET["lecture_id"])) {
    if($courseHandler->get($_GET["lecture_id"], "lecture")) {
        $jsonArray['status_value'] = true;
        $jsonArray['user_course_table_id'] = $courseHandler->current_element->user_course_lecture_id;
        $jsonArray['current_progress'] = (($courseHandler->current_element->is_complete == 1) ? $courseHandler->current_element->time_length : (isset($courseHandler->current_element->progress) ? $courseHandler->current_element->progress : "1"));
        $jsonArray['course_title'] = $courseHandler->current_element->course_title;
        $jsonArray['action_title'] = $courseHandler->current_element->title;
        $jsonArray['path'] = $courseHandler->current_element->path;
        $jsonArray['max_progress'] = $courseHandler->current_element->time_length;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $courseHandler->error->title;
    }
    echo json_encode($jsonArray);
}

if(isset($_GET["update_order"])) {
    $settingsHandler = new SettingsHandler();
    if($settingsHandler->update_course_show_order($_GET["update_order"])) {
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $settingsHandler->error->title;
    }
    echo json_encode($jsonArray);
}
    