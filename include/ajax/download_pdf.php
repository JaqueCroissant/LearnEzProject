<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/certificatesHandler.php';
require_once '../../html2pdf/mpdf.php';

$certificatesHandler = new CertificatesHandler();
$current_user = SessionKeyHandler::get_from_session("user", true);
$step = isset($_GET["step"]) ? $_GET["step"] : null;

switch ($step) {
    case "download_single":
        if ($certificatesHandler->get_from_id(isset($_GET["element_id"]) ? $_GET["element_id"] : 0)) {
            $mpdf = new mPDF();
            $name = $current_user->firstname . " " . $current_user->surname;
            $title = $certificatesHandler->current_certificate->course_title;
            $code = $certificatesHandler->current_certificate->validation_code;
            include '../../html2pdf/certificate/certificate_html.php';
            $mpdf->WriteHTML($html);
            $file_name = md5(uniqid(mt_rand(), true)) . ".pdf";
            $mpdf->Output('../../html2pdf/tmp/' . $file_name, 'F');
            $jsonArray['status_value'] = true;
            $jsonArray['file_name'] = $file_name;
            $jsonArray["success"] = TranslationHandler::get_static_text("DOWNLOAD_PDF_SUCCESFUL");
        } else {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $certificatesHandler->error->title;
        }
        break;

    case "download_multiple":
        if (isset($_POST)) {
            if ($certificatesHandler->get_multiple_by_id(isset($_POST["certificate"]) ? $_POST["certificate"] : array())) {
                $file_names = [];

                foreach ($certificatesHandler->certificates as $value) {
                    $mpdf = new mPDF();
                    $name = $current_user->firstname . " " . $current_user->surname;
                    $title = $value->course_title;
                    $code = $value->validation_code;
                    include '../../html2pdf/certificate/certificate_html.php';
                    $mpdf->WriteHTML($html);
                    $file_name = md5(uniqid(mt_rand(), true)) . ".pdf";
                    $file_names[] = $file_name;
                    $mpdf->Output('../../html2pdf/tmp/' . $file_name, 'F');
                }

                $jsonArray['file_names'] = $file_names;
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("DOWNLOAD_PDFS_SUCCESFUL");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $certificatesHandler->error->title;
            }
        } else {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $certificatesHandler->error->title;
        }
        break;

    default:
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $certificatesHandler->error->title;
        die();
        break;
}

echo json_encode($jsonArray);
?>