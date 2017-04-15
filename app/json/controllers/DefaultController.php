<?php
namespace App\Json\Controllers;
class DefaultController extends \Framework\Controller\BaseController {

	protected function init() {

	}

	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTIONS) {
			$this->app->setHeader("allow",$Request::GET.",".$Request::POST.",".$Request::PUT.",".$Request::DELETE.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}

		if($Request->method == $Request::GET) {
			$this->getResp($Request);
		}
		elseif($Request->method == $Request::POST) {
			$this->postResp($Request);
		}
		elseif($Request->method == $Request::PUT) {
			$this->putResp($Request);
		}
		elseif($Request->method == $Request::DELETE) {
			$this->deleteResp($Request);
		}
		else {
			$this->defaultResp($Request);
		}
	}

	protected function getResp(\Framework\Model\Inet\Request\Request $Request) {
		global $_GET,$_POST,$_PUT,$_DELETE;
		$this->app->setData(json_encode(array("class"=>__CLASS__,"message"=>"Welcome","method"=>"GET","path"=>$Request->path,"data"=>array("GET"=>$_GET,"POST"=>$_POST,"PUT"=>$_PUT,"DELETE"=>$_DELETE))));
	}

	protected function postResp(\Framework\Model\Inet\Request\Request $Request) {
		$this->app->setData(json_encode(array("class"=>__CLASS__,"message"=>"Welcome","method"=>"POST","path"=>$Request->path,"data"=>array("GET"=>$_GET,"POST"=>$_POST,"PUT"=>$_PUT,"DELETE"=>$_DELETE))));
	}

	protected function putResp(\Framework\Model\Inet\Request\Request $Request) {
		$this->app->setData(json_encode(array("class"=>__CLASS__,"message"=>"Welcome","method"=>"PUT","path"=>$Request->path,"data"=>array("GET"=>$_GET,"POST"=>$_POST,"PUT"=>$_PUT,"DELETE"=>$_DELETE))));
	}

	protected function deleteResp(\Framework\Model\Inet\Request\Request $Request) {
		$this->app->setData(json_encode(array("class"=>__CLASS__,"message"=>"Welcome","method"=>"DELETE","path"=>$Request->path,"data"=>array("GET"=>$_GET,"POST"=>$_POST,"PUT"=>$_PUT,"DELETE"=>$_DELETE))));
	}

	protected function defaultResp(\Framework\Model\Inet\Request\Request $Request) {
		$this->app->setData(json_encode(array("class"=>__CLASS__,"message"=>"Welcome","method"=>$Request->method,"path"=>$Request->path,"data"=>array("GET"=>$_GET,"POST"=>$_POST,"PUT"=>$_PUT,"DELETE"=>$_DELETE))));
	}
}
