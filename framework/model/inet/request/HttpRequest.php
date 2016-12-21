<?php
namespace Framework\Model\Inet\Request;
class HttpRequest extends Request {
	var $version;
	public function __construct(string $ver = "1.1") {
		$this->version = $ver;
		$this->method = "GET";
		$this->path = "/";
	}
}