<?php

class ModuleNotFoundException extends InvalidArgumentException {
	function __construct($message = null, $code = null, $previous = null) {
		parent::__construct($message,$code,$previous);
	}
}