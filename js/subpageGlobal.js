jQuery(function ($) {
    $(document).ready(function () {
        relocateCheckboxes();
    });
    
    $(window).resize(function(){
        relocateCheckboxes();
    });
    
    function relocateCheckboxes(){
        $('.mail-item').each(function(index, value){
            relocateCheckbox(value);
        });
    }

    function relocateCheckbox(value){
        var size = $(value).height();
        console.log(size);
        $($(value).children('.checkbox-resize')).css("margin-top", size / 2 - 10);
    }
    
    
});

