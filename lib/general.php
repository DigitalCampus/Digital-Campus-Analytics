<?php

function displayUserRiskFactor($weight){
	if($weight >= 4){
		echo '<div class="risk highrisk">'.$weight.'</div>';
	} else if ($weight >= 1){
		echo '<div class="risk mediumrisk">'.$weight.'</div>';
	} else {
		echo '<div class="risk lowrisk">'.$weight.'</div>';
	}
}


class XMLSerializer {

	// functions adopted from http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/

	public static function generateValidXmlFromObj(stdClass $obj, $node_block='nodes', $node_name='node') {
		$arr = get_object_vars($obj);
		return self::generateValidXmlFromArray($arr, $node_block, $node_name);
	}

	public static function generateValidXmlFromArray($array, $node_block='nodes', $node_name='node') {
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';

		$xml .= '<' . strtolower($node_block) . '>';
		$xml .= self::generateXmlFromArray($array, $node_name);
		$xml .= '</' . strtolower($node_block) . '>';

		return $xml;
	}

	private static function generateXmlFromArray($array, $node_name) {
		$xml = '';

		if (is_array($array) || is_object($array)) {
			foreach ($array as $key=>$value) {
				if (is_numeric($key)) {
					$key = $node_name;
				}

				$xml .= '<' . strtolower($key) . '>' . self::generateXmlFromArray($value, $node_name) . '</' . strtolower($key) . '>';
			}
		} else {
			$xml = htmlspecialchars($array, ENT_QUOTES);
		}

		return $xml;
	}

}



