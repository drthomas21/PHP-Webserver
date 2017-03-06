<?php
namespace App\Json\Controllers;
class FourOhFourController extends \Framework\Controller\BaseController {

	protected function init() {

	}

	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTIONS) {
			$this->app->setHeader("allow",$Request::GET.",".$Request::POST.",".$Request::PUT.",".$Request::DELETE.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}

		$this->defaultResp($Request);
	}

	protected function defaultResp(\Framework\Model\Inet\Request\Request $Request) {
		$this->app->setData(json_encode(array("class"=>__CLASS__,"message"=>"This is a sample 404")));
	}
}
