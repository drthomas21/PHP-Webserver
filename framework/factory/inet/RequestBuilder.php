<?php
namespace Framework\Factory\Inet;
class RequestBuilder {
	public static function buildRequest(string $ver = "HTTP/1.1"): \Framework\Model\Inet\Request\Request {
		$parts = explode('/',$ver);
		$classname = "\\Framework\\Model\\Inet\\Request\\".ucfirst(strtolower($parts[0]))."Request";
		return new $classname($parts[1]);
	}
}