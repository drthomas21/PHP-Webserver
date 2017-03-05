<?php
namespace Framework\Controller;

/**
 *
 * @property \Framework\App\BaseApp $app
 */
abstract class BaseController {
	private static $Instances = array();
	protected $app;

	protected static function getInstance(string $classname, \Framework\App\BaseApp $app = null): BaseController {
		$classname = ucfirst($classname)."Controller";
		if(!array_key_exists($classname, self::$Instances)) {
			if(!class_exists($classname) || !is_subclass_of($classname, __CLASS__,true)) {
				throw new \Framework\Exception\InvalidClassException($classname);
			}
			self::$Instances[$classname] = new $classname();
			self::$Instances[$classname]->app = $app;
			self::$Instances[$classname]->init();
		}

		return self::$Instances[$classname];
	}

	public static function processRequest(string $controllerName, \Framework\Model\Inet\Request\Request $Request, \Framework\App\BaseApp $app = null) {
		$Instance = self::getInstance($controllerName,$app);
		$Instance->handleRequest($Request);
	}

	abstract protected function handleRequest(\Framework\Model\Inet\Request\Request $Request);
	abstract protected function init();
}
