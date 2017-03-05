<?php
namespace App\Json;
use Framework\Driver\Memcached\BaseMemcachedDriver;
use Framework\Driver\Database\BaseDatabaseDriver;
use Framework\Controller\BaseController;

class JsonApp extends \Framework\App\BaseJsonApp {
	public function processRequest(\Framework\Model\Inet\Request\Request $Request) {
		$this->setResponseCode(200);
		$this->setHeader("content_type","application/json; charset=UTF-8");

		if(preg_match("/^\/users\/?\?(.+)/", $Request->path, $matches)) {
			BaseController::processRequest(__NAMESPACE__.'\\Controllers\\User', $Request,$this);
		} elseif(preg_match("/^\/places\/?\?(.+)/", $Request->path, $matches)) {
			BaseController::processRequest(__NAMESPACE__.'\\Controllers\\Place', $Request,$this);
		} else {
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
