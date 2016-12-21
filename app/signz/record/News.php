<?php
namespace App\Signz\Record;
abstract class Point {
	var $id;
	var $title;
	var $timestamp;
	var $content;

	public function __construct(\stdClass $obj = null) {
		if($obj != null) {
			foreach(array_keys(get_class_vars(__CLASS__)) as $prop) {
				if(property_exists($obj,$prop))
					$this->$prop = $obj->$prop;
			}
		}
	}
}
