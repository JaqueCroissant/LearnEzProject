function start_notification_beat(timerInSeconds){
    setInterval(function(){
        console.log("running notification beat");
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
            if (result !== null || result.count !== undefined) {
                $('#notification_counter').html(result.count);                
            }
            else {
                $('#notification_counter').html("");
                
            }
       }
    });
}