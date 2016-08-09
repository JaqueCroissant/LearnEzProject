<!DOCTYPE html>
<html lang="en">
<head>
<meta name='viewport' content='initial-scale = 1, minimum-scale = 1, maximum-scale = 1'/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="x-ua-compatible" content="IE=10">
<link href="../../core/css/course_css.css" rel="stylesheet" type="text/css"/>
<title></title>

<style>#initialLoading{background:url(assets/htmlimages/loader.gif) no-repeat center center;background-color:#ffffff;position:absolute;margin:auto;top:0;left:0;right:0;bottom:0;z-index:10010;}</style>

<script>

var deviceReady = false;
var initCalled = false ;
var initialized = false;

function onBodyLoad()
{
    if(typeof window.device === 'undefined')
    {
        document.addEventListener("deviceready", onDeviceReady, false);
    }
    else
    {
        onDeviceReady();
    }
}

function onDeviceReady()
{
    deviceReady = true ;
    if(initCalled === true) {initializeCP();}
}

function initializeCP()
{
    if(initialized) {return;}
    initCalled = true;
    if(cp.pg && deviceReady === false) {return;}

    function cpInit()
    {
        document.getElementById("course").innerHTML = " <div class='cpMainContainer' id='cpDocument' style='left: 0px; top:0px;' >	<div id='main_container' style='top:0px;position:absolute;'>	<div id='projectBorder' style='top:0px;left:0px;position:absolute;display:block'></div>	<div class='shadow' id='project_container' style='left: 0px; top:0px;position:absolute;' >	<div id='project' class='cp-movie' style='width:1024px ;height:740px '>		<div id='project_main' class='cp-timeline cp-main'>			<div id='div_Slide' onclick='cp.handleClick(event)' style='top:0px; width:1024px ;height:740px ;position:absolute;-webkit-tap-highlight-color: rgba(0,0,0,0);'></div>			<canvas id='slide_transition_canvas'></canvas>		</div>		<div id='autoplayDiv' style='display:block;text-align:center;position:absolute;left:0px;top:0px;'>			<img id='autoplayImage' src='' style='position:absolute;display:block;vertical-align:middle;'/>			<div id='playImage' tabindex='9999' role='button' aria-label='play' onkeydown='cp.CPPlayButtonHandle(event)' onClick='cp.movie.play()' style='position:absolute;display:block;vertical-align:middle;'></div>		</div>	</div>	<div id='toc' style='left:0px; float:left;position:absolute;-webkit-tap-highlight-color: rgba(0,0,0,0);'>	</div>	<div id='playbar' style='left:0px; float:left;position:absolute'>	</div>	<div id='cc' style='left:0px; float:left;position:absolute;visibility:hidden;pointer-events:none;' onclick='cp.handleCCClick(event)'>		<div id='ccText' style='left:0px;float:left;position:absolute;width:100%;height:100%;'>		<p style='margin-left:8px;margin-right:8px;margin-top:2px;'>		</p>		</div>		<div id='ccClose' style='background-image:url(./assets/htmlimages/ccClose.png);right:0px; float:right;position:absolute;cursor:pointer;width:13px;height:11px;' onclick='cp.showHideCC()'>		</div>	</div>	<div id='gestureIcon' class='gestureIcon'>	</div>	<div id='gestureHint' class='gestureHintDiv'>		<div id='gImage' class='gesturesHint'></div>	</div>	<div id='pwdv' style='display:block;text-align:center;position:absolute;width:100%;height:100%;left:0px;top:0px'></div>	<div id='exdv' style='display:block;text-align:center;position:absolute;width:100%;height:100%;left:0px;top:0px'></div>	</div>	</div></div><div id='blockUserInteraction' class='blocker' style='width:100%;height:100%;'>	<table style='width:100%;height:100%;text-align:center;vertical-align:middle' id='loading' class='loadingBackground'>		<tr style='width:100%;height:100%;text-align:center;vertical-align:middle'>			<td style='width:100%;height:100%;text-align:center;vertical-align:middle'>				<image id='preloaderImage'></image>				<div id='loadingString' class='loadingString'>Loading...</div>			</td>		</tr>	</table></div> <div id='initialLoading'></div>";
        cp.DoCPInit();
        var lCpExit = window["DoCPExit"];
        window["DoCPExit"] = function()
        {
            if(cp.UnloadActivties)
                cp.UnloadActivties();
            lCpExit();
        };
    }

    cpInit();
    initialized = true;
}

(function()
    {
        if(document.documentMode < 9)
        {
            document.getElementById("course").innerHTML = "";
            document.write("The content you are trying to view is not supported in the current Document Mode of Internet Explorer. Change the Document Mode to Internet Explorer 9 Standards and try to view the content again.<br>To change the Document Mode, press F12, click Document Mode: <current mode>, and then select Internet Explorer 9 Standards.");
            return;
        }
        window.addEventListener("load",function() 
        {
            setTimeout(function() 
            {					
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = 'assets/js/CPXHRLoader.js';
                script.defer = 'defer';
                script.onload = function()
                {
                    var lCSSLoaded = false;
                    var lJSLoaded = false;
                    function constructDIVs()
                    {
                        if(lCSSLoaded && lJSLoaded)
                        {
                            initializeCP();
                        }
                    }
                    cpXHRJSLoader.css('assets/css/CPLibraryAll.css',function() {
                        lCSSLoaded = true;
                        constructDIVs();
                    });
                    var lJSFiles = [  '../../core/js/jquery-1.6.1.min.js','assets/js/CPM.js','../../core/playbar/playbarScript.js' ];
                    cpXHRJSLoader.js(lJSFiles,function()
                    {
                        lJSLoaded = true;
                        constructDIVs();
                    });
                }
                document.getElementsByTagName('head')[0].appendChild(script);
            },1);
        },false);
    })();

</script>

 </head>
 <body>
<div id="course" style="width:1024px;" onload="onBodyLoad()">
    <div id='initialLoading'></div>
    <noscript style="text-align:center;font-size:24px;">Enable Javascript support in the browser.</noscript>
</div>
</body>
</html>