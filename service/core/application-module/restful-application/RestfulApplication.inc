<?php
abstract class RestfulApplication extends \Core\Application {
	abstract public function doGet(Request $Request,Response $Response);
	abstract public function doPost(Request $Request, Request $Response);
	abstract public function doPut(Request $Request, Request $Response);
	abstract public function doDelete(Request $Request, Request $Response);
	abstract public function doOption(Request $Request, Request $Response);
}