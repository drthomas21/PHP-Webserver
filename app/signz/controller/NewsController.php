<?php
namespace App\Signz\Controller;
class NewsController extends \Framework\Controller\BaseController {
	protected $News;

	protected function init() {
		$this->News = new \App\Signz\Model\NewsModel($this->app->Database,$this->app->Memcached);
	}

	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTIONS) {
			$this->app->setHeader("allow",$Request::GET.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}

		elseif($Request->method == $Request::GET) {
			if(preg_match("/^\/news\/?/",$Request->path)) {
				if($this->decrypt("", $Request)) {
					$this->getNews($Request);
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

	protected function getNews(\Framework\Model\Inet\Request\Request $Request) {
		$News = $this->News->getAll();
		$this->app->setData(json_encode(array("news"=>$News)));
	}
}
