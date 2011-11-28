<?php 

class RiskAssessment {
	function getRisks($p){
		$risk = new stdClass;
		$risk->risks = array();
		$risk->count = 0;
		/*
		 * Age
		 */ 
		$risk->risks['Q_AGE_UNDER18'] = false;
		// from Registration 
		if(isset($p->Q_AGE) && $p->Q_AGE < 18 ){
			$risk->risks['Q_AGE_UNDER18'] = true;
		} 
		// from ANC first
		if(isset($p->ancfirst->Q_AGE) && $p->ancfirst->Q_AGE < 18){
			$risk->risks['Q_AGE_UNDER18'] = true;
		}
		//from ANC Follow
		foreach($p->ancfollow as $x){
			if(isset($x->Q_AGE) && $x->Q_AGE < 18){
				$risk->risks['Q_AGE_UNDER18'] = true;
			}
		}
		//from ANC Transfer
		foreach($p->anctransfer as $x){
			if(isset($x->Q_AGE) && $x->Q_AGE < 18){
				$risk->risks['Q_AGE_UNDER18'] = true;
			}
		}
		
		$risk->risks['Q_AGE_OVER34'] = false;
		// from Registration
		if(isset($p->Q_AGE) && $p->Q_AGE > 34){
			$risk->risks['Q_AGE_OVER34'] = true;
		}
		// from ANC first
		if(isset($p->ancfirst->Q_AGE) && $p->ancfirst->Q_AGE > 34){
			$risk->risks['Q_AGE_OVER34'] = true;
		}
		//from ANC Follow
		foreach($p->ancfollow as $x){
			if(isset($x->Q_AGE) && $x->Q_AGE > 34){
				$risk->risks['Q_AGE_OVER34'] = true;
			}
		}
		//from ANC Transfer
		foreach($p->anctransfer as $x){
			if(isset($x->Q_AGE) && $x->Q_AGE > 34){
				$risk->risks['Q_AGE_OVER34'] = true;
			}
		}
		if($risk->risks['Q_AGE_OVER34'] == true || $risk->risks['Q_AGE_UNDER18'] == true){
			$risk->count++;
		}
		
		/*
		 * Birth Interval
		 */
		$risk->risks['Q_BIRTHINTERVAL'] = false;
		// from ANC First
		if(isset($p->ancfirst->Q_BIRTHINTERVAL) && ($p->ancfirst->Q_BIRTHINTERVAL == 'within1' || $p->ancfirst->Q_BIRTHINTERVAL == 'within2')){
			$risk->risks['Q_BIRTHINTERVAL'] = true;
		}
		// from Transfer
		foreach($p->anctransfer as $x){
			if(isset($x->Q_BIRTHINTERVAL) && ($x->Q_BIRTHINTERVAL == 'within1' || $x->Q_BIRTHINTERVAL == 'within2')){
				$risk->risks['Q_BIRTHINTERVAL'] = true;
			}
		}
		if($risk->risks['Q_BIRTHINTERVAL'] == true){
			$risk->count++;
		}
		
		/*
		 * Birth order/gravida
		 */
		$risk->risks['Q_GRAVIDA'] = false;
		// from ANC First
		if(isset($p->ancfirst->Q_GRAVIDA) && ($p->ancfirst->Q_GRAVIDA > 6)){
			$risk->risks['Q_GRAVIDA'] = true;
		}
		// from Transfer
		foreach($p->anctransfer as $x){
			if(isset($x->Q_GRAVIDA) && ($x->Q_GRAVIDA > 6)){
				$risk->risks['Q_GRAVIDA'] = true;
			}
		}
		if($risk->risks['Q_GRAVIDA'] == true){
			$risk->count++;
		}
		
		/*
		 * eclampsia, bleeding, fatigue
		 */
		$otherrisks = array ('Q_PREECLAMPSIA', 'Q_BLEEDING', 'Q_FATIGUE');
		foreach ($otherrisks AS $r){
			$risk->risks[$r] = false;
			// from ANC First
			if(isset($p->ancfirst->{$r}) && ($p->ancfirst->{$r} == 'yes')){
				$risk->risks[$r] = true;
			}
			// from Transfer
			foreach($p->anctransfer as $x){
				if(isset($x->{$r}) && ($x->{$r} == 'yes')){
					$risk->risks[$r] = true;
				}
			}
			if($risk->risks[$r] == true){
				$risk->count++;
			}
		}
		
		//abdominal pain
		

		
		// work out the risk category
		$risk->category = 'none';
		
		// unavoidable risk
		//if no other risks and first order birth
		if($risk->count == 0){
			// from ANC First
			if(isset($p->ancfirst->Q_GRAVIDA) && ($p->ancfirst->Q_GRAVIDA == 1)){
				$risk->category = 'unavoidable';
			}
			// from Transfer
			foreach($p->anctransfer as $x){
				if(isset($x->Q_GRAVIDA) && ($x->Q_GRAVIDA == 1)){
					$risk->category = 'unavoidable';
				}
			}	
		}
		
		// single
		if ($risk->count == 1){
			$risk->category = 'single';
		}
		// multiple
		if ($risk->count > 1){
			$risk->category = 'multiple';
		}
		
		return $risk;
	}
}	
?>