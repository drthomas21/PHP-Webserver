<?php
namespace Framework\Factory\App;
abstract class BaseAppFactory {
	const APPLICATION_NAME = "Prophpet";
	const APPLICATION_VERSION = "0.1a";
	private static $Instances = array();

	public static function getInstance(string $appName, \stdClass $config = null): \Framework\App\BaseApp {
		if(!array_key_exists($appName, self::$Instances)) {
			self::$Instances[$appName] = null;
			if(is_subclass_of($appName, "\\Framework\\App\\BaseApp",true)) {
				self::$Instances[$appName] = new $appName($config);
				self::$Instances[$appName]->init();
				self::$Instances[$appName]->setHeader("Server",self::APPLICATION_NAME . ' '. self::APPLICATION_VERSION);
			}
		}

		if(self::$Instances[$appName] == null) {
			throw new \Framework\Exception\InvalidClassException($appName);
		}
		return self::$Instances[$appName];
	}
}
