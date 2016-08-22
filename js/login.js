$(document).on("click", ".display_login_overlay", function (event) {
    $(".login_overlay").fadeIn(500, function() {
        
    });
});

$(document).on("click", ".hide_login_overlay", function (event) {
    $(".login_overlay").fadeOut(500, function() {
        
    });
});