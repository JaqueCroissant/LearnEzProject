<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/certificateHandler.php';

$html = '
<p style="font-family: pacifico;">HELLO CUSTOM</p>
<p style="font-family: alexbrush;">HELLO CUSTOM</p>
<p style="font-family: amaranth;">HELLO CUSTOMffFFf</p>
<p style="font-family: amaranth;font-weight:bold;">HELLO CUSTOMfffFFff</p>
<p style="font-family: amaranth;font-style:italic;">HELLO CUSTOM</p>
<p style="font-family: amaranth;font-weight:bold;font-style:italic;">HELLO CUSTOM</p>';

include("../../html2pdf/mpdf.php");

$mpdf=new mPDF(); 
$mpdf->WriteHTML($html);
$mpdf->Output('lul.pdf', 'D');
?>