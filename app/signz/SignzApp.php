<?php
namespace App\Signz;
use App\BaseAppFactory;
use Framework\Driver\Memcached\BaseMemcachedDriver;
use Framework\Driver\Database\BaseDatabaseDriver;
use Framework\Controller\BaseController;

class SignzApp extends \Framework\App\BaseJsonApp {
	var $config;

	protected function config(\stdClass $config) {
		$this->config = $config;
		if(property_exists($config, "memcached")) {
			$this->Memcached = BaseMemcachedDriver::getInstance($config->memcached->driver);
			foreach($config->memcached->servers as $server) {
				$this->Memcached->addServer($server->host,property_exists($server, "port") ? $server->port :11211, property_exists($server, "weight") ? $server->weight :1);
			}
		}

		if(property_exists($config, "database")) {
			$serverConfig = $config->database->server;
			$this->Database = BaseDatabaseDriver::getInstance($config->database->driver,$serverConfig->host.$serverConfig->schema);
			$this->Database->connect($serverConfig->host, $serverConfig->schema, $serverConfig->username, $serverConfig->password,property_exists($serverConfig, "port") ? $serverConfig->port :3306);
		}
	}
	public function processRequest(\Framework\Model\Inet\Request\Request $Request) {
		$this->setResponseCode(200);
		$this->setHeader("content_type","application/json; charset=UTF-8");

		if(preg_match("/^\/map\/?\?(.+)/", $Request->path, $matches)) {
			$this->setData(json_encode($matches));
			BaseController::processRequest(__NAMESPACE__.'\\Controller\\Map', $Request,$this);
		} elseif(preg_match("/^\/gang\/(.+)/", $Request->path, $matches)) {
			$this->setData(json_encode($matches));
			BaseController::processRequest(__NAMESPACE__.'\\Controller\\Gang', $Request,$this);
		} elseif(preg_match("/^\/gangs\/?\?(.+)/", $Request->path, $matches)) {
			$this->setData(json_encode($matches));
			BaseController::processRequest(__NAMESPACE__.'\\Controller\\Gang', $Request,$this);
		} elseif(preg_match("/^\/users(\/?$|\/([A-Za-z0-9\-]+)\/?$|\/([A-Za-z0-9\-]+\/[A-Za-z0-9\-]+\/?$))/", $Request->path, $matches)) {
			$this->setData(json_encode($matches));
			BaseController::processRequest(__NAMESPACE__.'\\Controller\\User', $Request,$this);
		} elseif(preg_match("/^\/auth\/?/", $Request->path, $matches)) {
			$this->setData(json_encode($matches));
			BaseController::processRequest(__NAMESPACE__.'\\Controller\\Auth', $Request,$this);
		} elseif(preg_match("/^\/news\/?\?(.+)/",$Request->path, $matches)) {
			$this->setData(json_encode($matches));
			BaseController::processRequest(__NAMESPACE__.'\\Controller\\News', $Request,$this);
		} elseif(preg_match("/^\/grid\/?\?(.+)/", $Request->path, $matches)) {
			$this->setData(json_encode($matches));
			BaseController::processRequest(__NAMESPACE__.'\\Controller\\Grid', $Request,$this);
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
