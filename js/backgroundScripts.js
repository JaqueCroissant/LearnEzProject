function start_notification_beat(timerInSeconds){
    setInterval(function(){
        notification_beat();
    }, timerInSeconds * 1000);
}

function notification_beat(){
    $.ajax({
       type: "POST",
       url: "ajax/notifications.php",
       dataType: "json",
       data: {action: 'get_new_notifications'},
       success: function (result) {
            if (result !== null) {
                if (result.id !== 0) {
                    $('#notification_counter').html(result);
                }
                else {
                     $('#notification_counter').html("");
                }
            }
       }
    });
}