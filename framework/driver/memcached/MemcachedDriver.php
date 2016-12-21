<?php
namespace Framework\Driver\Memcached;

class MemcachedDriver extends BaseMemcachedDriver {
	private $MemDriver = null;
	
	protected function __construct() {
		$this->MemDriver = new \Memcached();
	}
	
	public function addServer(string $host, int $port = 11211, int $weight = 0) {
		$this->MemDriver->addServer($host,$port,$weight);
	}
	public function get(string $name) {
		return $this->MemDriver->get($name);
	}
	public function set(string $name, &$data, int $ttl = 0): bool {
		return $this->MemDriver->set($name,$data,$ttl);
	}
	public function delete(string $name): bool {
		return $this->MemDriver->delete($name);
	}
}