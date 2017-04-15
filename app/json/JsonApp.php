<?php
namespace App\Json;
use Framework\Driver\Memcached\BaseMemcachedDriver;
use Framework\Driver\Database\BaseDatabaseDriver;
use Framework\Controller\BaseController;

class JsonApp extends \Framework\App\BaseJsonApp {
	public function processRequest(\Framework\Model\Inet\Request\Request $Request) {
		parent::processRequest($Request);

		if(preg_match("/^\/timestamp/", $Request->path, $matches)) {
			BaseController::processRequest(__NAMESPACE__.'\\Controllers\\Timestamp', $Request,$this);
		} elseif(preg_match("/^\/404/", $Request->path, $matches)) {
			BaseController::processRequest(__NAMESPACE__.'\\Controllers\\FourOhFour', $Request,$this);
			$this->setResponseCode(404);
		} elseif(preg_match("/^\/403/", $Request->path, $matches)) {
			BaseController::processRequest(__NAMESPACE__.'\\Controllers\\FourOhThree', $Request,$this);
			$this->setResponseCode(403);
		} else {
			BaseController::processRequest(__NAMESPACE__.'\\Controllers\\FourOhFour', $Request,$this);
			$this->setResponseCode(404);
		}
	}

	public function getAppUrl(string $path = ""): string {
		global $config;
		return "http://".$this->config->hostname.":".$config->port."/".stripcslashes($path);
	}
	public function getPath(string $path = ""):string {
		return __DIR__."/".stripcslashes($path);
	}
}
