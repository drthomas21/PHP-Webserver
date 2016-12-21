<?php
namespace App\Signz\Controller;
class GangController extends \Framework\Controller\BaseController {
	protected $Gang;
	
	protected function init() {
		$this->Gang = new \App\Signz\Model\GangModel($this->app->Database,$this->app->Memcached);
	}
	
	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTIONS) {
			$this->app->setHeader("allow",$Request::GET.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}
		
		elseif($Request->method == $Request::GET) {
			if(preg_match("/^\/gang\/([a-z0-9\-]+)\/?/",$Request->path,$matches)) {
				if($this->decrypt("", $Request)) {
					$this->getGang($Request,$matches[1]);
				}
			}
			elseif(preg_match("/^\/gangs\/?/",$Request->path)) {
				if($this->decrypt("", $Request)) {
					$this->getGangs($Request);
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
	
	protected function getGang(\Framework\Model\Inet\Request\Request $Request,string $name) {
		$Gang = $this->Gang->getByName($name);
		$this->app->setData(json_encode(array("point"=>$Gang)));
	}
	
	protected function getGangs(\Framework\Model\Inet\Request\Request $Request) {
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
		$offset = 0;
		if(preg_match("/offset=([0-9]+)/",$Request->path,$matches)) {
			$offset = invtal($matches[1]);
		}
		
		error_log("lat:{$latitude} | lng:{$longitude} | dist:{$distance}");
		$gangs = $this->Gang->getByRadius($latitude, $longitude, $distance, $offset);
		$this->app->setData(json_encode(array("points"=>$gangs)));
	}
}