<?php
namespace Framework\Exception;

class InvalidClassException extends \InvalidArgumentException {
	function __construct(string $classname, int $code = 100, \Throwable $previous = NULL) {
		parent::__construct("Invalid classname [{$classname}] passed",$code,$previous);
	}
}