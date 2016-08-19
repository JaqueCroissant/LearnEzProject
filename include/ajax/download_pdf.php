<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/certificatesHandler.php';
    require_once '../../html2pdf/mpdf.php';
    
    $certificatesHandler = new CertificatesHandler();

    $step = isset($_GET["step"]) ? $_GET["step"] : null;

    $html = '
    <p style="font-family: pacifico;">HELLO CUSTOM</p>
    <p style="font-family: alexbrush;">HELLO CUSTOM</p>
    <p style="font-family: amaranth;">HELLO CUSTOMffFFf</p>
    <p style="font-family: amaranth;font-weight:bold;">HELLO CUSTOMfffFFff</p>
    <p style="font-family: amaranth;font-style:italic;">HELLO CUSTOM</p>
    <p style="font-family: amaranth;font-weight:bold;font-style:italic;">HELLO CUSTOM</p>';



    switch($step) {
        case "download_single":
            $mpdf = new mPDF(); 
            $mpdf->WriteHTML($html);
            $file_name = md5(uniqid(mt_rand(), true)) . ".pdf";
            $mpdf->Output('../../html2pdf/tmp/' . $file_name, 'F');
            $jsonArray['status_value'] = true;
            $jsonArray['file_name'] = $file_name;
            $jsonArray["success"] = TranslationHandler::get_static_text("DOWNLOAD_PDF_SUCCESFUL");
            break;
        
        case "send_pdf":
            $path = realpath(__DIR__ . '/../..') . "/html2pdf/tmp/";
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'. $path . basename($_GET["file"]).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path . $_GET["file"]));
            readfile($path . $_GET["file"]);
            exit();
            break;

        default:
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $certificatesHandler->error->title;
            die();
            break;

    }

    echo json_encode($jsonArray);



?>