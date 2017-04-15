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
		$this->hostname = property_exists($config,'hostname') ? $config->hostname : get_class();
		$this->appName = get_class();
	}

	public function init() {
		if(empty($this->hostname)) $this->hostname = get_class();
	}

	abstract public function getAppName(): string;
}
