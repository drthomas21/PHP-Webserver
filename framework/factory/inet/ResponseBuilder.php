<?php
namespace Framework\Factory\Inet;
class ResponseBuilder {
	public static function buildResponse(string $ver = "HTTP/1.1"): \Framework\Model\Inet\Response\Response {
		$parts = explode('/',$ver);
		$classname = "\\Framework\\Model\\Inet\\Response\\".ucfirst(strtolower($parts[0]))."Response";
		return new $classname($parts[1]);
	}
}