<?php
require_once 'require.php';

if (!isset($_GET["file"])) {
    die();
}
$file = $_GET["file"];
$path = realpath(__DIR__ . '/../..') . "/html2pdf/tmp/";
if (!SessionKeyHandler::session_exists("user") || !file_exists($path . $file)) {
    die();
}

header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'. $path . basename($file).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($path . $file));
readfile($path . $file);
unlink($path . $file);