<?php
namespace Framework\Driver\Database;
abstract class BaseDatabaseDriver {
	const DRIVER_MYSQL = "Mysql";
	private static $arrInstances = array();
	
	public static function getInstance(string $driver = self::DRIVER_MYSQL,string $key = "default"): BaseDatabaseDriver {
		$classname = ucfirst($driver)."Driver";
		if(!array_key_exists($key, self::$arrInstances)) {
			if(!class_exists($classname) || !is_subclass_of($classname, __CLASS__,true)) {
				throw new \Framework\Exception\InvalidClassException($classname);
			}
			self::$arrInstances[$key] = new $classname();
		}
	
		return self::$arrInstances[$key];
	}
	protected function __construct() {
	
	}
	
	abstract public function connect(string $host, string $schema, string $username, string $password, int $port);
	abstract public function query(string $query): BaseDatabaseQuery;
	abstract public function prepare(string $query, string $type, array $arguments): BaseDatabaseQuery;
	abstract public function isConnected(): bool;
	abstract public function getLastError(): string;
	abstract public function reconnect();
}