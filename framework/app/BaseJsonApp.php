<?php
namespace Framework\App;
abstract class BaseJsonApp extends BaseApp {
	private $Response = null;
	private $hostname;
	private $appName;

	var $Memcached;
	var $Database;

	public final function setResponse(\Framework\Model\Inet\Response\Response $Response) {
		$this->Response = $Response;
	}
	public final function setResponseCode(int $code) {
		$this->Response->setStatus($code);
	}
	public final function setHeader(string $property, string $value) {
		if(property_exists($this->Response, $property) && is_string($this->Response->$property)) {
			$this->Response->$property = $value;
		} else if(!property_exists($this->Response, $property)) {
			$this->Response->$property = $value;
		}
	}
	public final function setData(string $data) {
		$this->Response->data = $data;
	}

	public final function getHostname(): string {
		return $this->hostname;
	}

	public final function getAppName(): string {
		return $this->appName;
	}
}
