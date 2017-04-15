<?php
namespace Framework\App\Web;
abstract class BaseWebApp extends \Framework\App\BaseApp {
	abstract public function setResponse(\Framework\Model\Inet\Response\Response $Response);
	abstract public function setResponseCode(int $code);
	abstract public function setHeader(string $property, string $value);
	abstract public function setData(string $data);
	abstract public function getAppUrl(string $path = ""): string;
	abstract public function getPath(string $path = ""):string;
	abstract public function processRequest(\Framework\Model\Inet\Request\Request $Request);
}
