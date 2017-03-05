<?php
namespace App\SSE;
use Framework\Controller\BaseController;

class ServerSentEventApp extends \Framework\App\BaseServerSentEventApp {
	var $config;

	protected function config(\stdClass $config) {
		$this->config = $config;
	}

	public function processRequest(\Framework\Model\Inet\Request\Request $Request) {
		$this->setResponseCode(200);
		$this->setHeader("content_type","text/event-stream;charset=UTF-8");
		$this->setHeader("connection","Keep-Alive");
		$this->setHeader("Keep-Alive","timeout=5, max=100");
		$this->setHeader("Access-Control-Allow-Origin","*");

        BaseController::processRequest(__NAMESPACE__.'\\Controllers\\Default', $Request,$this);
	}

	public function getAppUrl(string $path = ""): string {
		global $config;
		return "http://".$this->config->hostname.":".$config->port."/".stripcslashes($path);
	}
	public function getPath(string $path = ""):string {
		return __DIR__."/".stripcslashes($path);
	}
}
