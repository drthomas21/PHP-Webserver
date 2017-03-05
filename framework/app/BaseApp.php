<?php
namespace Framework\App;
abstract class BaseApp {
	var $config;
	var $Memcached;
	var $Database;

	protected $hostname;
	protected $appName;
	protected $Response = null;

	public final function __construct(\stdClass $config = null) {
		if(is_object($config) && $config) {
			$this->config($config);
		}

		$this->init();
	}

	protected function config(\stdClass $config) {
		$this->config = $config;
		$this->hostname = property_exists('hostname',$config) ? $config->hostname : get_class();
		$this->appName = get_class();
	}

	public function init() {
		$this->setResponse(new \Framework\Model\Inet\Response\HttpResponse());
		if(empty($this->hostname)) $this->hostname = get_class();
	}

	abstract public function setResponse(\Framework\Model\Inet\Response\Response $Response);
	abstract public function setResponseCode(int $code);
	abstract public function setHeader(string $property, string $value);
	abstract public function setData(string $data);
	abstract public function getAppName(): string;
	abstract public function processRequest(\Framework\Model\Inet\Request\Request $Request);
	abstract public function getAppUrl(string $path = ""): string;
	abstract public function getPath(string $path = ""):string;
}
