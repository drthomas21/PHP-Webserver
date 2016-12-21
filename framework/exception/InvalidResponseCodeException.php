<?php
namespace Framework\Exception;
class InvalidResponseCodeException extends \InvalidArgumentException {
	function __construct(string $message, int $code = 0, \Throwable $previous = null) {
		parent::__construct("Invalid Response Code: {$message}",$code,$previous);
	}
}