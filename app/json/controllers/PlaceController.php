<?php
namespace App\Json\Controllers;
class PlaceController extends \Framework\Controller\BaseController {

	protected function init() {

	}

	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTIONS) {
			$this->app->setHeader("allow",$Request::GET.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}

		elseif($Request->method == $Request::GET) {
			$this->defaultResp($Request);
		}

		else {
			$this->app->setResponse(403);
		}
	}

	protected function defaultResp(\Framework\Model\Inet\Request\Request $Request) {
		$this->app->setData(json_encode(array("class"=>__CLASS__,"timestamp"=>time())));
	}
}
