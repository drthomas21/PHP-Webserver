<?php
namespace Framework\Exception;
class QueryExecutionException extends \RuntimeException {
	function __construct(string $message, int $code = 0, \Throwable $prev = null) {
		parent::__construct($message,$code,$prev);
	}
}