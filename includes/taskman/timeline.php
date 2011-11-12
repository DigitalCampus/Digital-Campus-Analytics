
 <script type="text/javascript">
        var tl;
        SimileAjax.History.enabled = false;
        function onLoad() {
          var eventSource = new Timeline.DefaultEventSource();
          var bandInfos = [
            Timeline.createBandInfo({
                eventSource:    eventSource,
                date:           "<?php echo date('d M Y')." UTC";  ?>",
                width:          "80%", 
                intervalUnit:   Timeline.DateTime.DAY, 
                intervalPixels: 300
            }),
            Timeline.createBandInfo({
                showEventText:  false,
                /*eventSource:    eventSource,*/
                date:           "<?php echo date('d M Y')." UTC";  ?>",
                width:          "20%", 
                intervalUnit:   Timeline.DateTime.MONTH, 
                intervalPixels: 600
            })
          ];
          bandInfos[1].syncWith = 0;
          bandInfos[1].highlight = true;
          tl = Timeline.create(document.getElementById("task_timeline"), bandInfos);
          Timeline.loadXML("<?php echo $CONFIG->homeAddress."data/simile-xml.php?duein=".$duein; ?>", function(xml, url) { eventSource.loadXML(xml, url); });
        }
        
        var resizeTimerID = null;
        function onResize() {
            if (resizeTimerID == null) {
                resizeTimerID = window.setTimeout(function() {
                    resizeTimerID = null;
                    tl.layout();
                }, 500);
            }
        }
    </script>

<div id="task_timeline" style="height:500px; border: 1px solid #aaa"></div>