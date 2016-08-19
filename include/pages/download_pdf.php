<?php
session_start();
if (!isset($_GET["file"])) {
    die();
}
$file = $_GET["file"];
$path = realpath(__DIR__ . '/../..') . "/html2pdf/tmp/";
if (!isset($_SESSION["user"]) || !file_exists($path . $file)) {
    die();
}

header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Certificate.pdf"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($path . $file));
readfile($path . $file);
unlink($path . $file);