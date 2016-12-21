<?php
namespace Framework\Exception;
class NoQueryExecutedException extends \InvalidArgumentException {
	function __construct(int $code = 0, \Throwable $prev = null) {
		parent::__construct("No query selected",$code,$prev);
	}
}