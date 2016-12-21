<?php
namespace Framework\Driver\Memcached;
abstract class BaseMemcachedDriver {
	const DRIVER_MEMCACHED = "Memcached";
	const DRIVER_MEMCACHE = "Memcache";
	private static $arrInstances = array();
	
	public static function getInstance(string $driver = self::DRIVER_MEMCACHED): BaseMemcachedDriver {
		$classname = ucfirst($driver)."Driver";
		if(!array_key_exists($driver, self::$arrInstances)) {
			if(!class_exists($classname) || !is_subclass_of($classname, __CLASS__,true)) {
				throw new \Framework\Exception\InvalidClassException($classname);
			}
			self::$arrInstances[$driver] = new $classname();
		} 
		
		return self::$arrInstances[$driver];
	}
	protected function __construct() {
		
	}	
	abstract public function addServer(string $host, int $port = 11211, int $weight = 0);
	abstract public function get(string $name);
	abstract public function set(string $name, &$data, int $ttl = 0): bool;
	abstract public function delete(string $name): bool;
}