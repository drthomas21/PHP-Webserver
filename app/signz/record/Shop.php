<?php
namespace App\Signz\Record;
class Shop {
	var $name;
	var $description;

	public function __construct(\stdClass $obj = null) {
		$this->type = "shop";
		if($obj != null) {
			foreach(array_keys(get_class_vars(__CLASS__)) as $prop) {
				if(property_exists($obj,$prop))
					$this->$prop = $obj->$prop;
			}
		}
	}
}