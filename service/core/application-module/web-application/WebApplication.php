<?php
abstract class WebApplication extends \Core\Application {
	abstract public function doGet(Request $Request,Response $Response);
	abstract public function doPost(Request $Request, Request $Response);
	
}