<?php
namespace Framework\App\Web;
abstract class BaseJsonApp extends BaseWebApp {
	public function init() {
		parent::init();
		//$this->setResponse(new \Framework\Model\Inet\Response\HttpResponse());
	}

	public final function setResponse(\Framework\Model\Inet\Response\Response $Response) {
		$this->Response = $Response;
	}
	public final function setResponseCode(int $code) {
		$this->Response->setStatus($code);
	}
	public function processRequest(\Framework\Model\Inet\Request\Request $Request) {
		$this->setResponseCode(200);
		$this->setHeader("content_type","application/json; charset=UTF-8");
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
