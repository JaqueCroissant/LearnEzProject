<link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="css/css.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="js/jQuery.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="js/cookie.js" type="text/javascript"></script>
<script src="js/scripts.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<script src="js/navigation.js" type="text/javascript"></script>
<script src="js/form.js" type="text/javascript"></script>
<script src="js/notifications.js" type="text/javascript"></script>

<?php        
if (SessionKeyHandler::session_exists("user")) {
?>
<script src="js/backgroundScripts.js" type="text/javascript"></script>
<script src="js/userGlobal.js" type="text/javascript"></script>
<?php
}
?>