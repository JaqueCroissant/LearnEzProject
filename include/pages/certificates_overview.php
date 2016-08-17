<?php
    require_once 'require.php';
    require_once '../../include/handler/pageHandler.php';
    
$certificateHandler = new certificatesHandler();
$paginationHandler = new PaginationHandler();

$current_page_number = isset($_GET['p']) && !empty($_GET['p']) ? $_GET['p'] : 1;

if ($certificateHandler->get_from_user()) {

$certificates = $paginationHandler->run_pagination($certificateHandler->certificates, $current_page_number, SettingsHandler::get_settings()->elements_shown);

foreach ($certificates as $certificate) {
    echo "<pre>";
    var_dump($certificate);
    echo "</pre>";
}
?>

<?php
}
?>