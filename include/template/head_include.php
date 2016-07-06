<!--<link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>-->
<!--<link href="css/css.css" rel="stylesheet" type="text/css"/>-->

<script src="../libs/bower/jquery/dist/jquery.js"></script>
<script src="../libs/bower/jquery-ui/jquery-ui.min.js"></script>
<script src="js/cookie.js" type="text/javascript"></script>
<script src="js/scripts.js" type="text/javascript"></script>
<script src="js/global.js" type="text/javascript"></script>
<script src="js/navigation.js" type="text/javascript"></script>
<script src="js/form.js" type="text/javascript"></script>
<script src="js/notifications.js" type="text/javascript"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
<link href="../assets/images/logo.png" sizes="196x196" rel="shortcut icon">
<link href="../libs/bower/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="../libs/bower/material-design-iconic-font/dist/css/material-design-iconic-font.css" rel="stylesheet">
<link href="../libs/bower/animate.css/animate.min.css" rel="stylesheet">
<link href="../libs/bower/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
<link href="../libs/bower/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
<link href="../assets/css/bootstrap.css" rel="stylesheet">
<link href="../assets/css/app.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../libs/misc/jvectormap/jquery-jvectormap.css">
<link rel="stylesheet" type="text/css" href="../libs/bower/switchery/dist/switchery.min.css">
<link rel="stylesheet" type="text/css" href="../libs/bower/lightbox2/dist/css/lightbox.min.css">

<?php        
if (SessionKeyHandler::session_exists("user")) {
?>
<script src="js/backgroundScripts.js" type="text/javascript"></script>
<script src="js/userGlobal.js" type="text/javascript"></script>
<?php
}
?>