jQuery(function ($) {
    $(document).ready(function () {
        $('#notificationWindow').hide();
        $('#navBar').addClass("collapsed");
        $('#navBar .menu_text').hide();
        
        function HideSidebarOnTablet () {
            $('.sidebar').addClass("hidden-sm hidden-xs collapsed");
        }
        
        $("html").click(function () {
            if (!$("#notificationWindow").is(":hidden")){
                $("#notificationWindow").hide("fast");
                $(".notificationUnseen").switchClass("notificationUnseen", "notificationSeen");
            }
            HideSidebarOnTablet();
        });
        
        // Sidebar expand funktion
        var navBarTransitionDur = $('#navBar').css("transition-duration");
            navBarTransitionDur = 1000 * parseFloat(navBarTransitionDur);

        var menuTextTimeout;
        function showMenuText() {
            menuTextTimeout = setTimeout(function () {
                $('#navBar .menu_text').show();
            }, navBarTransitionDur);
        }
        
        $('#navBar').hover(function () {
            $('#navBar').switchClass("collapsed", "expanded");
            showMenuText();
        }, function () {
            clearTimeout(menuTextTimeout);
            $('#navBar .menu_text').hide();
            $('#navBar').switchClass("expanded", "collapsed");
            HideSidebarOnTablet();
        });

        var top = $('.topbar').height();
        var left = $('.collapsed').width();
        $('.sidebar').css("top", top);
        $('.content').css("padding-top", top + 10);
        $('.content').css("padding-left", left + 10);
        $('#notificationWindow').css("top", top + 5);
        $('#notificationWindow').css("right", 40);


        $('#notifications').click(function (event) {
            event.stopPropagation();
            if ($('#notificationWindow').is(":hidden")) {
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   data: {action: 'get_notifications'},
                   success: function (result) {
                       $('#notificationWindow').html(result);
                   }
                });
                $('#notificationWindow').show("fast");
                $('#notification_counter').html("");
            } else {
                $('#notificationWindow').hide("fast");
            }
        });
        
        $('#sidebarButton').click(function (e) {
            e.stopPropagation();
            $('.sidebar').removeClass("hidden-sm hidden-xs collapsed");
        });
    });
 });