<?php
namespace App\Signz\Controller;
class GridController extends \Framework\Controller\BaseController {
	protected $Grid;
	
	protected function init() {
		$this->Grid = new \App\Signz\Model\GridModel($this->app->Database,$this->app->Memcached);
	}
	
	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTIONS) {
			$this->app->setHeader("allow",$Request::GET.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}
		
		elseif($Request->method == $Request::GET) {
			if(preg_match("/^\/grid\/?/",$Request->path)) {
				if($this->decrypt("", $Request)) {
					$this->getGrid($Request);
				}
			}
			else {
				$this->app->setResponse(404);
			}
		}
		
		else {
			$this->app->setResponse(403);
		}
	}
	
	protected function decrypt(string $key, \Framework\Model\Inet\Request\Request $Request): bool {
		return true;
	}
	
	protected function getGrid(\Framework\Model\Inet\Request\Request $Request) {
		$distance = 0;
		if(preg_match("/dist=([0-9\.\-]+)/",$Request->path,$matches)) {
			$distance = floatval($matches[1]);
		}
		$latitude = 0;
		if(preg_match("/lat=([0-9\.\-]+)/",$Request->path,$matches)) {
			$latitude = floatval($matches[1]);
		}
		$longitude = 0;
		if(preg_match("/long=([0-9\.\-]+)/",$Request->path,$matches)) {
			$longitude = floatval($matches[1]);
		}
		
		$grid = $this->Grid->getByRadius($latitude, $longitude, $distance);
		$this->app->setData(json_encode(array("grid"=>$grid)));
	}
}