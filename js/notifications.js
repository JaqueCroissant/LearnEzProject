jQuery(function ($) {
    $(document).ready(function () {
        var currently_recieving_notifications = false;
        var no_more_notifications = false;
        var currently_clicked_button = false;
        
        //init window
        var win = $('#notifications');
        win.attr("aria-expanded", "false");
        win.attr("data-toggle", "dropdown");
        $(win.closest("li")).append("<div class='dropdown-menu animated flipInY' style='width:auto;width:400px;'>" +
                "<div id='notification_window' class='hidden-xs' style='width:400px;'>" + 
                "<div id='notification_top' style='width:400px;'>" +
                "<div class='pull-left' style='width:200px;'><h4 class='notification_title'></h4></div>" +
                "<div class='pull-right' style='width:65px;margin-top:8px;'>" +
                "<a href='javascript:void(0)' class='change_page notification_load_window' page='notifications' id='notifications' step='all'></a></div></div>" +
                "<div id='notification_data' style='width:400px;'></div>" +    
                "<div id='notification_loading'>" +   
                "<div id='notification_loading_image' class='center'></div></div></div>");
        
        //close on notification click
        $(document).on("click", "#notification_window", function(event){
            event.stopPropagation();
        });
          
        //window open
        $('#notifications').click(function (event) {       
            if ($('#notification_window').is(":hidden")) {
                no_more_notifications = false;
                currently_recieving_notifications = true;
                setTimeout(function(){$('#notification_window').scrollTop(0);},1);
                $('#notification_data').html("");
                $(".notification_title").html("");
                $(".notification_load_window").html("");
                $('#notification_loading').fadeIn(0);
                $('#notification_window').show("fast");
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'get_notifications'},
                   success: function (result) {
                        $('#notification_loading').fadeOut(0);
                        if (result.status_value) {
                            $('#notification_data').html(result.notifications);
                            if (result.status_text !== undefined) {
                                no_more_notifications = true;
                                $('#notification_data').append(result.status_text);
                            }
                            $(".notification_load_window").html(result.translations["SEE_ALL"]);
                            $(".notification_title").html(result.translations["NOTIFICATIONS"]);
                            currently_recieving_notifications = false;

                            $('#notification_counter').addClass("hidden");
                        }
                        else {
                            show_status_bar("error", result.error, 5000);
                            $('#notification_data').append(result.error);
                        }
                   }
                });
            }
        });
        
        //read on click
        $(document).on("click", ".read_notif", function(event){
           if (!currently_clicked_button) {
                $('#notifications').attr("aria-expanded", false);
                $('#notifications').closest("li").removeClass("open");
                currently_clicked_button = true;
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'read', notifs: [$(this).attr("notif")]},
                   success: function (result) {
                        if (result.status_value === true) {
                            $(event.target).closest(".notification").removeClass("item_unread");
                        }
                        else {
                            show_status_bar("error", result.error, 7000);
                        }
                        currently_clicked_button = false;
                   }
                });
            }
        });
        
        //delete
        $("#notification_data").on("click", ".notification .notification_button .notification_delete", function(event){
            event.stopPropagation();
            if (!currently_clicked_button) {
                currently_clicked_button = true;
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'delete', notifs: [$(this).attr("notif")]},
                   success: function (result) {
                        if (result.status_value === true) {
                            $(event.target).closest(".notification").remove();
                        }
                        else {
                            show_status_bar("error", result.error, 7000);
                        }
                        currently_clicked_button = false;
                   }
                });
            }
        });
        
        //scroll load
        $('#notification_window').on("scroll", (function() {
            var notif = $("#notification_window");
            if (!no_more_notifications && !currently_recieving_notifications && (notif.scrollTop() + notif.innerHeight() >= notif[0].scrollHeight - 10)) {
                currently_recieving_notifications = true;
                $("#notification_loading_image").fadeIn(0);
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'get_more_notifications', offset: $('.notification').length},
                   success: function (result) {
                        $('#notification_loading').fadeOut(0);
                        if (result.status_value) {
                            $('#notification_data').append(result.notifications);
                            if (result.status_text !== undefined) {
                                no_more_notifications = true;
                                $('#notification_data').append(result.status_text);
                            }
                        }
                        else {
                            show_status_bar("error", result.error);
                        }
                        currently_recieving_notifications = false;
                   }
                });
            }
        }));
    });
});