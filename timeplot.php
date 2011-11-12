<?php
include_once "config.php";
$PAGE = "timeplot";
global $HEADER;
$BODY_ATT = 'onload="onLoad();" onresize="onResize();"';
$HEADER = "<script src='/timeplot/api/1.0/timeplot-api.js?local' type='text/javascript'></script>";

echo "page under development";
die;
include_once "includes/header.php";

?>

<script type="text/javascript">
	var timeplot;
	
	function onLoad() {
	  var plotInfo = [
	    Timeplot.createPlotInfo({
	      id: "plot1"
	    })
	  ];
	            
	  timeplot = Timeplot.create(document.getElementById("my-timeplot"), plotInfo);
	}
	
	var resizeTimerID = null;
	function onResize() {
	    if (resizeTimerID == null) {
	        resizeTimerID = window.setTimeout(function() {
	            resizeTimerID = null;
	            timeplot.repaint();
	        }, 10);
	    }
	}

	 var timeGeometry = new Timeplot.DefaultTimeGeometry({
		    gridColor: new Timeplot.Color("#000000"),
		    axisLabelsPlacement: "bottom"
	});

	var valueGeometry = new Timeplot.DefaultValueGeometry({
		gridColor: "#000000"
	});

	var lightGreen = new Timeplot.Color('#5C832F');
	
	function onLoad() {
		  var eventSource = new Timeplot.DefaultEventSource();
		  var plotInfo = [
		    Timeplot.createPlotInfo({
		      id: "plot1",
		      dataSource: new Timeplot.ColumnSource(eventSource,1),
		      timeGeometry: timeGeometry,
		      valueGeometry: valueGeometry,
		      lineColor: "#ff0000",
		      dotColor: lightGreen,
		      showValues: true
		    }),

		    Timeplot.createPlotInfo({
			      id: "plot2",
			      dataSource: new Timeplot.ColumnSource(eventSource,2),
			      timeGeometry: timeGeometry,
			      valueGeometry: valueGeometry,
			      lineColor: "#D0A825",
			      dotColor: lightGreen,
			      showValues: true
			    })
		  ];
		  
		  timeplot = Timeplot.create(document.getElementById("my-timeplot"), plotInfo);
		  timeplot.loadText("data/timeplot.php", ",", eventSource);
		}
</script>



<div id="my-timeplot" style="height:300px;"></div>


<?php 


include_once "includes/footer.php";
?>