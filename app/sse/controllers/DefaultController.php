<?php
namespace App\SSE\Controllers;
class DefaultController extends \Framework\Controller\BaseController {

	protected function init() {

	}

	protected function handleRequest(\Framework\Model\Inet\Request\Request $Request) {
		if($Request->method == $Request::OPTIONS) {
			$this->app->setHeader("allow",$Request::GET.",".$Request::OPTIONS);
			$this->app->setHeader("content_type","httpd/unix-directory");
		}

		elseif($Request->method == $Request::GET) {
            $this->app->sendHeaders();
			while($this->app->isClientConnected()) {
				$resp = new \stdClass();
	            $resp->cpu = $this->getReports();
	            $resp->memory = $this->getSeleniumStatus();

	            $this->app->sendContent($this->buildResponse($resp));
				sleep(2);
			}
		}

		else {
			$this->app->setResponse(403);
		}
	}

	protected function getReports():array {
        return sys_getloadavg();
	}

    protected function getSeleniumStatus():array {
        return array(
            "usage" => memory_get_usage(),
            "allocated" => memory_get_usage(true),
            "peak"=>memory_get_peak_usage()
        );
	}

    protected function buildResponse(\stdClass $data):string {
        $ret = "";
        foreach(get_object_vars($data) as $event => $data) {
            $ret .= "event: {$event}" . PHP_EOL;
			$ret .= "id: " . time() . PHP_EOL;
            $ret .= "data: " . json_encode($data) . PHP_EOL;
			$ret .= "retry: 10".PHP_EOL;
            $ret .= PHP_EOL;
        }

        return $ret;
    }
}
