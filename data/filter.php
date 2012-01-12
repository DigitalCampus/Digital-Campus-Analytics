<?php 

interface FilterInterface {
	// interface class for filter
	public function get($opts=array());
}

// 
class AllPatientsFilter implements FilterInterface {
	
	public function get($opts=array()){
		$sql = "";
	}
}

// filter to get only the current patients
class CurrentPatientsFilter implements FilterInterface {
	
	public function get($opts=array()){
		
	}
}

class HealthPointPatientsFilter implements FilterInterface {

	public function get($opts=array()){

	}
}


?>