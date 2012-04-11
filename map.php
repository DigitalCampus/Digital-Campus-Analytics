<?php
include_once "config.php";
$PAGE = "map";
$BODY_ATT = 'onload="initialize()" onunload="GUnload()"';
$HEADER = '<script src="http://maps.googleapis.com/maps/api/js?sensor=false"
            type="text/javascript"></script>';
include_once "includes/header.php";

$days = optional_param("days",14,PARAM_INT);

$opts = array('days'=>$days,'limit'=>500);
$submitted = $API->getProtocolsSubmitted($opts);
$healthposts = $API->getHealthPoints();
$dayopts = Array(7,14,31);
?>

<h2>
		<?php 
			echo getstring('map.title');
			
			for($i=0; $i<count($dayopts);$i++){
				if($days == $dayopts[$i]){
					printf(" <span style='color:green'>%s</span> ",getstring("map.nodays",array($dayopts[$i])));
				} else {
					printf(" <a href='?days=%d'>%s</a> ",$dayopts[$i],getstring("map.nodays",array($dayopts[$i])));
				}
				if($i != count($dayopts)-1){
					printf("|");
				}
			}
		?>
</h2>

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
      	var marker<?php echo $hp->hpid; ?> = new google.maps.Marker({
      	      position: new google.maps.LatLng(<?php echo $hp->locationlat.",".$hp->locationlng; ?>),
      	      title:"<?php echo displayHealthPointName($hp->hpcode); ?>",
      	      map: map,
      	      icon: '<?php echo $CONFIG->homeAddress; ?>images/mapicons/hospital-building.png'
      	 });

      	var infowindow<?php echo $hp->hpid; ?> = new google.maps.InfoWindow({
      	    content: "<?php echo displayHealthPointName($hp->hpcode); ?>"
      	});

      	google.maps.event.addListener(marker<?php echo $hp->hpid; ?>, 'click', function() {
      	  infowindow<?php echo $hp->hpid; ?>.open(map,marker<?php echo $hp->hpid; ?>);
      	});
        

  		<?php 
  			    }
  			}
  			
  			// submitted protocols
  			foreach ($submitted->protocols as $s){
  				if ($s->Q_LOCATION == "healthpost" || $s->Q_LOCATION == "healthcentre"  ){
  					$gpslat = $s->locationlat;
  					$gpslng = $s->locationlng;
  				} else {
  					$gpslat = $s->Q_GPSDATA_LAT;
  					$gpslng = $s->Q_GPSDATA_LNG;
  				}
  				if ($gpslat != "" && $gpslng != ""){
	  				switch ($s->protocol){
	  						case getString('protocol.registration'):
	  							?>
				      	var marker = new google.maps.Marker({
				      	      position: new google.maps.LatLng(<?php echo $gpslat.",".$gpslng; ?>),
				      	      title:"<?php echo $s->patientname; ?>",
				      	      map: map,
				      	    icon: '<?php echo $CONFIG->homeAddress; ?>images/mapicons/protocol1.png'
				      	 });
				
				  		<?php 
	  							break;
	  						case getString('protocol.anc'):
	  							?>
				      	var marker = new google.maps.Marker({
				      	      position: new google.maps.LatLng(<?php echo $gpslat.",".$gpslng; ?>),
				      	      title:"<?php echo $s->patientname; ?>",
				      	      map: map,
				      	    icon: '<?php echo $CONFIG->homeAddress; ?>images/mapicons/protocol2.png'
				      	 });
							
				  		<?php 
	  							break;
	  						
  							case getString('protocol.anclabtest'):
  									?>
  								  	var marker = new google.maps.Marker({
  								  		position: new google.maps.LatLng(<?php echo $gpslat.",".$gpslng; ?>),
  								  		title:"<?php echo $s->patientname; ?>",
  								  		map: map,
  								  		icon: '<?php echo $CONFIG->homeAddress; ?>images/mapicons/protocol5.png'
  								  	});
  								  								  														
  						<?php 
  								break;
							default:
								?>
						      	var marker = new google.maps.Marker({
						      	      position: new google.maps.LatLng(<?php echo $gpslat.",".$gpslng; ?>),
						      	      title:"<?php echo $s->patientname; ?>",
						      	      map: map
						      	 });
				
				  		<?php 
	  				}
				}
 			}
  		?>
      }
   
    
    </script>

	<div id="map_canvas" style="float:left; height: 500px; width:700px" class="summarygraph"><?php echo getstring('warning.map.unavailable');?></div>
	<div id="legend" style="float:left, width:100px">
		Legend:<br/>
		<img align="center" src="<?php echo $CONFIG->homeAddress; ?>images/mapicons/hospital-building.png"/> <?php echo getString('map.healthpost');?><br/>
		<img align="center" src="<?php echo $CONFIG->homeAddress; ?>images/mapicons/protocol1.png"/> <?php echo getString('protocol.registration');?><br/>
		<img align="center" src="<?php echo $CONFIG->homeAddress; ?>images/mapicons/protocol2.png"/> <?php echo getString('protocol.ancfirst');?><br/>
		<img align="center" src="<?php echo $CONFIG->homeAddress; ?>images/mapicons/protocol3.png"/> <?php echo getString('protocol.ancfollow');?><br/>
		<img align="center" src="<?php echo $CONFIG->homeAddress; ?>images/mapicons/protocol4.png"/> <?php echo getString('protocol.anctransfer');?><br/>
		<img align="center" src="<?php echo $CONFIG->homeAddress; ?>images/mapicons/protocol5.png"/> <?php echo getString('protocol.anclabtest');?><br/>
	</div>
<?php 
include_once "includes/footer.php";
?>