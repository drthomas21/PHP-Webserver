<?php
namespace App\Signz\Record;
class User {
	var $id;
	var $username;
	var $password;
	var $email;
	var $token;
	var $type;
	var $options;
	
	public function __construct(\stdClass $obj = null) {
		if($obj != null) {
			foreach(array_keys(get_class_vars(__CLASS__)) as $prop) {
				if(property_exists($obj,$prop))
					$this->$prop = $obj->$prop;
			}
		}
	}
}