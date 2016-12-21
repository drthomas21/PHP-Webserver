<?php
namespace Framework\Driver\Shared;

class MemcachedDriver extends BaseSharedMem {
	private $MemDriver;
	
	protected function __construct() {
		$this->MemDriver = new \Memcached();
	}
	
	public function addServer(string $host, int $port = 11211, int $weight = 0) {
		$this->MemDriver->addServer($host,$port,$weight);
	}
	
	public function get(string $name,float &$token) {
		$ret = $this->MemDriver->get($name,null,$token);
		if(!$ret && $this->MemDriver->getResultCode() == \Memcached::RES_NOTFOUND) {
			throw new \Framework\Exception\DataNotFoundException($name);
		}
		return $ret;
	}
	
	public function set(string $name, &$data, float $token, int $ttl = 0):bool {
		$ret = $this->MemDriver->cas($token,$name,$data,$ttl);
		if(!$ret && $this->MemDriver->getResultCode() == \Memcached::RES_DATA_EXISTS) {
			throw new \Framework\Exception\ExpiredTokenException($name);
		}
		return $ret;
	}
	
	public function delete(string $name):bool {
		return $this->MemDriver->delete($name);
	}
	
	public function config(\stdClass $config) {
		if(property_exists($config, 'servers') && is_array($config->servers) && !empty($config->servers)) {
			foreach($servers as $server) {
				$this->addServer($server->host,property_exists($server,'port') ? $server->port : 11211,property_exists($server,'weight') ? $server->weight : 1);
			}
		}
	}
}