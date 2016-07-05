jQuery(function ($) {
    $(document).ready(function () {
        
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
        
        $('#sidebarButton').click(function (e) {
            e.stopPropagation();
            $('.sidebar').removeClass("hidden-sm hidden-xs collapsed");
        });
    });
 });