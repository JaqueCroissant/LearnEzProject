<html>
    <script src="Jquery.js" type="text/javascript"></script>
    <style type="text/css">
        #courseFrame{
            width:60%;
        }
        
    </style>
        <iframe id="courseFrame" src="course.html"></iframe>
<script type="text/javascript">
    //This is just for future reference, not to be copied directly
    //Use the API names and calls for the required functionality, rest can be changed to use JQuery
    
    //resize the iframe to 0.757 * width, which i found to be the most precise aspect ratio
    //can be changed later if other ratio is better
    function resize() {
        var width = document.getElementById("courseFrame").offsetWidth;
        document.getElementById("courseFrame").height = (width * 0.757);
    }
    
    //add event listener on resizing the window, to make the iframe responsive
    window.addEventListener("resize", resize);
    resize();
    
    
    
    var d = document.getElementById("courseFrame");
    var w = d.contentWindow;   
    
    //Add event listener to go to run after module is loaded
    w.addEventListener("moduleReadyEvent", function(){
        //Changes shown slide to a specific slide
        //change value to wanted slide
        w.cpAPIInterface.setVariableValue("cpCmndGotoSlide", 0);
        //Hides player bar below the slides, to disallow changing slides
        jQuery(function(){
            $("#courseFrame").contents().find(".playbarSlider").attr("style", "display:none !important;");
        });
        //Runs after slide is changed
        //used to save the progress for later
        w.cpAPIEventEmitter.addEventListener("CPAPI_SLIDEENTER", function(){
            console.log(w.cpAPIInterface.getCurrentSlideIndex());
        });
    });
    
    
</script>
<p id="test"></p>
</html>


