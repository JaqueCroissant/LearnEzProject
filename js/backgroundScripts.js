function start_notification_beat(timerInSeconds){
    setInterval(function(){
        notification_beat();
    }, timerInSeconds * 1000);
}

function notification_beat(){
    $.ajax({
       type: "POST",
       url: "include/ajax/notifications.php",
       dataType: "json",
       data: {action: 'get_new_notifications'},
       success: function (result) {
            if (result.status_value) {
                $('#notification_counter').removeClass("hidden");
                $('#notification_counter').html(result.count > 9 ? "!" : result.count);                
            }
            else {
                $('#notification_counter').html("");
            }
       }
    });
}