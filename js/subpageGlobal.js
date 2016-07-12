jQuery(function ($) {
    $(document).ready(function () {
        relocateCheckboxes();
    });
    
    $(window).resize(function(){
        relocateCheckboxes();
    });
    
    function relocateCheckboxes(){
        $('.checkbox-resize').each(function(index, value){
            relocateCheckbox(value);
        });
    }

    function relocateCheckbox(value){
        var size = $(value).closest('.mail-item').height();
        $(value).css("margin-top", size / 2 - 10);
    } 
});