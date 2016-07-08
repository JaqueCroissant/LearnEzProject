<?php
require_once '../../include/ajax/require.php';
echo json_encode(TranslationHandler::get_static_texts());