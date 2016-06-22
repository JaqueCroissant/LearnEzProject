jQuery(function ($) {
   $(document).ready(function () {
       $('#navBar').mouseover(function () {
           $('#navBar').removeClass("collapsed");
           $('.menu_text').switchClass("collapsedTitle", "visible");
       }); 
       
       $('#navBar').mouseleave(function () {
           $('#navBar .menu_text').removeClass("visible");
           $('#navBar .menu_text').addClass("collapsedTitle");
           $('#navBar').addClass("collapsed");
       });
    });
 });