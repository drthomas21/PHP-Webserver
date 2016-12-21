<?php
namespace Framework\Driver\Shared;

abstract class BaseSharedMem {
	const DRIVER_SEMAPHORE = "Semaphore";
	const DRIVER_MEMCACHED = "Memcached";
	private static $Instance = Null;
	
	public static function getInstance(string $driver = self::DRIVER_SEMAPHORE): BaseMemcachedDriver {		
		if(self::$arrInstance == null) {
			$classname = ucfirst($driver)."Driver";
			if(!class_exists($classname) || !is_subclass_of($classname, __CLASS__,true)) {
				throw new \Framework\Exception\InvalidClassException($classname);
			}
			self::$arrInstance = new $classname();
		}
	
		return self::$arrInstance;
	}
	protected function __construct() {
	
	}
	
	abstract public function get(string $name,float &$token);
	abstract public function set(string $name, &$data, float $token, int $null = 0):bool;	
	abstract public function delete(string $name):bool;
	abstract public function config(\stdClass $config);
}