jQuery(function ($) {
    $(document).ready(function () {
        var currently_recieving_notifications = false;
        var no_more_notifications = false;
        
        $('#notification_window').hide();
        $('#navBar').addClass("collapsed");
        $('#navBar .menu_text').hide();
        
        function HideSidebarOnTablet () {
            $('.sidebar').addClass("hidden-sm hidden-xs collapsed");
        }
        
        $("html").click(function (e) {
            if (!$("#notification_window").is(":hidden") && !$(e.target).closest("#notification_window").length > 0){
                $("#notification_window").hide("fast");
                $(".notification_unseen").switchClass("notification_unseen", "notification_seen");
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
        $('#notification_window').css("top", top + 5);
        $('#notification_window').css("right", 40);


        $('#notifications').click(function (event) {
            event.stopPropagation();
            if ($('#notification_window').is(":hidden")) {
                currently_recieving_notifications = true;
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'get_notifications'},
                   success: function (result) {
                       $('#notification_data').html(result.notifications);
                       no_more_notifications = false;
                       currently_recieving_notifications = false;
                   }
                });
                $('#notification_window').show("fast");
                $('#notification_counter').html("");
            } else {
                $('#notification_window').hide("fast");
            }
        });
        
        $('#notification_window').scroll(function() {
            var notif = $("#notification_window");
            if (!no_more_notifications && !currently_recieving_notifications && (notif.scrollTop() + notif.innerHeight() >= notif[0].scrollHeight - 1)) {
                currently_recieving_notifications = true;
                $("#notification_loading_image").show();
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'get_more_notifications'},
                   success: function (result) {
                        if (result.status_value === true) {
                            no_more_notifications = true;
                        }
                        $('#notification_data').append(result.notifications);
                        currently_recieving_notifications = false;
                        $("#notification_loading_image").hide();
                   },
                   error: function(result){
                       console.log(result.error);
                       currently_recieving_notifications = false;
                       $("#notification_loading_image").hide();
                   }
                });
            }
        });
        
        $('#sidebarButton').click(function (e) {
            e.stopPropagation();
            $('.sidebar').removeClass("hidden-sm hidden-xs collapsed");
        });
    });
 });