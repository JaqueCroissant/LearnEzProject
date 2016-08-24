<?php
require_once 'require.php';
DbHandler::get_instance()->query("DELETE FROM user_mail WHERE sender_folder_id = '7' AND receiver_folder_id = '7'");
DbHandler::get_instance()->query("DELETE m FROM user_mail m LEFT JOIN users u ON u.id = m.receiver_id WHERE u.id IS NULL AND m.sender_folder_id = '7'");
DbHandler::get_instance()->query("DELETE m FROM user_mail m LEFT JOIN users u ON u.id = m.sender_id WHERE u.id IS NULL AND m.receiver_folder_id = '7'");
DbHandler::get_instance()->query("DELETE m FROM mail m LEFT JOIN user_mail u ON u.mail_id = m.id WHERE u.id IS NULL");