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
        
        case "download_multiple":
            if(isset($_POST)) {
                if($certificatesHandler->get_multiple_by_id(isset($_POST["certificate"]) ? $_POST["certificate"] : array())) {
                    $mpdf = new mPDF(); 
                    $file_names = [];
                    
                    foreach($certificatesHandler->certificates as $value) {
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