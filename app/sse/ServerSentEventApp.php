<?php
namespace App\SSE;
use Framework\Controller\BaseController;

class ServerSentEventApp extends \Framework\App\Web\BaseServerSentEventApp {
	public function processRequest(\Framework\Model\Inet\Request\Request $Request) {
		parent::processRequest($Request);
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
