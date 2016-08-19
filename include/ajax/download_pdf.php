<?php

require_once '../../include/ajax/require.php';
require_once '../../include/handler/certificatesHandler.php';
require_once '../../html2pdf/mpdf.php';

$certificatesHandler = new CertificatesHandler();

$step = isset($_GET["step"]) ? $_GET["step"] : null;

$title = "Microsoft Excel 2016 Certificate";
$name = "Niels Nielsen";
$html = '<html>
        <head>
            <title>Certificate</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="../../html2pdf/certificate/certificate.css" type="text/css" />
        </head>
        <body>
            <page size="A4"></page>
            
            <div class="course_text_container">
                <div class="' . (strlen($name) > 32 ? "course_text_small" : "course_text_large") . '">' . $name . '</div>
            </div>
            
            <div class="name_text_container">
                <div class="' . (strlen($name) > 16 ? "name_text_small" : "name_text_large") . '">'. $name .'</div>
            </div>
            
            <div class="info_text_container">
                <div class="info_text">Has successfully completed the course for Microsoft Excel 2016. asdf asdf sadf asdf asd fs</div>
            </div>
            
            <div class="certificate_id_text_container" ' . (strlen($name) > 16 ? 'style="padding-top:90px !important;"' : '') . '>
                <div class="certificate_id_text">Certificate ID:</div>
            </div>
            
            <div class="certificate_id_container">
                <div class="certificate_id">H43D-FT2M-EM32-OD4Q-6YLX</div>
            </div>
            
            <div style="width:100%;">
                <div class="date_awarded_text"><div>Date awarded:</div><div style="margin-top:-5px;font-size:26px;">26-07-2015</div></div>
                <div class="nazer_text"><img src="../../html2pdf/certificate/underskrift.png" style="width:200px;height:70px;" /><br />Nazer Mehali</div>
                <div style="clear:both;"></div>
            </div>
            
        </body>
    </html>';




switch ($step) {
    case "download_single":
        $mpdf = new mPDF();
        $mpdf->WriteHTML($html);
        $file_name = md5(uniqid(mt_rand(), true)) . ".pdf";
        $mpdf->Output('../../html2pdf/tmp/' . $file_name, 'F');
        $jsonArray['status_value'] = true;
        $jsonArray['file_name'] = $file_name;
        $jsonArray["success"] = TranslationHandler::get_static_text("DOWNLOAD_PDF_SUCCESFUL");
        break;

    case "download_multiple":
        if (isset($_POST)) {
            if ($certificatesHandler->get_multiple_by_id(isset($_POST["certificate"]) ? $_POST["certificate"] : array())) {
                $file_names = [];

                foreach ($certificatesHandler->certificates as $value) {
                    $mpdf = new mPDF();
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