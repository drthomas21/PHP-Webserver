<?php
namespace App\Signz\Record;
class Gang extends Point {
	var $title;
	var $radius;
	var $description;
	var $coords;
	
	public function __construct(\stdClass $obj = null) {
		$this->type = "gangs";
		if($obj != null) {
			foreach(array_keys(get_class_vars(__CLASS__)) as $prop) {
				if(property_exists($obj,$prop))
					$this->$prop = $obj->$prop;
			}
		}
	}
}