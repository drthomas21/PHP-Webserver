<?php
namespace Framework\Exception;

class ExpiredTokenException extends \OutOfBoundsException {
	public function __construct(string $token,int $code=200, \Throwable $previous = NULL) {
		parent::__construct("The token for {$token} is expired",$code,$previous);
	}
}