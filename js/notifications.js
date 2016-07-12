jQuery(function ($) {
    $(document).ready(function () {
        var currently_recieving_notifications = false;
        var no_more_notifications = false;
        var currently_clicked_button = false;
        
        $('#notification_data').on("click", ".notification_load_window", function(){
            $('#notification_window').hide("fast");
        });
        
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
                        if (result.status_value === true) {
                            no_more_notifications = true;
                        }
                        $('#notification_data').html(result.notifications);
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
        
        $("#notification_data").on("click", ".notification .notification_button .read_notification", function(event){
            if (!currently_clicked_button) {
                currently_clicked_button = true;
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'delete_notification', notif_id: $(this).attr("notif")},
                   success: function (result) {
                        if (result.status_value === true) {
                            $(event.target).closest(".notification").remove();
                        }
                        else {
                            console.log("status = false");
                        }
                        currently_clicked_button = false;
                   },
                   error: function(){
                       console.log("error");
                       currently_clicked_button = false;
                   }
                });
            }
        });
        
        $('#notification_window').on("scroll", (function() {
            var notif = $("#notification_window");
            if (!no_more_notifications && !currently_recieving_notifications && (notif.scrollTop() + notif.innerHeight() >= notif[0].scrollHeight - 1)) {
                currently_recieving_notifications = true;
                $("#notification_loading_image").show();
                $.ajax({
                   type: "POST",
                   url: "include/ajax/notifications.php",
                   dataType: "json",
                   data: {action: 'get_more_notifications', offset: $('.notification').length},
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
        }));
    });
});