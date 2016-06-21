jQuery(function ($) {
   $(document).ready(function () {
       $('#navBar').mouseenter(function () {
           $('#navBar').removeClass("collapsed");
           $('.menu_text').removeClass("collapsedTitle");
           $('.menu_text').addClass("visible");
       }); 
       
       $('#navBar').mouseleave(function () {
           $('#navBar').addClass("collapsed");
           $('.menu_text').removeClass("visible");
           $('.menu_text').addClass("collapsedTitle");
       });
    });
 });