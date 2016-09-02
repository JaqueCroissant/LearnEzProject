<?php
$html = '<html>
        <head>
            <title>Certificate</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="../../html2pdf/certificate/certificate.css" type="text/css" />
        </head>
        <body>
            <div style="height:10px;"></div>
            <img src="../../html2pdf/certificate/lol4.png"></img>
            <page size="A4"></page>
            <div style="margin-top:-29.7cm;">
            <div class="course_text_container">
                <div class="' . (strlen($title) > 32 ? "course_text_small" : "course_text_large") . '">' . $title . ' ' . TranslationHandler::get_static_text("CERTIFICATE") . '</div>
            </div>
            
            <div class="name_text_container">
                <div class="' . (strlen($name) > 16 ? "name_text_small" : "name_text_large") . '">'. format_first_last_name($name, "", 30) .'</div>
            </div>
            
            <div class="info_text_container">
                <div class="info_text">'. TranslationHandler::get_static_text("HAS_COMPLETED_THE_COURSE_FOR") . ' ' . $title . '.</div>
            </div>
            
            <div class="certificate_id_text_container" ' . (strlen($name) > 16 ? 'style="padding-top:70px !important;"' : '') . '>
                <div class="certificate_id_text">'.  TranslationHandler::get_static_text("CERTIFICATE") .' ID:</div>
            </div>
            
            <div class="certificate_id_container">
                <div class="certificate_id">'. $code . '</div>
            </div>
            
            <div style="width:100%;margin-top:-30px;">
                <div class="date_awarded_text"><div style="padding-top:15px;">'. TranslationHandler::get_static_text("CERTIFICATE_ASSIGNED_DATE") .':</div><div style="margin-top:5px;">26-07-2015</div></div>
                <div class="nazer_text"><img src="../../html2pdf/certificate/underskrift.png" style="width:200px;height:70px;" /><br />Nazer Mehrali<br /> '. TranslationHandler::get_static_text("CEO") .' LearnEZ ApS</div>
                <div style="clear:both;"></div>
            </div>
            
            <table style="width:100%;margin-top:75px;">
            <tr>
            <td style="width:30%;font-family: calibri;font-size:16px;text-align:left;padding-left:30px;">www.learnez.dk</td>
            <td style="width:70%;font-family: calibri;font-size:16px;text-align:right;paddin-right:60px;">'. TranslationHandler::get_static_text("IDENTIFY_CERTIFICATE") .'</td>
            </tr>
            </table>
            
            </div>
        </body>
    </html>';
?>