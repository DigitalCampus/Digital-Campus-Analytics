<?php 
$opts = array('days'=>14,'limit'=>500);
$submitted = $API->getProtocolsSubmitted($opts);
$healthposts = $API->getHealthPosts();
?>
<h2>Map of protocols submitted in last 14 days</h2>
 <script type="text/javascript">

 var map;
    function initialize() {

    	var myOptions = {
    			    zoom: 9,
    			    center: new google.maps.LatLng(13.5,39.49),
    			    mapTypeId: google.maps.MapTypeId.HYBRID,
    			  }
    			        
        map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

        loadMarkers();
      
    }

    function loadMarkers(){

      <?php
      		// health posts
			foreach ($healthposts as $hp){
				if ($hp->locationlat != 0 && $hp->locationlng !=0){
      ?>
    	var marker = new google.maps.Marker({
    	      position: new google.maps.LatLng(<?php echo $hp->locationlat.",".$hp->locationlng; ?>),
    	      title:"<?php echo displayHealthPointName($hp->hpcode); ?>",
    	      map: map,
    	      icon: '<?php echo $CONFIG->homeAddress; ?>images/mapicons/hospital-building.png'
    	 });

		<?php 
			    }
			}
			
			// submitted protocols
			foreach ($submitted->protocols as $s){
				if ($s->gpsstamp != "~"){
					$gps = explode(",",$s->gpsstamp);
      ?>
    	var marker = new google.maps.Marker({
    	      position: new google.maps.LatLng(<?php echo $gps[0].",".$gps[1]; ?>),
    	      title:"<?php echo $s->patientname; ?>",
    	      map: map
    	 });

		<?php 
			    }
			}
		?>
    }
   
    
    </script>

	 <div id="map_canvas" style="height: 300px" class="summarygraph"><?php echo getstring('warning.map.unavailable');?></div>