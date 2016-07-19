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
                "<div id='notification_window' class='hidden-sm hidden-xs'>" + 
                "<div id='notification_top' class='col-md-12'>" +
                "<div class='col-md-6 pull-left'><h4>Notifikationer</h4></div>" +
                "<div class='col-md-3 pull-right' style='margin-top:8px;'>" +
                "<a href='javascript:void(0)' class='change_page notification_load_window' page='notifications' id='notifications' step='all'>Se alle</a></div></div>" +
                "<div id='notification_data' class='col-md-12'></div>" +    
                "<div id='notification_loading' class='col-md-12'>" +   
                "<div id='notification_loading_image'></div></div></div>");
        
        //close on notification click
//        $('#notification_data').on("click", ".notification_load_window", function(){
//            $('#notification_window').hide("fast");
//        });
//        
        //window open
        $('#notifications').click(function (event) {          
            if ($('#notification_window').is(":hidden")) {
                $('#notification_loading').show();
                $("#notification_loading_image").show();
                currently_recieving_notifications = true;
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
        $("#notification_data").on("click", ".notification .notification_button", function(event){
            alert("clicked");
//            if (!currently_clicked_button) {
//                currently_clicked_button = true;
//                $.ajax({
//                   type: "POST",
//                   url: "include/ajax/notifications.php",
//                   dataType: "json",
//                   data: {action: 'delete_notification', notif_id: $(this).attr("notif")},
//                   success: function (result) {
//                        if (result.status_value === true) {
//                            $(event.target).closest(".notification").remove();
//                        }
//                        else {
//                            console.log("status = false");
//                        }
//                        currently_clicked_button = false;
//                   },
//                   error: function(){
//                       console.log("error");
//                       currently_clicked_button = false;
//                   }
//                });
//            }
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