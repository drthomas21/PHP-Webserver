<?php
namespace Framework\App\Web;
abstract class BaseWebApp extends \Framework\App\BaseApp {
	abstract public function setResponse(\Framework\Model\Inet\Response\Response $Response);
	abstract public function setResponseCode(int $code);
	abstract public function setHeader(string $property, string $value);
	abstract public function setData(string $data);
	abstract public function getAppUrl(string $path = ""): string;
	abstract public function getPath(string $path = ""):string;
	abstract public function processRequest(\Framework\Model\Inet\Request\Request $Request);

	public final function setupGlobals(\Framework\Model\Inet\Request\Request $Request) {
		global $_GET, $_POST, $_PUT, $_DELETE;
		$_GET = $_POST = $_PUT = $_DELETE = array();

		$search = preg_replace("/.+\?(.*)/","\$1",$Request->path);
		if(!empty($search)) {
			parse_str($search, $_GET);
		}

		if($Request->method == $Request::POST) {
			$data = $Request->data;
			if(!empty($data)) {
				parse_str($data, $_POST);
			}
		}
		if($Request->method == $Request::PUT) {
			$data = $Request->data;
			if(!empty($data)) {
				parse_str($data, $_PUT);
			}
		}
		if($Request->method == $Request::DELETE) {
			$data = $Request->data;
			if(!empty($data)) {
				parse_str($data, $_DELETE);
			}
		}
	}
}
