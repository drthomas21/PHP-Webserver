<?php
namespace Framework\App;
abstract class BaseApp {
	private final function __construct();
	abstract public function setResponse(\Framework\Model\Inet\Response\Response $Response);
	abstract public function setResponseCode(int $code);
	abstract public function setHeader(string $property, string $value);
	abstract public function setData(string $data);
	abstract public function getAppName(): string;
	abstract protected function config(\stdClass $config);
	abstract public function processRequest(\Framework\Model\Inet\Request\Request $Request);
	abstract public function getAppUrl(string $path = ""): string;
	abstract public function getPath(string $path = ""):string;
}
