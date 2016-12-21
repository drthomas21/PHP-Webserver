<?php
namespace Framework\Exception;

class DataNotFoundException extends \InvalidArgumentException {
	function __construct(string $varName, int $code = 300, \Throwable $previous = NULL) {
		parent::__construct("Cannot find data for {$varName}",$code,$previous);
	}
}