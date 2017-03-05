<?php
namespace Framework\App;
abstract class BaseApp {
	public final function __construct(\stdClass $config = null) {
		if(is_object($config) && $config) {
			$this->config($config);
		}

	}

	abstract protected function config(\stdClass $config);

	abstract public function setResponse(\Framework\Model\Inet\Response\Response $Response);
	abstract public function setResponseCode(int $code);
	abstract public function setHeader(string $property, string $value);
	abstract public function setData(string $data);
	abstract public function getAppName(): string;
	abstract public function processRequest(\Framework\Model\Inet\Request\Request $Request);
	abstract public function getAppUrl(string $path = ""): string;
	abstract public function getPath(string $path = ""):string;
}
