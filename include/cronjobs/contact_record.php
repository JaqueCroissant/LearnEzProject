<?php
require_once 'require.php';
DbHandler::get_instance()->query("DELETE FROM contact_record WHERE time < DATE_SUB(NOW(), interval 15 minute)");
?>
