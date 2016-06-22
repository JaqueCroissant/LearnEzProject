jQuery(function ($) {
    $(document).ready(function () {
        $('#notificationWindow').hide();
        $("html").click(function () {
            $("#notificationWindow").hide("fast");
        });
        $('#navBar').hover(function () {
            $('#navBar').removeClass("collapsed");
            $('#navBar').addClass("expanded");
            $('.menu_text').switchClass("collapsedTitle", "visible");
        }, function () {
            $('#navBar .menu_text').removeClass("visible");
            $('#navBar .menu_text').addClass("collapsedTitle");
            $('#navBar').removeClass("expanded");
            $('#navBar').addClass("collapsed");
        }); 

        var top = $('.topbar').height();
        $('.sidebar').css("top", top);
        $('.content').css("padding-top", top);
        $('#notificationWindow').css("top", top + 5);
        $('#notificationWindow').css("right", 40);


        $('#notificationLink').click(function (event) {
            event.stopPropagation();
            if ($('#notificationWindow').is(":hidden")) {
                $.ajax({
                   type: "POST",
                   url: "pages/notifications.php",
                   data: {},
                   success: function (result) {
                       $('#notificationWindow').html(result);
                   }, 
                   failure: function (r) {
                       alert(r);
                   }
                });
                $('#notificationWindow').show("fast");
            } else {
                $('#notificationWindow').hide("fast");
            }
        });
    });
 });