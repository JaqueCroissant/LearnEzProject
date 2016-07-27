<html>
    <script src="Jquery.js" type="text/javascript"></script>
    <style type="text/css">
        #courseFrame{
            width:60%;
        }
        
    </style>
        <iframe id="courseFrame" src="course.html"></iframe>
<script type="text/javascript">  
    //event on module ready
    window.addEventListener("moduleReadyEvent", function(){});
    
    //most must be done after module is ready
    //get slide count
    window.cpAPIInterface.getVariableValue("rdinfoSlideCount")
    //go to slide
    window.cpAPIInterface.setVariableValue("cpCmndGotoSlide", 0);
    //event on silde enter
    window.cpAPIEventEmitter.addEventListener("CPAPI_SLIDEENTER", function(){});
    //get current slide index
    window.cpAPIInterface.getCurrentSlideIndex());
    
    
</script>
<p id="test"></p>
</html>


