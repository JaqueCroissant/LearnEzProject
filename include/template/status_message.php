<?php

$text = isset($text) && !empty($text) ? $text : null;

switch($display_type) {
    default:
        echo "<div> " . $text . "</div>";
        break;
    
    case 2:
        echo "<div> " . $text . "</div>";
        break;
    
    case 3:
        echo "<div> " . $text . "</div>";
        break;
}
