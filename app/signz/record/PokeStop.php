<?php
namespace App\Signz\Record;
class PokeStop {
	var $name;

	public function __construct(\stdClass $obj = null) {
		$this->type = "pokestop";
		if($obj != null) {
			foreach(array_keys(get_class_vars(__CLASS__)) as $prop) {
				if(property_exists($obj,$prop))
					$this->$prop = $obj->$prop;
			}
		}
	}
}