<?php
namespace Framework\Provider\Routing;
class RoutingProvider {
	private static $apps = array();
	public static function registerApp(\App\BaseAppFactory $App) {
		self::$apps[$App->getHostname()] = $App;
	}
	
	public static function processRequest(\Framework\Model\Inet\Request\Request $Request):\Framework\Model\Inet\Response\Response {
		$type = strtoupper(str_replace("Request","",array_slice(explode('\\',get_class($Request)),-1)[0]));
		$Response = \Framework\Factory\Inet\ResponseBuilder::buildResponse("{$type}/{$Request->version}");
		$requestHost = preg_replace("/\:[0-9]+$/","",$Request->host);
		if(array_key_exists($requestHost,self::$apps)) {
			$App = self::$apps[$requestHost];
			$App->setResponse($Response);
			$App->processRequest($Request);
		} else {
			$Response->setAsNotFound();
		}
		return $Response;
	}
}