jQuery(function ($) {
    $(document).ready(function () {
        var currently_recieving_notifications = false;
        var no_more_notifications = false;
        var currently_clicked_button = false;
        
        //init window
        var win = $('#notifications');
        win.attr("aria-expanded", "false");
        win.attr("data-toggle", "dropdown");
        $(win.closest("li")).append("<div class='dropdown-menu animated flipInY' style='width:auto;'>" +
                "<div id='notification_window' class='hidden-xs'>" + 
                "<div id='notification_top' style='width:400px;'>" +
                "<div class='pull-left' style='width:200px;'><h4 class='notification_title'></h4></div>" +
                "<div class='pull-right' style='width:65px;margin-top:8px;'>" +
                "<a href='javascript:void(0)' class='change_page notification_load_window' page='notifications' id='notifications' step='all'></a></div></div>" +
                "<div id='notification_data' style='width:400px;'></div>" +    
                "<div id='notification_loading' style='width:400px;height:25px;'>" +   
                "<div id='notification_loading_image'></div></div></div>");
        
        //close on notification click
//        $('#notification_data').on("click", ".notification_load_window", function(){
//            $('#notification_window').hide("fast");
//        });
          
        //window open
        $('#notifications').click(function (event) {       
            if ($('#notification_window').is(":hidden")) {
                no_more_notifications = false;
                currently_recieving_notifications = true;
                setTimeout(function(){$('#notification_window').scrollTop(0);},1);
                $('#notification_loading').show();
                $("#notification_loading_image").show();
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'get_notifications'},
                   success: function (result) {
                        $('#notification_data').html(result.notifications);
                        if (result.status_text !== undefined) {
                            no_more_notifications = true;
                            $('#notification_loading').hide();
                            $('#notification_data').append(result.status_text);
                        }
                        $(".notification_load_window").html(result.translations["SEE_ALL"]);
                        $(".notification_title").html(result.translations["NOTIFICATIONS"]);
                        currently_recieving_notifications = false;
                        $("#notification_loading_image").hide();
                   }
                });
                $('#notification_window').show("fast");
                $('#notification_counter').html("");
            } else {
                $('#notification_window').hide("fast");
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
                            show_status_bar("error", result.error);
                        }
                        currently_clicked_button = false;
                   },
                   error: function(result){
                       show_status_bar("error", result.error);
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
                console.log("running");
                $("#notification_loading_image").show();
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'get_more_notifications', offset: $('.notification').length},
                   success: function (result) {
                        
                        $('#notification_data').append(result.notifications);
                        if (result.status_text !== undefined) {
                            no_more_notifications = true;
                            $('#notification_loading').hide();
                            $('#notification_data').append(result.status_text);
                        }
                        currently_recieving_notifications = false;
                        $("#notification_loading_image").hide();
                   },
                   error: function(result){
                       currently_recieving_notifications = false;
                       $("#notification_loading_image").hide();
                   }
                });
            }
        }));
    });
});