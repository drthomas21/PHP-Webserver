<?php
namespace Framework\Exception;
class ServerSocketException extends \RuntimeException {
	public function __construct(string $msg, int $code = 0, \Throwable $prev = null) {
		parent::__construct($msg,$code,$prev);
	}
}