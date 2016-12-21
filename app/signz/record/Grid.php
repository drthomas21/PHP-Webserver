<?php
namespace App\Signz\Record;
class Grid {
	var $id;
	var $latitude;
	var $longitude;
	
	public function __construct(\stdClass $obj = null) {
		if($obj != null) {
			foreach(array_keys(get_class_vars(__CLASS__)) as $prop) {
				if(property_exists($obj,$prop))
					$this->$prop = $obj->$prop;
			}
		}		
	}
}