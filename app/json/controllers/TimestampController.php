<?php
namespace App\Json\Controllers;
class TimestampController extends \Framework\Controller\BaseController {

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
		preg_match("/^\/timestamp\/([0-9\A-Z\a-z\:\,\s\-\/]+)\/?/",$Request->path,$matches);
		if(is_array($matches) && !empty($matches[1])) {
			$this->app->setData(json_encode(array("class"=>__CLASS__,"timestamp"=>strtotime($matches[1]),"message"=>"using strtotime({$matches[1]})")));
		} else {
			$this->app->setData(json_encode(array("class"=>__CLASS__,"timestamp"=>time(),"message"=>"Using default argument")));
		}
	}
}
